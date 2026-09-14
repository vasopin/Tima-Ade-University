<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestAnswer;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\QuizFoundationService;

class StudentTestController extends Controller
{
    public function __construct(private QuizFoundationService $quizzes) {}
    /**
     * Show tests available for the student.
     */
    public function index()
    {
        $user = Auth::user();
        abort_unless($user->isStudent(), 403, 'Student access required.');

        $student = Student::where('user_id', $user->id)->first();
        abort_unless($student, 404, 'Student profile not found.');

        // Get tests for this student's class
        $upcomingTests = Test::where('school_class_id', $student->school_class_id)
            ->where('status', 'published')
            ->with(['subject', 'schoolClass', 'questions'])
            ->orderByDesc('publish_date')
            ->get();

        $testAttempts = TestAttempt::where('student_id', $student->id)
            ->with(['test.subject', 'test.schoolClass'])
            ->orderByDesc('submitted_at')
            ->get();

        return view('student.tests.index', compact('student', 'upcomingTests', 'testAttempts'));
    }

    /**
     * Show the test (with instructions, but not for taking).
     */
    public function show(Test $test)
    {
        $user = Auth::user();
        abort_unless($user->isStudent(), 403, 'Student access required.');

        $student = Student::where('user_id', $user->id)->first();
        abort_unless($student, 404, 'Student profile not found.');

        // Check authorization: student must be enrolled in the course
        abort_unless($student->school_class_id === $test->school_class_id, 403, 'You are not enrolled in this course.');

        // Check publication
        abort_unless($test->status === 'published', 403, 'This test is not available.');

        $test->load(['subject', 'schoolClass', 'questions']);
        $attempt = TestAttempt::where('test_id', $test->id)
            ->where('student_id', $student->id)
            ->latest()
            ->first();

        return view('student.tests.show', compact('test', 'attempt'));
    }

    /**
     * Start/resume a test attempt.
     */
    public function start(Request $request, Test $test)
    {
        $user = Auth::user();
        abort_unless($user->isStudent(), 403, 'Student access required.');

        $student = Student::where('user_id', $user->id)->first();
        abort_unless($student, 404, 'Student profile not found.');

        // Authorization checks
        abort_unless($student->school_class_id === $test->school_class_id, 403, 'You are not enrolled in this course.');
        abort_unless($test->status === 'published', 403, 'This test is not published.');
        abort_unless($test->isAvailable(), 403, 'This test is not currently available.');

        // Check attempt limit
        $previousAttempts = TestAttempt::where('test_id', $test->id)
            ->where('student_id', $student->id)
            ->where('status', '!=', 'in_progress') // Don't count incomplete attempts
            ->count();

        if ($previousAttempts >= $test->attempt_limit) {
            abort(403, 'You have exceeded the maximum number of attempts for this test.');
        }

        // Check for existing in-progress attempt
        $existingAttempt = TestAttempt::where('test_id', $test->id)
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            $attempt = $existingAttempt;
        } else {
            $attempt = DB::transaction(function () use ($test, $student): TestAttempt {
                $lockedTest = Test::whereKey($test->id)->lockForUpdate()->firstOrFail();
                $completed = TestAttempt::where('test_id', $lockedTest->id)
                    ->where('student_id', $student->id)
                    ->where('status', '!=', 'in_progress')
                    ->lockForUpdate()->count();
                abort_unless($completed < $lockedTest->attempt_limit, 403, 'You have exceeded the maximum number of attempts for this test.');
                $attempt = TestAttempt::create([
                    'test_id' => $lockedTest->id, 'student_id' => $student->id,
                    'attempt_number' => $completed + 1, 'started_at' => now(), 'status' => 'in_progress',
                ]);
                $this->quizzes->snapshotAttemptQuestions($attempt);
                return $attempt;
            });
        }

        return redirect()->route('student.tests.attempt', $attempt->id);
    }

    /**
     * Display the test attempt interface for answering questions.
     */
    public function attempt(TestAttempt $attempt)
    {
        $user = Auth::user();
        abort_unless($user->isStudent(), 403, 'Student access required.');

        $student = Student::where('user_id', $user->id)->first();
        abort_unless($student, 404, 'Student profile not found.');

        // Authorization: student can only view their own attempt
        abort_unless($attempt->student_id === $student->id, 403, 'Unauthorized to view this attempt.');

        // Check if test is still available
        $test = $attempt->test;
        abort_unless($test->isAvailable() || $attempt->status === 'in_progress', 403, 'This test is no longer available.');

        // Check if attempt is timed out
        if ($attempt->status === 'in_progress' && $test->duration_minutes && $attempt->isTimedOut()) {
            $this->autoSubmitAttempt($attempt);
            return redirect()->route('student.tests.show', $test->id)
                ->with('info', 'Your test time has expired. Your answers have been automatically submitted.');
        }

        $attempt->load(['test', 'questions', 'answers']);
        $test->setRelation('questions', $attempt->questions);

        return view('student.tests.attempt', compact('attempt', 'test'));
    }

    /**
     * Save an answer to a question.
     */
    public function saveAnswer(Request $request, TestAttempt $attempt)
    {
        $user = Auth::user();
        abort_unless($user->isStudent(), 403, 'Student access required.');

        $student = Student::where('user_id', $user->id)->first();
        abort_unless($student, 404, 'Student profile not found.');
        abort_unless($attempt->student_id === $student->id, 403, 'Unauthorized.');
        abort_unless($attempt->status === 'in_progress', 403, 'Test attempt is not active.');

        $test = $attempt->test;
        abort_unless($test->isAvailable(), 403, 'Test is no longer available.');

        if ($test->duration_minutes && $attempt->isTimedOut()) {
            return response()->json(['error' => 'Test time has expired.'], 403);
        }

        $validated = $request->validate([
            'test_question_id' => 'nullable|exists:test_questions,id',
            'question_id' => 'nullable|exists:test_questions,id',
            'answer_text' => 'nullable|string|max:5000',
            'answer' => 'nullable|string|max:5000',
            'selected_option_id' => 'nullable|exists:test_options,id',
        ]);
        $validated['test_question_id'] ??= $validated['question_id'] ?? null;
        $validated['answer_text'] ??= $validated['answer'] ?? null;
        abort_unless($validated['test_question_id'], 422, 'A question is required.');

        // Verify the question belongs to this test
        $question = $attempt->questions()->where('test_question_id', $validated['test_question_id'])->first();
        abort_unless($question, 422, 'Invalid question for this test.');
        if ($validated['selected_option_id']) {
            abort_unless(collect($question->options)->contains(fn ($option): bool => (int) ($option['id'] ?? 0) === (int) $validated['selected_option_id']), 422, 'Invalid option for this question.');
        }

        // Find or create answer record
        $answer = TestAnswer::firstOrCreate(
            [
                'test_attempt_id' => $attempt->id,
                'test_question_id' => $question->test_question_id,
            ],
            [
                'answer_text' => $validated['answer_text'],
                'selected_option_id' => $validated['selected_option_id'],
            ]
        );

        // Update if already exists
        $answer->update([
            'answer_text' => $validated['answer_text'],
            'selected_option_id' => $validated['selected_option_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Answer saved.',
        ]);
    }

    /**
     * Submit the test attempt.
     */
    public function submit(Request $request, TestAttempt $attempt)
    {
        $user = Auth::user();
        abort_unless($user->isStudent(), 403, 'Student access required.');

        $student = Student::where('user_id', $user->id)->first();
        abort_unless($student, 404, 'Student profile not found.');
        abort_unless($attempt->student_id === $student->id, 403, 'Unauthorized.');
        abort_unless($attempt->status === 'in_progress', 403, 'Test attempt is not active.');

        $this->autoSubmitAttempt($attempt);

        return redirect()->route('student.tests.show', $attempt->test_id)
            ->with('success', 'Your test has been submitted successfully.');
    }

    /**
     * Auto-grade and submit the attempt.
     */
    private function autoSubmitAttempt(TestAttempt $attempt): void
    {
        $attempt->update([
            'submitted_at' => now(),
            'status' => 'submitted',
            'time_spent_minutes' => $attempt->started_at->diffInMinutes(now()),
        ]);

        // Auto-grade automatically gradable questions
        $attempt->loadMissing('questions');
        $snapshots = $attempt->questions->keyBy('test_question_id');
        $answers = $attempt->answers()->with(['question', 'selectedOption'])->get();
        $totalScore = 0;
        $gradedCount = 0;

        foreach ($answers as $answer) {
            $question = $answer->question;
            $snapshot = $snapshots->get($answer->test_question_id);

            if ($question->isAutoGradeable()) {
                $isCorrect = false;

                if ($question->question_type === 'multiple_choice') {
                    $option = collect($snapshot?->options ?? [])->firstWhere('id', $answer->selected_option_id);
                    $isCorrect = $snapshot
                        ? (bool) ($option['is_correct'] ?? false)
                        : (bool) ($answer->selectedOption?->is_correct);
                } elseif ($question->question_type === 'true_false') {
                    // Correct answer stored as 'true' or 'false' string
                    $isCorrect = $answer->selectedOption && strtolower($answer->selectedOption->option_text) === strtolower($snapshot?->correct_answer ?? $question->correct_answer);
                }

                $marksObtained = $isCorrect ? ($snapshot?->marks ?? $question->marks) : 0;
                $answer->update([
                    'is_correct' => $isCorrect,
                    'marks_obtained' => $marksObtained,
                ]);

                $totalScore += $marksObtained;
                $gradedCount++;
            }
        }

        // Update attempt with score (auto-graded questions only)
        if ($gradedCount > 0) {
            $isPassed = $totalScore >= $attempt->test->pass_marks;
            $attempt->update([
                'score' => $totalScore,
                'is_passed' => $isPassed,
                'status' => $attempt->test->questions()->whereNotIn('question_type', ['short_answer'])->count() === $gradedCount 
                    ? 'graded' 
                    : 'submitted',
            ]);
        }
    }

    /**
     * View submitted test results (if released).
     */
    public function results(Test $test)
    {
        $user = Auth::user();
        abort_unless($user->isStudent(), 403, 'Student access required.');

        $student = Student::where('user_id', $user->id)->first();
        abort_unless($student, 404, 'Student profile not found.');
        abort_unless($student->school_class_id === $test->school_class_id, 403, 'Unauthorized.');
        abort_unless($test->canViewResults(), 403, 'Results are not yet available.');

        $attempt = TestAttempt::where('test_id', $test->id)
            ->where('student_id', $student->id)
            ->latest()
            ->firstOrFail();

        $attempt->load(['test', 'answers.question.options', 'answers.selectedOption']);

        return view('student.tests.results', compact('attempt', 'test'));
    }
}
