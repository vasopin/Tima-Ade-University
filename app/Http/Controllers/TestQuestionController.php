<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\TestOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestQuestionController extends Controller
{
    /**
     * Store a new question for a test.
     */
    public function store(Request $request, Test $test)
    {
        $user = Auth::user();
        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized to manage this test.');
        abort_unless($test->status === 'draft', 403, 'Cannot edit published tests.');

        $validated = $request->validate([
            'question_text' => 'required|string|max:5000',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer',
            'marks' => 'required|integer|min:1|max:1000',
            'order_index' => 'nullable|integer|min:0',
        ]);

        $validated['test_id'] = $test->id;
        $validated['order_index'] = $validated['order_index'] ?? $test->questions()->count();

        $question = TestQuestion::create($validated);

        return response()->json([
            'success' => true,
            'question' => $question->load('options'),
            'message' => 'Question added successfully.',
        ]);
    }

    /**
     * Update a question.
     */
    public function update(Request $request, TestQuestion $question)
    {
        $user = Auth::user();
        $test = $question->test;

        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized to manage this test.');
        abort_unless($test->status === 'draft', 403, 'Cannot edit published tests.');

        $validated = $request->validate([
            'question_text' => 'required|string|max:5000',
            'marks' => 'required|integer|min:1|max:1000',
            'order_index' => 'nullable|integer|min:0',
        ]);

        $question->update($validated);

        return response()->json([
            'success' => true,
            'question' => $question->load('options'),
            'message' => 'Question updated successfully.',
        ]);
    }

    /**
     * Delete a question.
     */
    public function destroy(TestQuestion $question)
    {
        $user = Auth::user();
        $test = $question->test;

        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized to manage this test.');
        abort_unless($test->status === 'draft', 403, 'Cannot edit published tests.');

        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Question deleted successfully.',
        ]);
    }

    /**
     * Store options for MCQ/True-False questions.
     */
    public function storeOption(Request $request, TestQuestion $question)
    {
        $user = Auth::user();
        $test = $question->test;

        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized to manage this test.');
        abort_unless($test->status === 'draft', 403, 'Cannot edit published tests.');
        abort_unless(in_array($question->question_type, ['multiple_choice', 'true_false']), 422, 'This question type does not support options.');

        $validated = $request->validate([
            'option_text' => 'required|string|max:1000',
            'is_correct' => 'boolean',
            'order_index' => 'nullable|integer|min:0',
        ]);

        $validated['test_question_id'] = $question->id;
        $validated['order_index'] = $validated['order_index'] ?? $question->options()->count();

        $option = TestOption::create($validated);

        return response()->json([
            'success' => true,
            'option' => $option,
            'message' => 'Option added successfully.',
        ]);
    }

    /**
     * Update an option.
     */
    public function updateOption(Request $request, TestOption $option)
    {
        $user = Auth::user();
        $question = $option->question;
        $test = $question->test;

        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized to manage this test.');
        abort_unless($test->status === 'draft', 403, 'Cannot edit published tests.');

        $validated = $request->validate([
            'option_text' => 'required|string|max:1000',
            'is_correct' => 'boolean',
        ]);

        $option->update($validated);

        return response()->json([
            'success' => true,
            'option' => $option,
            'message' => 'Option updated successfully.',
        ]);
    }

    /**
     * Delete an option.
     */
    public function destroyOption(TestOption $option)
    {
        $user = Auth::user();
        $question = $option->question;
        $test = $question->test;

        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized to manage this test.');
        abort_unless($test->status === 'draft', 403, 'Cannot edit published tests.');

        $option->delete();

        return response()->json([
            'success' => true,
            'message' => 'Option deleted successfully.',
        ]);
    }
}
