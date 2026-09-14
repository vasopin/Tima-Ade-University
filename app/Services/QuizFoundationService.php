<?php

namespace App\Services;

use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\TestQuestionPool;
use App\Models\TestAttempt;
use App\Models\TestAttemptQuestion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class QuizFoundationService
{
    public function availablePoolQuestions(Test $test, User $user, ?string $questionType = null)
    {
        abort_unless($this->teacherOwnsTest($test, $user), 403);

        return TestQuestion::query()
            ->where('teacher_id', $user->id)
            ->whereNull('test_id')
            ->when($questionType, fn ($query) => $query->where('question_type', $questionType))
            ->where(function ($query) use ($test): void {
                $query->whereNull('school_class_id')->orWhere('school_class_id', $test->school_class_id);
            })
            ->where(function ($query) use ($test): void {
                $query->whereNull('subject_id')->orWhere('subject_id', $test->subject_id);
            });
    }

    public function configurePool(Test $test, User $user, array $data): TestQuestionPool
    {
        abort_unless($this->teacherOwnsTest($test, $user), 403);
        abort_unless($test->status === 'draft', 422, 'Published quizzes cannot be structurally changed.');

        $availableQuery = $this->availablePoolQuestions($test, $user, $data['question_type'] ?? null);
        $available = $availableQuery->count();
        $existingCount = $test->questionPools()
            ->when($data['question_type'] ?? null, fn ($query, $type) => $query->where('question_type', $type))
            ->sum('questions_count');
        abort_unless($available >= $existingCount + $data['questions_count'], 422, "The question pools require more than the {$available} available questions.");

        return TestQuestionPool::create([
            ...$data,
            'test_id' => $test->id,
            'teacher_id' => $user->id,
            'school_class_id' => $test->school_class_id,
            'subject_id' => $test->subject_id,
        ]);
    }

    public function deterministicPoolQuestions(TestQuestionPool $pool, int|string $seed = 0, array $excludedIds = [])
    {
        $questions = $this->availablePoolQuestions($pool->test, $pool->teacher, $pool->question_type)
            ->with('options')
            ->when($excludedIds, fn ($query) => $query->whereNotIn('test_questions.id', $excludedIds))
            ->get();

        return $questions
            ->sortBy(fn (TestQuestion $question): string => hash('sha256', "{$seed}:{$pool->id}:{$question->id}"))
            ->take($pool->questions_count)
            ->values();
    }

    public function deletePool(TestQuestionPool $pool, User $user): void
    {
        abort_unless($this->teacherOwnsTest($pool->test, $user), 403);
        abort_unless($pool->test->status === 'draft', 422, 'Published quizzes cannot be structurally changed.');
        $pool->delete();
    }

    public function snapshotAttemptQuestions(TestAttempt $attempt): void
    {
        $test = $attempt->test->load(['questions.options', 'selectedQuestions.options', 'questionPools']);
        $questions = $test->questions->concat($test->selectedQuestions)->unique('id')->values();
        $excluded = $questions->pluck('id')->all();

        foreach ($test->questionPools as $pool) {
            $poolQuestions = $this->deterministicPoolQuestions($pool, $attempt->id, $excluded);
            $questions = $questions->concat($poolQuestions);
            $excluded = array_merge($excluded, $poolQuestions->pluck('id')->all());
        }

        $questions->values()->each(function (TestQuestion $question, int $index) use ($attempt): void {
            $marks = $question->marks;
            $attempt->questions()->create([
                'test_question_id' => $question->id,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'marks' => $marks,
                'order_index' => $index,
                'options' => $question->options->map(fn ($option): array => [
                    'id' => $option->id, 'option_text' => $option->option_text,
                    'is_correct' => (bool) $option->is_correct,
                ])->values()->all(),
                'correct_answer' => $question->correct_answer,
            ]);
        });
    }

    public function teacherOwnsTest(Test $test, User $user): bool
    {
        return $user->isTeacher() && (int) $test->teacher_id === (int) $user->id;
    }

    public function teacherOwnsQuestion(TestQuestion $question, User $user): bool
    {
        return $user->isTeacher()
            && ((int) $question->teacher_id === (int) $user->id
                || ((int) $question->test?->teacher_id === (int) $user->id));
    }

    public function attach(Test $test, TestQuestion $question, User $user, ?int $marks = null): void
    {
        abort_unless($this->teacherOwnsTest($test, $user), 403);
        abort_unless($this->teacherOwnsQuestion($question, $user), 403);
        abort_unless($test->status === 'draft', 422, 'Published quizzes cannot be structurally changed.');
        DB::table('test_question_selections')->updateOrInsert(
            ['test_id' => $test->id, 'test_question_id' => $question->id],
            ['order_index' => (int) DB::table('test_question_selections')->where('test_id', $test->id)->max('order_index') + 1, 'marks' => $marks ?: $question->marks, 'created_at' => now(), 'updated_at' => now()]
        );
    }
}
