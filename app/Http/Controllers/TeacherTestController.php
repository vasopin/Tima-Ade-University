<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestAnswer;
use App\Models\Teacher;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherTestController extends Controller
{
    /**
     * View submissions for a test.
     */
    public function submissions(Test $test)
    {
        $user = Auth::user();
        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized.');

        $attempts = TestAttempt::where('test_id', $test->id)
            ->with(['student.user', 'test'])
            ->orderByDesc('submitted_at')
            ->paginate(15);

        return view('teacher.tests.submissions', compact('test', 'attempts'));
    }

    /**
     * Show a single submission for grading.
     */
    public function gradeSubmission(TestAttempt $attempt)
    {
        $user = Auth::user();
        $test = $attempt->test;

        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized.');
        abort_unless($attempt->status === 'submitted', 403, 'Only submitted attempts can be graded.');

        $attempt->load(['student.user', 'test.questions', 'answers.question.options', 'answers.selectedOption']);

        return view('teacher.tests.grade-submission', compact('attempt'));
    }

    /**
     * Save grade for a short-answer question.
     */
    public function saveGrade(Request $request, TestAnswer $answer)
    {
        $user = Auth::user();
        $attempt = $answer->attempt;
        $test = $attempt->test;

        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized.');
        abort_unless($attempt->status === 'submitted', 403, 'Only submitted attempts can be graded.');

        // Short-answer questions are manually graded
        abort_unless($answer->question->question_type === 'short_answer', 422, 'Only short-answer questions need manual grading.');

        $validated = $request->validate([
            'marks_obtained' => 'required|numeric|min:0|max:' . $answer->question->marks,
            'teacher_feedback' => 'nullable|string|max:5000',
        ]);

        $isCorrect = $validated['marks_obtained'] > 0;

        $answer->update([
            'marks_obtained' => $validated['marks_obtained'],
            'is_correct' => $isCorrect,
            'teacher_feedback' => $validated['teacher_feedback'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Grade saved.',
        ]);
    }

    /**
     * Finalize grading and calculate total score.
     */
    public function finalizeGrade(Request $request, TestAttempt $attempt)
    {
        $user = Auth::user();
        $test = $attempt->test;

        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized.');
        abort_unless($attempt->status === 'submitted', 403, 'Only submitted attempts can be graded.');

        // Calculate total score from all answers
        $totalScore = TestAnswer::where('test_attempt_id', $attempt->id)
            ->where('is_correct', '!=', null)
            ->sum('marks_obtained') ?? 0;

        $isPassed = $totalScore >= $test->pass_marks;

        // Determine grade (can be extended with grade scale)
        $percentage = ($totalScore / $test->total_marks) * 100;
        $grade = $this->calculateGrade($percentage);

        $attempt->update([
            'score' => $totalScore,
            'grade' => $grade,
            'is_passed' => $isPassed,
            'status' => 'graded',
            'graded_at' => now(),
        ]);

        return redirect()->route('teacher.tests.submissions', $test->id)
            ->with('success', 'Grades finalized successfully.');
    }

    /**
     * Release results for all students in a test.
     */
    public function releaseResults(Test $test)
    {
        $user = Auth::user();
        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized.');

        $test->update([
            'results_release_date' => now(),
        ]);

        // Update all graded attempts to show results_released status
        TestAttempt::where('test_id', $test->id)
            ->where('status', 'graded')
            ->update(['status' => 'results_released']);

        TestAttempt::where('test_id', $test->id)
            ->where('status', 'results_released')
            ->with('student.user.role')
            ->get()
            ->each(function (TestAttempt $attempt) use ($test): void {
                Notification::create([
                    'user_id' => $attempt->student->user_id,
                    'title' => 'Test Result Available',
                    'body' => 'Your result for ' . $test->title . ' is now available.',
                    'role' => $attempt->student->user?->role?->slug,
                    'read' => false,
                    'meta' => ['test_id' => $test->id, 'attempt_id' => $attempt->id, 'type' => 'test_result_released'],
                ]);
            });

        return redirect()->route('teacher.tests.show', $test->id)
            ->with('success', 'Results released to students.');
    }

    /**
     * Calculate letter grade based on percentage.
     */
    private function calculateGrade(float $percentage): string
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }
}
