<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class EnrollmentRegistrationTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_student_can_register_and_drop_only_their_own_enrollment(): void
    {
        [$student, $section] = $this->records();
        $this->actingAs($student->user)->post(route('student.registration.store'), [
            'student_id' => $student->id,
            'course_section_id' => $section->id,
        ])->assertCreated();
        $enrollment = Enrollment::firstOrFail();
        $this->actingAs($student->user)->post(route('student.enrollments.drop', $enrollment))->assertRedirect();
        $this->assertDatabaseHas('enrollments', ['id' => $enrollment->id, 'status' => 'dropped', 'action_by' => $student->user_id]);
    }

    public function test_student_cannot_register_another_student_or_bypass_capacity(): void
    {
        [$student, $section] = $this->records(0);
        $other = User::factory()->create(['role_id' => Role::where('slug', Role::STUDENT)->firstOrFail()->id]);
        $this->actingAs($other)->post(route('student.registration.store'), ['student_id' => $student->id, 'course_section_id' => $section->id])->assertForbidden();
        $this->actingAs($student->user)->post(route('student.registration.store'), ['student_id' => $student->id, 'course_section_id' => $section->id])->assertSessionHasErrors('course_section_id');
    }

    private function records(?int $capacity = null): array
    {
        $this->seed();
        $role = Role::where('slug', Role::STUDENT)->firstOrFail();
        $class = SchoolClass::create(['name' => 'Registration class '.uniqid()]);
        $classSection = Section::create(['school_class_id' => $class->id, 'name' => 'A']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $student = Student::create(['user_id' => $user->id, 'roll_number' => uniqid('roll'), 'admission_number' => uniqid('adm'), 'school_class_id' => $class->id, 'section_id' => $classSection->id, 'admission_date' => now(), 'status' => 'active']);
        $year = AcademicYear::create(['name' => uniqid('year'), 'code' => uniqid('Y'), 'starts_on' => now()->startOfYear(), 'ends_on' => now()->endOfYear(), 'is_current' => true]);
        $term = Term::create(['academic_year_id' => $year->id, 'name' => 'Semester', 'code' => uniqid('T'), 'starts_on' => now()->startOfYear(), 'ends_on' => now()->endOfYear(), 'is_current' => true]);
        $course = Subject::create(['name' => uniqid('Course'), 'code' => uniqid('C'), 'is_active' => true]);
        $section = CourseSection::create(['course_id' => $course->id, 'term_id' => $term->id, 'code' => 'A', 'capacity' => $capacity]);
        return [$student, $section];
    }
}
