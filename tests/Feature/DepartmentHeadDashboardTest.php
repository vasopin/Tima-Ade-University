<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Campus;
use App\Models\CourseSection;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Section;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class DepartmentHeadDashboardTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_department_head_is_scoped_and_filters_by_program_and_term(): void
    {
        $this->seed();
        $role = Role::firstOrCreate(['slug' => Role::DEPARTMENT_HEAD], ['name' => 'Department Head']);
        $campus = Campus::create(['name' => 'Main', 'code' => 'DHM']);
        $faculty = Faculty::create(['campus_id' => $campus->id, 'name' => 'Science', 'code' => 'DHS']);
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Computing', 'code' => 'DHC']);
        $otherDepartment = Department::create(['faculty_id' => $faculty->id, 'name' => 'History', 'code' => 'DHH']);
        $program = Program::create(['department_id' => $department->id, 'name' => 'Computer Science', 'code' => 'DH-CS', 'degree_type' => 'Bachelor']);
        $otherProgram = Program::create(['department_id' => $otherDepartment->id, 'name' => 'History', 'code' => 'DH-HIS', 'degree_type' => 'Bachelor']);
        $year = AcademicYear::create(['name' => '2026/2027', 'code' => 'DH-2026', 'starts_on' => '2026-09-01', 'ends_on' => '2027-07-31', 'is_current' => true]);
        $term = Term::create(['academic_year_id' => $year->id, 'name' => 'Semester 1', 'code' => 'DH-S1', 'starts_on' => '2026-09-01', 'ends_on' => '2027-01-31']);
        $course = Subject::create(['department_id' => $department->id, 'name' => 'Algorithms', 'code' => 'DH-ALG']);
        $section = CourseSection::create(['course_id' => $course->id, 'term_id' => $term->id, 'code' => 'A']);
        $studentRole = Role::where('slug', Role::STUDENT)->firstOrFail();
        $schoolClass = SchoolClass::create(['name' => '100 Level']);
        $classSection = Section::create(['school_class_id' => $schoolClass->id, 'name' => 'A']);
        $studentUser = User::factory()->create(['role_id' => $studentRole->id]);
        $student = Student::create(['user_id' => $studentUser->id, 'roll_number' => 'DH-ROLL', 'admission_number' => 'DH-ADM', 'school_class_id' => $schoolClass->id, 'section_id' => $classSection->id, 'admission_date' => now(), 'program_id' => $program->id, 'status' => 'active']);
        Enrollment::create(['student_id' => $student->id, 'course_section_id' => $section->id, 'enrolled_at' => now(), 'status' => 'enrolled']);
        $otherUser = User::factory()->create(['role_id' => $studentRole->id]);
        Student::create(['user_id' => $otherUser->id, 'roll_number' => 'DH-OTHER', 'admission_number' => 'DH-OTHER', 'school_class_id' => $schoolClass->id, 'section_id' => $classSection->id, 'admission_date' => now(), 'program_id' => $otherProgram->id, 'status' => 'active']);
        $head = User::factory()->create(['role_id' => $role->id]);
        $department->update(['head_user_id' => $head->id]);

        $this->actingAs($head)->get('/dashboard?academic_year_id='.$year->id.'&term_id='.$term->id.'&program_id='.$program->id)
            ->assertOk()->assertViewIs('dashboard.department-head')
            ->assertViewHas('department', fn (Department $viewDepartment) => $viewDepartment->id === $department->id)
            ->assertViewHas('stats', fn (array $stats) => $stats['students'] === 1 && $stats['active_enrollments'] === 1);
        $this->actingAs($head)->get('/dashboard?program_id='.$otherProgram->id)->assertNotFound();
    }

    public function test_department_head_cannot_combine_a_term_from_another_academic_year(): void
    {
        $this->seed();
        $role = Role::firstOrCreate(['slug' => Role::DEPARTMENT_HEAD], ['name' => 'Department Head']);
        $faculty = Faculty::firstOrFail();
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Scoped Department', 'code' => 'DH-TERM']);
        $head = User::factory()->create(['role_id' => $role->id]);
        $department->update(['head_user_id' => $head->id]);
        $currentYear = AcademicYear::create([
            'name' => '2026/2027',
            'code' => 'DH-TERM-CURRENT',
            'starts_on' => '2026-09-01',
            'ends_on' => '2027-07-31',
            'is_current' => true,
        ]);
        $otherYear = AcademicYear::create([
            'name' => '2027/2028',
            'code' => 'DH-TERM-OTHER',
            'starts_on' => '2027-09-01',
            'ends_on' => '2028-07-31',
            'is_current' => false,
        ]);
        $term = Term::create([
            'academic_year_id' => $otherYear->id,
            'name' => 'Semester 1',
            'code' => 'DH-TERM-OTHER-S1',
            'starts_on' => '2027-09-01',
            'ends_on' => '2028-01-31',
        ]);

        $this->actingAs($head)
            ->get(route('dashboard', ['academic_year_id' => $currentYear->id, 'term_id' => $term->id]))
            ->assertNotFound();
    }

    public function test_unassigned_department_head_is_forbidden_and_other_roles_are_unchanged(): void
    {
        $this->seed();
        $role = Role::firstOrCreate(['slug' => Role::DEPARTMENT_HEAD], ['name' => 'Department Head']);
        $head = User::factory()->create(['role_id' => $role->id]);
        $this->actingAs($head)->get('/dashboard')->assertForbidden();

        $student = User::whereHas('role', fn ($query) => $query->where('slug', Role::STUDENT))->firstOrFail();
        $this->actingAs($student)->get('/dashboard')->assertOk()->assertViewIs('dashboard.student');
    }

    public function test_department_head_read_only_surfaces_remain_department_scoped(): void
    {
        $this->seed();
        $role = Role::where('slug', Role::DEPARTMENT_HEAD)->firstOrFail();
        $faculty = Faculty::firstOrFail();
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Scoped Computing', 'code' => 'DHSCOPE']);
        $otherDepartment = Department::create(['faculty_id' => $faculty->id, 'name' => 'Hidden History', 'code' => 'DHHIDDEN']);
        $program = Program::create(['department_id' => $department->id, 'name' => 'Scoped Program', 'code' => 'SCOPED', 'degree_type' => 'Bachelor']);
        Program::create(['department_id' => $otherDepartment->id, 'name' => 'Hidden Program', 'code' => 'HIDDEN', 'degree_type' => 'Bachelor']);
        Subject::create(['department_id' => $department->id, 'name' => 'Scoped Course', 'code' => 'SCOPED-101']);
        Subject::create(['department_id' => $otherDepartment->id, 'name' => 'Hidden Course', 'code' => 'HIDDEN-101']);
        $head = User::factory()->create(['role_id' => $role->id]);
        $department->update(['head_user_id' => $head->id]);

        foreach ([
            'department-head.programs' => 'Scoped Program',
            'department-head.courses' => 'Scoped Course',
            'department-head.sections' => 'Course Sections',
            'department-head.students' => 'Students',
            'department-head.instructors' => 'Instructors',
            'department-head.academic-performance' => 'Academic Performance',
            'department-head.advising' => 'Advising',
            'department-head.analytics' => 'Department Analytics',
            'department-head.reports' => 'Department Reports',
        ] as $route => $expected) {
            $response = $this->actingAs($head)->get(route($route));
            $response->assertOk()->assertSee($expected)->assertDontSee('Hidden History')->assertDontSee('Hidden Program')->assertDontSee('Hidden Course');
        }

        $this->actingAs($head)->get(route('department-head.students', ['search' => 'Hidden']))->assertOk()->assertDontSee('Hidden History')->assertDontSee('Hidden Program');
        $this->actingAs(User::factory()->create(['role_id' => Role::where('slug', Role::STAFF)->value('id')]))->get(route('department-head.programs'))->assertForbidden();
    }
}
