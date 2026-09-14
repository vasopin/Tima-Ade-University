<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestOption;
use App\Models\TestQuestion;
use App\Models\TestQuestionPool;
use App\Models\Subject;
use App\Services\QuizFoundationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizFoundationController extends Controller
{
    public function __construct(private QuizFoundationService $quizzes) {}

    public function bank()
    {
        $user = auth()->user();
        abort_unless($user->isTeacher(), 403);
        $questions = TestQuestion::where('teacher_id', $user->id)->whereNull('test_id')->with(['options', 'subject'])->latest()->paginate(25);
        return view('teacher.question-bank', compact('questions'));
    }

    public function storeQuestion(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->isTeacher(), 403);
        $data = $request->validate([
            'question_text' => ['required', 'string', 'max:5000'],
            'question_type' => ['required', 'in:multiple_choice,true_false,short_answer'],
            'marks' => ['required', 'integer', 'min:1', 'max:1000'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'correct_answer' => ['nullable', 'string', 'max:1000'],
            'options' => ['nullable', 'array', 'min:2', 'max:10'],
            'options.*.option_text' => ['required', 'string', 'max:1000'],
            'options.*.is_correct' => ['boolean'],
        ]);
        if (isset($data['school_class_id']) || isset($data['subject_id'])) {
            abort_unless(
                isset($data['school_class_id'], $data['subject_id'])
                && DB::table('class_subject')
                    ->where('teacher_id', $user->id)
                    ->where('school_class_id', $data['school_class_id'])
                    ->where('subject_id', $data['subject_id'])
                    ->exists(),
                422,
                'You are not authorized for this question scope.'
            );
        }
        $this->validateQuestionOptions($data);
        $question = DB::transaction(function () use ($data, $user): TestQuestion {
            $question = TestQuestion::create([...$data, 'teacher_id' => $user->id, 'test_id' => null, 'order_index' => 0]);
            foreach ($data['options'] ?? [] as $index => $option) {
                $question->options()->create(['option_text' => $option['option_text'], 'is_correct' => (bool) ($option['is_correct'] ?? false), 'order_index' => $index]);
            }
            return $question;
        });
        return back()->with('success', 'Question added to your question bank.');
    }

    public function attach(Request $request, Test $test, TestQuestion $question)
    {
        $data = $request->validate(['marks' => ['nullable', 'integer', 'min:1', 'max:1000']]);
        $this->quizzes->attach($test, $question, auth()->user(), $data['marks'] ?? null);
        return back()->with('success', 'Question added to quiz.');
    }

    public function detach(Test $test, TestQuestion $question)
    {
        abort_unless($this->quizzes->teacherOwnsTest($test, auth()->user()), 403);
        abort_unless($test->status === 'draft', 422);
        DB::table('test_question_selections')->where(['test_id' => $test->id, 'test_question_id' => $question->id])->delete();
        return back()->with('success', 'Question removed from quiz.');
    }

    public function teacherPreview(Test $test)
    {
        abort_unless($this->quizzes->teacherOwnsTest($test, auth()->user()), 403);
        $test->load(['questions.options', 'selectedQuestions.options', 'questionPools', 'schoolClass', 'subject']);
        $excludedIds = [];
        $poolPreviews = $test->questionPools->mapWithKeys(function (TestQuestionPool $pool) use (&$excludedIds): array {
            $questions = $this->quizzes->deterministicPoolQuestions($pool, request()->integer('seed', 0), $excludedIds);
            $excludedIds = array_merge($excludedIds, $questions->modelKeys());
            return [$pool->id => $questions];
        });
        return view('teacher.tests.preview', compact('test', 'poolPreviews'));
    }

    public function storePool(Request $request, Test $test)
    {
        $data = $request->validate([
            'question_type' => ['nullable', 'in:multiple_choice,true_false,short_answer'],
            'questions_count' => ['required', 'integer', 'min:1', 'max:1000'],
            'marks_per_question' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);
        $this->quizzes->configurePool($test, auth()->user(), $data);
        return back()->with('success', 'Question pool added to quiz.');
    }

    public function detachPool(TestQuestionPool $pool)
    {
        $this->quizzes->deletePool($pool, auth()->user());
        return back()->with('success', 'Question pool removed from quiz.');
    }

    public function studentPreview(Test $test)
    {
        $user = auth()->user();
        abort_unless($user->isStudent(), 403);
        $student = $user->student;
        abort_unless($student && (int) $student->school_class_id === (int) $test->school_class_id, 403);
        abort_unless($test->isAvailable(), 403, 'This quiz is not available.');
        $test->load(['questions' => fn ($query) => $query->select('id', 'test_id', 'question_text', 'question_type', 'marks', 'order_index')->with('options:id,test_question_id,option_text,order_index'), 'selectedQuestions' => fn ($query) => $query->select('test_questions.*')->with('options:id,test_question_id,option_text,order_index')]);
        return view('student.tests.preview', compact('test'));
    }

    private function validateQuestionOptions(array $data): void
    {
        $options = collect($data['options'] ?? []);
        if ($data['question_type'] === 'multiple_choice') {
            abort_unless($options->count() >= 2 && $options->contains(fn (array $option): bool => (bool) ($option['is_correct'] ?? false)), 422, 'Multiple-choice questions require options and one correct answer.');
        }
        if ($data['question_type'] === 'true_false') {
            abort_unless($options->count() === 2 && $options->filter(fn (array $option): bool => (bool) ($option['is_correct'] ?? false))->count() === 1, 422, 'True/false questions require exactly two options and one correct answer.');
        }
    }
}
