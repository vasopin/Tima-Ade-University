<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\CoursePrerequisite;
use App\Models\CourseSection;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\QueryException;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class AcademicFoundationTest extends TestCase
{
    use ForceRefreshDatabase;

    private function admin(): User
    {
        $this->seed();
        return User::where('email', 'admin@school.com')->firstOrFail();
    }

    public function test_authorized_administrator_can_create_the_academic_hierarchy(): void
    {
        $admin = $this->admin();
        $campus = Campus::create(['name' => 'Main Campus', 'code' => 'MAIN']);
        $faculty = Faculty::create(['campus_id' => $campus->id, 'name' => 'Science', 'code' => 'SCI']);
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Computing', 'code' => 'CSC']);
        $program = Program::create(['department_id' => $department->id, 'name' => 'Computer Science', 'code' => 'BSC-CS', 'degree_type' => 'Bachelor']);

        $this->actingAs($admin)->get('/admin/academics')->assertOk()->assertSee('Academic Foundation');
        $this->assertSame($campus->id, $faculty->campus->id);
        $this->assertSame($faculty->id, $department->faculty->id);
        $this->assertSame($department->id, $program->department->id);
    }

    public function test_authorized_administrator_can_configure_program_visibility_for_apply_now(): void
    {
        $admin = $this->admin();
        $faculty = Faculty::create(['name' => 'Engineering', 'code' => 'ENG']);
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Civil Engineering', 'code' => 'CVE']);

        $this->actingAs($admin)->post(route('admin.academics.programs.store'), [
            'department_id' => $department->id,
            'name' => 'B.Eng. Civil Engineering',
            'code' => 'BENG-CVE',
            'degree_type' => 'Bachelor',
            'is_active' => false,
        ])->assertRedirect();

        $program = Program::where('code', 'BENG-CVE')->firstOrFail();
        $this->get(route('public.apply'))->assertDontSee($program->name);

        $this->actingAs($admin)->put(route('admin.academics.programs.update', $program), [
            'department_id' => $department->id,
            'name' => $program->name,
            'code' => $program->code,
            'degree_type' => $program->degree_type,
            'is_active' => true,
        ])->assertRedirect();

        $this->get(route('public.apply'))
            ->assertOk()
            ->assertSee($program->name)
            ->assertSee($program->code);
        $this->assertTrue($program->fresh()->is_active);
    }

    public function test_students_cannot_manage_academic_structure(): void
    {
        $this->seed();
        $student = User::whereHas('role', fn ($query) => $query->where('slug', Role::STUDENT))->firstOrFail();
        $this->actingAs($student)->get('/admin/academics')->assertForbidden();
        $this->actingAs($student)->postJson('/admin/academics/campuses', ['name' => 'Blocked', 'code' => 'BLOCK'])->assertForbidden();
    }

    public function test_course_catalogue_prerequisites_and_sections_are_related(): void
    {
        $this->seed();
        $campus = Campus::create(['name' => 'Main Campus', 'code' => 'MAIN']);
        $faculty = Faculty::create(['campus_id' => $campus->id, 'name' => 'Science', 'code' => 'SCI']);
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Computing', 'code' => 'CSC']);
        $course = Subject::create(['department_id' => $department->id, 'name' => 'Algorithms', 'code' => 'CSC201', 'course_type' => 'core']);
        $prerequisite = Subject::create(['department_id' => $department->id, 'name' => 'Programming', 'code' => 'CSC101', 'course_type' => 'core']);
        CoursePrerequisite::create(['course_id' => $course->id, 'prerequisite_course_id' => $prerequisite->id]);
        $year = AcademicYear::create(['name' => '2026/2027', 'code' => '2026-27', 'starts_on' => '2026-09-01', 'ends_on' => '2027-07-31']);
        $term = Term::create(['academic_year_id' => $year->id, 'name' => 'Semester 1', 'code' => 'S1', 'starts_on' => '2026-09-01', 'ends_on' => '2027-01-31', 'status' => 'open']);
        $section = CourseSection::create(['course_id' => $course->id, 'term_id' => $term->id, 'code' => 'A', 'status' => 'open']);

        $this->assertTrue($course->prerequisites()->where('prerequisite_course_id', $prerequisite->id)->exists());
        $this->assertSame($term->id, $section->term->id);
        $this->assertSame($course->id, $section->course->id);
    }

    public function test_current_year_and_invalid_prerequisite_rules_are_enforced(): void
    {
        $this->seed();
        $first = AcademicYear::create(['name' => '2026/2027', 'code' => '2026-27', 'starts_on' => '2026-09-01', 'ends_on' => '2027-07-31', 'is_current' => true]);
        $second = AcademicYear::create(['name' => '2027/2028', 'code' => '2027-28', 'starts_on' => '2027-09-01', 'ends_on' => '2028-07-31', 'is_current' => true]);
        $this->assertFalse($first->fresh()->is_current);
        $this->assertTrue($second->fresh()->is_current);

        $course = Subject::create(['name' => 'Self Reference', 'code' => 'SELF', 'course_type' => 'core']);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        CoursePrerequisite::create(['course_id' => $course->id, 'prerequisite_course_id' => $course->id]);
    }
}
