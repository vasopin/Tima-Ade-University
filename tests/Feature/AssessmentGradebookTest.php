<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class AssessmentGradebookTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_assigned_teacher_can_grade_enrolled_students_and_submit_results(): void
    {
        [$teacher, $otherTeacher, $student, $section, $exam] = $this->records();
        $this->actingAs($teacher)->post(route('exams.marks.save', $exam), ['marks' => [$student->id => 82], 'absent' => []])->assertRedirect();
        $this->assertDatabaseHas('exam_marks', ['exam_id' => $exam->id, 'student_id' => $student->id, 'grade' => 'A']);
        $this->actingAs($teacher)->post(route('exams.submit-results', $exam))->assertRedirect();
        $this->assertSame('submitted', $exam->fresh()->workflow_status);
        $this->actingAs($otherTeacher)->post(route('exams.marks.save', $exam), ['marks' => [$student->id => 50]])->assertForbidden();
    }

    public function test_scores_above_the_assessment_maximum_are_rejected(): void
    {
        [$teacher, , $student, , $exam] = $this->records();
        $this->actingAs($teacher)->post(route('exams.marks.save', $exam), ['marks' => [$student->id => 101]])->assertSessionHasErrors('marks.'.$student->id);
    }

    public function test_unpublished_marks_are_hidden_until_approval(): void
    {
        [$teacher, , $student, , $exam] = $this->records();
        $this->actingAs($teacher)->post(route('exams.marks.save', $exam), ['marks' => [$student->id => 82], 'absent' => []]);
        $this->actingAs($student->user)->get('/student/academic-records')->assertOk()->assertViewHas('marks', fn ($marks) => $marks->isEmpty());
        $admin = User::whereHas('role', fn ($query) => $query->where('slug', Role::ADMIN))->firstOrFail();
        $this->actingAs($admin)->post(route('registrar.exam-approvals.approve', $exam))->assertRedirect();
        $this->actingAs($student->user)->get('/student/academic-records')->assertOk()->assertViewHas('marks', fn ($marks) => $marks->contains(fn ($mark) => $mark->grade === 'A'));
    }

    private function records(): array
    {
        $this->seed();
        $teacherRole = Role::where('slug', Role::TEACHER)->firstOrFail();
        $studentRole = Role::where('slug', Role::STUDENT)->firstOrFail();
        $teacher = User::factory()->create(['role_id' => $teacherRole->id]);
        $otherTeacher = User::factory()->create(['role_id' => $teacherRole->id]);
        $class = SchoolClass::create(['name' => 'Assessment class '.uniqid()]);
        $classSection = Section::create(['school_class_id' => $class->id, 'name' => 'A']);
        $studentUser = User::factory()->create(['role_id' => $studentRole->id]);
        $student = Student::create(['user_id' => $studentUser->id, 'roll_number' => uniqid('roll'), 'admission_number' => uniqid('adm'), 'school_class_id' => $class->id, 'section_id' => $classSection->id, 'admission_date' => now(), 'status' => 'active']);
        $year = AcademicYear::create(['name' => uniqid('year'), 'code' => uniqid('Y'), 'starts_on' => now()->startOfYear(), 'ends_on' => now()->endOfYear(), 'is_current' => true]);
        $term = Term::create(['academic_year_id' => $year->id, 'name' => 'Semester', 'code' => uniqid('T'), 'starts_on' => now()->startOfYear(), 'ends_on' => now()->endOfYear(), 'is_current' => true]);
        $course = Subject::create(['name' => uniqid('Course'), 'code' => uniqid('C'), 'is_active' => true]);
        $section = CourseSection::create(['course_id' => $course->id, 'term_id' => $term->id, 'teacher_id' => $teacher->id, 'code' => 'A']);
        Enrollment::create(['student_id' => $student->id, 'course_section_id' => $section->id, 'enrolled_at' => now(), 'status' => 'enrolled']);
        $exam = Exam::create(['name' => 'Final', 'exam_type' => 'final', 'school_class_id' => $class->id, 'subject_id' => $course->id, 'course_section_id' => $section->id, 'term_id' => $term->id, 'academic_year_id' => $year->id, 'exam_date' => now()->toDateString(), 'total_marks' => 100, 'pass_marks' => 40, 'status' => 'scheduled', 'workflow_status' => 'draft']);
        return [$teacher, $otherTeacher, $student, $section, $exam];
    }
}
