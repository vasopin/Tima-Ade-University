<?php

namespace App\Http\Controllers;

use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Services\CourseCompletionService;
use Illuminate\Http\Request;

class CourseCompletionController extends Controller
{
    public function __construct(private CourseCompletionService $completion) {}

    public function student(Enrollment $enrollment)
    {
        abort_unless(auth()->user()?->isStudent() && auth()->user()->student?->id === $enrollment->student_id, 403);
        return view('student.course-completion', ['completion' => $this->completion->forEnrollment($enrollment)]);
    }

    public function teacher(Request $request, CourseSection $section)
    {
        $user = auth()->user();
        abort_unless($user?->isTeacher(), 403, 'Teacher access required.');
        abort_unless((int) $section->teacher_id === (int) $user->id, 403);

        $enrollments = $section->enrollments()
            ->whereIn('status', Enrollment::ACTIVE_STATUSES)
            ->with('student.user')
            ->paginate(25)
            ->withQueryString();
        $completions = $enrollments->getCollection()->mapWithKeys(
            fn (Enrollment $enrollment): array => [$enrollment->id => $this->completion->forEnrollment($enrollment)]
        );

        return view('teacher.course-completion', compact('section', 'enrollments', 'completions'));
    }
}
