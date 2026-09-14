<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Collection;

class CourseCompletionService
{
    public function forEnrollment(Enrollment $enrollment): array
    {
        $enrollment->loadMissing(['student.user', 'student.schoolClass', 'courseSection.course', 'courseSection.term.academicYear']);
        $student = $enrollment->student;
        $section = $enrollment->courseSection;

        $base = [
            'enrollment' => $enrollment,
            'student' => $student,
            'section' => $section,
            'assignments' => collect(),
            'activities' => collect(),
            'completed_count' => 0,
            'graded_count' => 0,
            'missing_count' => 0,
            'progress' => 0.0,
            'status' => 'not eligible',
            'final_grade' => $enrollment->final_grade,
        ];

        if (!$student || !$section || !$this->eligibleStudent($student)) {
            return $base;
        }

        if (in_array($enrollment->status, ['withdrawn', 'dropped'], true)) {
            $base['status'] = 'withdrawn';
            return $base;
        }
        if (!$this->eligibleEnrollment($enrollment)) {
            return $base;
        }

        if (strtoupper((string) $enrollment->final_grade) === 'F') {
            $base['status'] = 'failed';
        }

        $assignmentQuery = Assignment::query()
            ->where('school_class_id', $student->school_class_id)
            ->where('subject_id', $section->course_id)
            ->where('status', 'published')
            ->where(fn ($query) => $query->whereNull('available_from')->orWhere('available_from', '<=', now()))
            ->with('rubric');
        $teacher = Teacher::where('user_id', $section->teacher_id)->first();
        if ($teacher) {
            $assignmentQuery->where('teacher_id', $teacher->id);
        }
        $assignments = $assignmentQuery->latest()->get();

        $submissions = AssignmentSubmission::query()
            ->where('student_id', $student->id)
            ->whereIn('assignment_id', $assignments->modelKeys())
            ->with(['rubricScores', 'grader'])
            ->get()
            ->groupBy('assignment_id');

        $activities = $assignments->map(function (Assignment $assignment) use ($submissions): array {
            $attempts = $submissions->get($assignment->id, collect());
            $submission = $this->authoritativeSubmission($attempts);
            $isCompleted = $submission?->status === 'graded';
            return [
                'assignment' => $assignment,
                'submission' => $submission,
                'state' => $submission?->status ?? 'missing',
                'completed' => $isCompleted,
                'percentage' => $isCompleted && (float) $assignment->max_points > 0
                    ? round(((float) $submission->score / (float) $assignment->max_points) * 100, 1)
                    : null,
            ];
        });

        $completed = $activities->where('completed', true)->count();
        $total = $activities->count();
        $status = $base['status'];
        if ($status !== 'failed') {
            $status = $total === 0 ? 'not started' : ($completed === $total ? 'completed' : 'in progress');
            if ($enrollment->status === 'completed') {
                $status = 'completed';
            }
        }

        return [
            ...$base,
            'assignments' => $assignments,
            'activities' => $activities,
            'completed_count' => $completed,
            'graded_count' => $completed,
            'missing_count' => $activities->where('state', 'missing')->count(),
            'progress' => $total > 0 ? round(($completed / $total) * 100, 1) : 0.0,
            'status' => $status,
        ];
    }

    private function authoritativeSubmission(Collection $attempts): ?AssignmentSubmission
    {
        return $attempts->where('status', 'graded')
            ->sortByDesc(fn (AssignmentSubmission $submission) => $submission->graded_at?->timestamp ?? 0)
            ->first()
            ?? $attempts->where('status', 'submitted')->sortByDesc('attempt_number')->first()
            ?? $attempts->where('status', 'draft')->sortByDesc('attempt_number')->first();
    }

    private function eligibleStudent(?Student $student): bool
    {
        return $student && !in_array($student->status, ['inactive', 'expelled'], true)
            && !in_array($student->lifecycle_status, ['applicant', 'withdrawn', 'dismissed', 'graduated'], true);
    }

    private function eligibleEnrollment(Enrollment $enrollment): bool
    {
        return in_array($enrollment->status, Enrollment::ACTIVE_STATUSES, true);
    }
}
