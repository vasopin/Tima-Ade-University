<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\TestOption;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    /**
     * Display a listing of tests for the authenticated teacher.
     */
    public function index()
    {
        $user = Auth::user();
        abort_unless($user->isTeacher(), 403, 'Teacher access required.');

        $teacher = Teacher::where('user_id', $user->id)->first();
        abort_unless($teacher, 404, 'Teacher profile not found.');

        // Get tests created by this teacher
        $myTests = Test::where('teacher_id', $user->id)
            ->with(['schoolClass', 'subject', 'questions', 'attempts'])
            ->orderByDesc('created_at')
            ->get();

        return view('teacher.tests.index', compact('myTests'));
    }

    /**
     * Show the form for creating a new test.
     */
    public function create()
    {
        $user = Auth::user();
        abort_unless($user->isTeacher(), 403, 'Teacher access required.');

        $teacher = Teacher::where('user_id', $user->id)->first();
        abort_unless($teacher, 404, 'Teacher profile not found.');

        // Get authorized classes (via class_subject pivot)
        $authorizedClassIds = DB::table('class_subject')
            ->where('teacher_id', $user->id)
            ->pluck('school_class_id')
            ->unique()
            ->toArray();

        $authorizedClasses = SchoolClass::whereIn('id', $authorizedClassIds)
            ->with('subjects')
            ->get();

        $authorizedSubjectIds = DB::table('class_subject')
            ->where('teacher_id', $user->id)
            ->pluck('subject_id')
            ->unique();
        $allSubjects = Subject::whereIn('id', $authorizedSubjectIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('teacher.tests.create', compact('authorizedClasses', 'allSubjects'));
    }

    /**
     * Store a newly created test in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isTeacher(), 403, 'Teacher access required.');

        $teacher = Teacher::where('user_id', $user->id)->first();
        abort_unless($teacher, 404, 'Teacher profile not found.');

        // Validate authorization for the selected course
        $authorizedClassIds = DB::table('class_subject')
            ->where('teacher_id', $user->id)
            ->pluck('school_class_id')
            ->toArray();

        $validated = $request->validate([
            'school_class_id' => ['required', 'integer', function ($attribute, $value, $fail) use ($authorizedClassIds) {
                if (!in_array($value, $authorizedClassIds)) {
                    $fail('You are not authorized to create tests for this course.');
                }
            }],
            'subject_id' => 'required|integer|exists:subjects,id',
            'title' => 'required|string|max:255',
            'instructions' => 'nullable|string|max:5000',
            'total_marks' => 'required|integer|min:1|max:10000',
            'pass_marks' => 'required|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:1|max:480',
            'attempt_limit' => 'required|integer|min:1|max:10',
        ]);

        // Verify subject belongs to the selected class
        $subjectExists = DB::table('class_subject')
            ->where('school_class_id', $validated['school_class_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('teacher_id', $user->id)
            ->exists();

        abort_unless($subjectExists, 422, 'The selected subject does not belong to this class.');

        $test = Test::create([
            'teacher_id' => $user->id,
            'school_class_id' => $validated['school_class_id'],
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'instructions' => $validated['instructions'],
            'total_marks' => $validated['total_marks'],
            'pass_marks' => $validated['pass_marks'],
            'duration_minutes' => $validated['duration_minutes'],
            'attempt_limit' => $validated['attempt_limit'],
            'status' => 'draft',
        ]);

        return redirect()->route('teacher.tests.edit', $test->id)
            ->with('success', 'Test created successfully. Add questions now.');
    }

    /**
     * Show the form for editing the specified test (add/edit questions).
     */
    public function edit(Test $test)
    {
        $user = Auth::user();
        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized to edit this test.');
        abort_unless($test->status === 'draft', 403, 'Cannot edit published tests.');

        $test->load(['questions.options', 'selectedQuestions.options', 'schoolClass', 'subject']);

        return view('teacher.tests.edit', compact('test'));
    }

    /**
     * Update the test metadata.
     */
    public function update(Request $request, Test $test)
    {
        $user = Auth::user();
        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized to update this test.');
        abort_unless($test->status === 'draft', 403, 'Cannot edit published tests.');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'instructions' => 'nullable|string|max:5000',
            'total_marks' => 'required|integer|min:1|max:10000',
            'pass_marks' => 'required|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:1|max:480',
            'attempt_limit' => 'required|integer|min:1|max:10',
        ]);

        $test->update($validated);

        return redirect()->route('teacher.tests.edit', $test->id)
            ->with('success', 'Test updated successfully.');
    }

    /**
     * Show the test details (for teacher review before publishing).
     */
    public function show(Test $test)
    {
        $user = Auth::user();
        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized to view this test.');

        $test->load(['questions.options', 'selectedQuestions.options', 'schoolClass', 'subject', 'attempts']);

        return view('teacher.tests.show', compact('test'));
    }

    /**
     * Publish the test (make it available to students).
     */
    public function publish(Request $request, Test $test)
    {
        $user = Auth::user();
        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized to publish this test.');
        abort_unless($test->status === 'draft', 403, 'Test is already published or closed.');

        // Validate that test has questions
        abort_unless(
            $test->questions->count() + $test->selectedQuestions->count() + $test->questionPools->sum('questions_count') > 0,
            422,
            'Cannot publish a test without questions.'
        );

        $validated = $request->validate([
            'publish_date' => 'required|date',
            'close_date' => 'nullable|date|after:publish_date',
            'results_release_date' => 'nullable|date',
        ]);

        $test->update([
            'status' => 'published',
            'publish_date' => $validated['publish_date'],
            'close_date' => $validated['close_date'] ?? null,
            'results_release_date' => $validated['results_release_date'] ?? null,
        ]);

        Student::where('school_class_id', $test->school_class_id)
            ->with('user.role')
            ->get()
            ->each(function (Student $student) use ($test): void {
                Notification::create([
                    'user_id' => $student->user_id,
                    'title' => 'New Test Available',
                    'body' => $test->title . ' is now available.',
                    'role' => $student->user?->role?->slug,
                    'read' => false,
                    'meta' => ['test_id' => $test->id, 'type' => 'test_published'],
                ]);
            });

        return redirect()->route('teacher.tests.show', $test->id)
            ->with('success', 'Test published successfully!');
    }

    /**
     * Close a test (prevent further attempts).
     */
    public function close(Test $test)
    {
        $user = Auth::user();
        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized to close this test.');
        abort_unless(in_array($test->status, ['published']), 403, 'Only published tests can be closed.');

        $test->update(['status' => 'closed']);

        return redirect()->route('teacher.tests.show', $test->id)
            ->with('success', 'Test closed successfully.');
    }

    /**
     * Delete the test (only if draft).
     */
    public function destroy(Test $test)
    {
        $user = Auth::user();
        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        abort_unless($test->teacher_id === $user->id, 403, 'Unauthorized to delete this test.');
        abort_unless($test->status === 'draft', 403, 'Cannot delete published tests.');

        $test->delete();

        return redirect()->route('teacher.tests.index')
            ->with('success', 'Test deleted successfully.');
    }
}
