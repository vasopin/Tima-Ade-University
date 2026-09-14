<?php

namespace App\Services;

use App\Models\CoursePrerequisite;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Term;
use App\Models\Timetable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrollmentRegistrationService
{
    public function register(Student $student, CourseSection $section, ?int $actorId = null, bool $override = false): Enrollment
    {
        return DB::transaction(function () use ($student, $section, $actorId, $override): Enrollment {
            $section = CourseSection::with(['course.prerequisites', 'term'])->lockForUpdate()->findOrFail($section->id);
            $this->assertEligible($student, $section, $override);

            return Enrollment::create([
                'student_id' => $student->id,
                'course_section_id' => $section->id,
                'enrolled_at' => now(),
                'status' => 'enrolled',
                'action_by' => $actorId,
            ]);
        });
    }

    public function changeStatus(Enrollment $enrollment, string $status, ?int $actorId = null, ?string $reason = null): Enrollment
    {
        if (!in_array($status, Enrollment::STATUSES, true)) {
            throw ValidationException::withMessages(['status' => 'Invalid enrollment status.']);
        }
        if (in_array($status, ['dropped', 'withdrawn'], true) && !$this->registrationOpen($enrollment->courseSection->term)) {
            throw ValidationException::withMessages(['status' => 'The registration period is closed.']);
        }

        $enrollment->update([
            'status' => $status,
            'status_changed_at' => now(),
            'action_by' => $actorId,
            'action_reason' => $reason,
        ]);

        return $enrollment->refresh();
    }

    public function assertEligible(Student $student, CourseSection $section, bool $override = false): void
    {
        if (!$student->exists || $student->status !== 'active') {
            throw ValidationException::withMessages(['student' => 'Only active students may register.']);
        }
        if (in_array($student->lifecycle_status, ['leave', 'suspended', 'withdrawn', 'dismissed', 'graduated'], true)) {
            throw ValidationException::withMessages(['student' => 'This student is not eligible to register in the current lifecycle status.']);
        }
        if ($student->holds()->where('status', 'active')->whereIn('type', ['registration', 'academic', 'disciplinary', 'administrative'])->exists()) {
            throw ValidationException::withMessages(['student' => 'An active hold prevents registration.']);
        }
        if (!$section->course || !$section->term || $section->status !== 'open') {
            throw ValidationException::withMessages(['course_section_id' => 'This course section is not open.']);
        }
        if (!$override && !$this->registrationOpen($section->term)) {
            throw ValidationException::withMessages(['registration' => 'Registration is not currently open.']);
        }
        if (Enrollment::where('student_id', $student->id)->where('course_section_id', $section->id)->whereIn('status', Enrollment::ACTIVE_STATUSES)->exists()) {
            throw ValidationException::withMessages(['course_section_id' => 'You are already enrolled in this section.']);
        }
        if (!$override && $section->capacity !== null) {
            $count = Enrollment::where('course_section_id', $section->id)->whereIn('status', Enrollment::ACTIVE_STATUSES)->lockForUpdate()->count();
            if ($count >= $section->capacity) {
                throw ValidationException::withMessages(['course_section_id' => 'This section is full.']);
            }
        }
        if (!$override && !$this->prerequisitesSatisfied($student, $section)) {
            throw ValidationException::withMessages(['course_section_id' => 'Required prerequisites have not been completed.']);
        }
        if (!$override && $this->hasTimetableConflict($student, $section)) {
            throw ValidationException::withMessages(['course_section_id' => 'This section conflicts with your current timetable.']);
        }
    }

    public function registrationOpen(Term $term): bool
    {
        if ($term->status === 'closed') {
            return false;
        }
        if (!$term->registration_starts_on || !$term->registration_ends_on) {
            return true;
        }
        return now()->betweenIncluded($term->registration_starts_on->startOfDay(), $term->registration_ends_on->endOfDay());
    }

    public function prerequisitesSatisfied(Student $student, CourseSection $section): bool
    {
        $completed = Enrollment::where('student_id', $student->id)
            ->where('status', 'completed')
            ->whereNotNull('final_grade')
            ->get(['course_section_id', 'final_grade']);
        $completedCourseIds = CourseSection::whereIn('id', $completed->pluck('course_section_id'))
            ->pluck('course_id');

        return $section->course->prerequisites->every(function (CoursePrerequisite $prerequisite) use ($completed, $completedCourseIds): bool {
            if (!$completedCourseIds->contains($prerequisite->prerequisite_course_id)) {
                return false;
            }
            if (!$prerequisite->minimum_grade) {
                return true;
            }
            return $completed->contains(fn ($enrollment) => $enrollment->final_grade === $prerequisite->minimum_grade);
        });
    }

    private function hasTimetableConflict(Student $student, CourseSection $section): bool
    {
        $newSlots = Timetable::where('subject_id', $section->course_id)->where('is_active', true)->get();
        if ($newSlots->isEmpty()) {
            return false;
        }
        $currentCourseIds = CourseSection::whereIn('id', Enrollment::where('student_id', $student->id)
            ->whereIn('status', Enrollment::ACTIVE_STATUSES)->pluck('course_section_id'))->pluck('course_id');
        $currentSlots = Timetable::whereIn('subject_id', $currentCourseIds)->where('is_active', true)->get();

        return $newSlots->contains(fn ($new) => $currentSlots->contains(fn ($current) =>
            $new->day_of_week === $current->day_of_week
            && $new->start_time < $current->end_time
            && $new->end_time > $current->start_time
        ));
    }
}
