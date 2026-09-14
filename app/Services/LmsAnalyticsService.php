<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use Illuminate\Support\Collection;

class LmsAnalyticsService
{
    public function student(Student $student): array
    {
        $enrollments = Enrollment::with(['courseSection.course', 'courseSection.term'])
            ->where('student_id', $student->id)->get();
        $assignmentIds = Assignment::where('school_class_id', $student->school_class_id)->pluck('id');
        $submissions = AssignmentSubmission::where('student_id', $student->id)->get();
        $attempts = TestAttempt::with('test.subject')->where('student_id', $student->id)->get();
        $completion = app(CourseCompletionService::class);

        return [
            'student' => $student,
            'enrollments' => $enrollments,
            'assignments' => [
                'total' => $assignmentIds->count(),
                'submitted' => $submissions->whereIn('assignment_id', $assignmentIds)->whereIn('status', ['submitted', 'graded'])->count(),
                'graded' => $submissions->whereIn('assignment_id', $assignmentIds)->where('status', 'graded')->count(),
                'late' => $submissions->whereIn('assignment_id', $assignmentIds)->where('is_late', true)->count(),
                'missing' => max(0, $assignmentIds->count() - $submissions->whereIn('assignment_id', $assignmentIds)->whereIn('status', ['submitted', 'graded'])->pluck('assignment_id')->unique()->count()),
            ],
            'quizzes' => [
                'attempts' => $attempts->count(),
                'graded' => $attempts->whereIn('status', ['graded', 'results_released'])->count(),
                'average' => round((float) ($attempts->whereNotNull('score')->avg('score') ?? 0), 2),
            ],
            'courses' => $enrollments->map(fn ($enrollment) => $completion->forEnrollment($enrollment))->values(),
        ];
    }

    public function teacher(User $teacher): array
    {
        $sections = \App\Models\CourseSection::with('course')
            ->where('teacher_id', $teacher->id)->get();
        $scope = $sections->map(fn ($section) => [$section->course_id, $section->id]);
        $enrollments = Enrollment::with('student')->whereIn('course_section_id', $sections->modelKeys())->get();
        $domainTeacher = \App\Models\Teacher::where('user_id', $teacher->id)->first();
        $assignments = $domainTeacher
            ? Assignment::where('teacher_id', $domainTeacher->id)->withCount('submissions')->get()
            : collect();
        $assignmentIds = $assignments->modelKeys();
        $submissions = AssignmentSubmission::whereIn('assignment_id', $assignmentIds)->get();
        $attempts = TestAttempt::whereIn('test_id', Test::where('teacher_id', $teacher->id)->pluck('id'))->get();

        return [
            'sections' => $sections,
            'students' => $enrollments->whereIn('status', Enrollment::ACTIVE_STATUSES)->pluck('student_id')->unique()->count(),
            'enrollments' => $enrollments->count(),
            'assignments' => [
                'total' => $assignments->count(),
                'submitted' => $submissions->whereIn('status', ['submitted', 'graded'])->count(),
                'graded' => $submissions->where('status', 'graded')->count(),
                'late' => $submissions->where('is_late', true)->count(),
                'missing' => max(0, $assignments->sum('submissions_count') === 0 ? 0 : $enrollments->count() * $assignments->count() - $submissions->pluck('student_id')->count()),
            ],
            'quizzes' => [
                'attempts' => $attempts->count(),
                'graded' => $attempts->whereIn('status', ['graded', 'results_released'])->count(),
                'average' => round((float) ($attempts->whereNotNull('score')->avg('score') ?? 0), 2),
            ],
        ];
    }
}
