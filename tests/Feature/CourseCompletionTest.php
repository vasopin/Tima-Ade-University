<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Term;
use App\Models\User;
use App\Services\CourseCompletionService;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class CourseCompletionTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_completion_uses_graded_attempts_and_excludes_drafts(): void
    {
        [$student, $teacher, $section, $assignment] = $this->courseFixture();
        $enrollment = Enrollment::create(['student_id' => $student->id, 'course_section_id' => $section->id, 'enrolled_at' => now(), 'status' => 'enrolled']);
        AssignmentSubmission::create(['assignment_id' => $assignment->id, 'student_id' => $student->id, 'attempt_number' => 1, 'status' => 'graded', 'score' => 4, 'submitted_at' => now()->subDay(), 'graded_at' => now()->subDay()]);
        AssignmentSubmission::create(['assignment_id' => $assignment->id, 'student_id' => $student->id, 'attempt_number' => 2, 'status' => 'draft']);

        $result = app(CourseCompletionService::class)->forEnrollment($enrollment);
        $this->assertSame('completed', $result['status']);
        $this->assertSame(100.0, $result['progress']);
        $this->assertSame(4.0, (float) $result['activities']->first()['submission']->score);
    }

    public function test_missing_and_withdrawn_courses_are_not_marked_completed(): void
    {
        [$student, $teacher, $section, $assignment] = $this->courseFixture();
        $enrollment = Enrollment::create(['student_id' => $student->id, 'course_section_id' => $section->id, 'enrolled_at' => now(), 'status' => 'enrolled']);
        $result = app(CourseCompletionService::class)->forEnrollment($enrollment);
        $this->assertSame('in progress', $result['status']);
        $this->assertSame(1, $result['missing_count']);

        $enrollment->update(['status' => 'withdrawn']);
        $this->assertSame('withdrawn', app(CourseCompletionService::class)->forEnrollment($enrollment)['status']);
    }

    public function test_student_and_teacher_completion_views_are_scoped(): void
    {
        [$student, $teacher, $section, $assignment] = $this->courseFixture();
        $enrollment = Enrollment::create(['student_id' => $student->id, 'course_section_id' => $section->id, 'enrolled_at' => now(), 'status' => 'enrolled']);
        $otherStudentUser = User::factory()->create(['role_id' => Role::where('slug', Role::STUDENT)->firstOrFail()->id]);
        $otherStudent = Student::create(['user_id' => $otherStudentUser->id, 'roll_number' => 'CC-OTHER', 'admission_number' => 'CC-OTHER', 'school_class_id' => $student->school_class_id, 'section_id' => $student->section_id, 'admission_date' => now(), 'status' => 'active']);
        $otherEnrollment = Enrollment::create(['student_id' => $otherStudent->id, 'course_section_id' => $section->id, 'enrolled_at' => now(), 'status' => 'enrolled']);

        $this->actingAs($student->user)->get(route('student.enrollment.completion', $enrollment))->assertOk();
        $this->actingAs($student->user)->get(route('student.enrollment.completion', $otherEnrollment))->assertForbidden();
        $this->actingAs($teacher->user)->get(route('teacher.course-section.completion', $section))->assertOk()->assertSee($student->user->name);
        $otherTeacherUser = User::factory()->create(['role_id' => Role::where('slug', Role::TEACHER)->firstOrFail()->id]);
        $this->actingAs($otherTeacherUser)->get(route('teacher.course-section.completion', $section))->assertForbidden();
    }

    private function courseFixture(): array
    {
        $this->seed();
        $teacherRole = Role::where('slug', Role::TEACHER)->firstOrFail();
        $studentRole = Role::where('slug', Role::STUDENT)->firstOrFail();
        $year = AcademicYear::create(['name' => '2026/27', 'code' => 'CC-26', 'starts_on' => '2026-09-01', 'ends_on' => '2027-07-31', 'status' => 'open']);
        $term = Term::create(['academic_year_id' => $year->id, 'name' => 'First Term', 'code' => 'CC-T1', 'starts_on' => '2026-09-01', 'ends_on' => '2026-12-20', 'status' => 'open']);
        $class = SchoolClass::create(['name' => 'Completion Class']);
        $section = Section::create(['school_class_id' => $class->id, 'name' => 'A']);
        $subject = Subject::create(['name' => 'Completion Course', 'code' => 'CC-1']);
        $teacherUser = User::factory()->create(['role_id' => $teacherRole->id]);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'employee_id' => 'CC-T']);
        $studentUser = User::factory()->create(['role_id' => $studentRole->id]);
        $student = Student::create(['user_id' => $studentUser->id, 'roll_number' => 'CC-S', 'admission_number' => 'CC-A', 'school_class_id' => $class->id, 'section_id' => $section->id, 'admission_date' => now(), 'status' => 'active']);
        $courseSection = CourseSection::create(['course_id' => $subject->id, 'term_id' => $term->id, 'teacher_id' => $teacherUser->id, 'code' => 'A', 'status' => 'open']);
        $assignment = Assignment::create(['teacher_id' => $teacher->id, 'school_class_id' => $class->id, 'subject_id' => $subject->id, 'title' => 'Completion assignment', 'status' => 'published', 'max_points' => 10]);
        return [$student, $teacher, $courseSection, $assignment];
    }
}
