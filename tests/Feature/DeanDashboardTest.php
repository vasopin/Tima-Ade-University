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

class DeanDashboardTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_dean_is_scoped_to_assigned_faculty_and_filters_by_department_and_program(): void
    {
        $this->seed();
        $deanRole = Role::firstOrCreate(['slug' => Role::DEAN], ['name' => 'Dean']);
        $campus = Campus::create(['name' => 'Main', 'code' => 'MAIN']);
        $faculty = Faculty::create(['campus_id' => $campus->id, 'name' => 'Science', 'code' => 'SCI']);
        $otherFaculty = Faculty::create(['campus_id' => $campus->id, 'name' => 'Arts', 'code' => 'ART']);
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Computing', 'code' => 'CSC']);
        $otherDepartment = Department::create(['faculty_id' => $otherFaculty->id, 'name' => 'History', 'code' => 'HIS']);
        $program = Program::create(['department_id' => $department->id, 'name' => 'Computer Science', 'code' => 'BSC-CS', 'degree_type' => 'Bachelor']);
        $otherProgram = Program::create(['department_id' => $otherDepartment->id, 'name' => 'History', 'code' => 'BA-HIS', 'degree_type' => 'Bachelor']);
        $year = AcademicYear::create(['name' => '2026/2027', 'code' => 'D-2026', 'starts_on' => '2026-09-01', 'ends_on' => '2027-07-31', 'is_current' => true]);
        $term = Term::create(['academic_year_id' => $year->id, 'name' => 'Semester 1', 'code' => 'D-S1', 'starts_on' => '2026-09-01', 'ends_on' => '2027-01-31']);
        $course = Subject::create(['department_id' => $department->id, 'name' => 'Algorithms', 'code' => 'D-CSC201']);
        $section = CourseSection::create(['course_id' => $course->id, 'term_id' => $term->id, 'code' => 'A']);
        $schoolClass = SchoolClass::create(['name' => '100 Level']);
        $classSection = Section::create(['school_class_id' => $schoolClass->id, 'name' => 'A']);
        $studentRole = Role::where('slug', Role::STUDENT)->firstOrFail();
        $createStudent = function (Program $studentProgram) use ($studentRole, $schoolClass, $classSection): Student {
            $studentUser = User::factory()->create(['role_id' => $studentRole->id]);
            return Student::create([
                'user_id' => $studentUser->id,
                'roll_number' => 'ROLL-'.$studentProgram->id.'-'.$studentUser->id,
                'admission_number' => 'ADM-'.$studentProgram->id.'-'.$studentUser->id,
                'school_class_id' => $schoolClass->id,
                'section_id' => $classSection->id,
                'admission_date' => now()->toDateString(),
                'program_id' => $studentProgram->id,
                'status' => 'active',
            ]);
        };
        $inScope = $createStudent($program);
        $createStudent($otherProgram);
        Enrollment::create(['student_id' => $inScope->id, 'course_section_id' => $section->id, 'enrolled_at' => now(), 'status' => 'enrolled']);
        $dean = User::factory()->create(['role_id' => $deanRole->id, 'faculty_id' => $faculty->id]);

        $this->actingAs($dean)->get('/dashboard?academic_year_id='.$year->id.'&term_id='.$term->id.'&department_id='.$department->id.'&program_id='.$program->id)
            ->assertOk()->assertViewIs('dashboard.dean')
            ->assertViewHas('faculty', fn (Faculty $viewFaculty) => $viewFaculty->id === $faculty->id)
            ->assertViewHas('stats', fn (array $stats) => $stats['students'] === 1 && $stats['active_enrollments'] === 1)
            ->assertViewHas('departmentOverview', fn ($departments) => $departments->pluck('id')->all() === [$department->id])
            ->assertViewHas('courseOverview', fn ($courses) => $courses->pluck('id')->all() === [$course->id])
            ->assertViewHas('studentOverview', fn ($students) => $students->pluck('id')->all() === [$inScope->id]);
        $this->actingAs($dean)->get('/dashboard?department_id='.$otherDepartment->id)->assertNotFound();
    }

    public function test_dean_without_faculty_and_other_roles_cannot_access_dean_view(): void
    {
        $this->seed();
        $deanRole = Role::firstOrCreate(['slug' => Role::DEAN], ['name' => 'Dean']);
        $dean = User::factory()->create(['role_id' => $deanRole->id]);
        $this->actingAs($dean)->get('/dashboard')->assertForbidden();

        $student = User::whereHas('role', fn ($query) => $query->where('slug', Role::STUDENT))->firstOrFail();
        $response = $this->actingAs($student)->get('/dashboard')->assertOk();
        $this->assertNotSame('dashboard.dean', $response->original->name());
    }

    public function test_dedicated_dean_surfaces_are_faculty_scoped_and_export_is_protected(): void
    {
        $this->seed();
        $deanRole = Role::where('slug', Role::DEAN)->firstOrFail();
        $campus = Campus::create(['name' => 'Main', 'code' => 'DED']);
        $faculty = Faculty::create(['campus_id' => $campus->id, 'name' => 'Science', 'code' => 'DSC']);
        $otherFaculty = Faculty::create(['campus_id' => $campus->id, 'name' => 'Arts', 'code' => 'DART']);
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Computing', 'code' => 'DCO']);
        $otherDepartment = Department::create(['faculty_id' => $otherFaculty->id, 'name' => 'History', 'code' => 'DH']);
        $program = Program::create(['department_id' => $department->id, 'name' => 'Computer Science', 'code' => 'DCS', 'degree_type' => 'Bachelor']);
        $otherProgram = Program::create(['department_id' => $otherDepartment->id, 'name' => 'History', 'code' => 'DHI', 'degree_type' => 'Bachelor']);
        $schoolClass = SchoolClass::create(['name' => '200 Level']);
        $classSection = Section::create(['school_class_id' => $schoolClass->id, 'name' => 'B']);
        $studentRole = Role::where('slug', Role::STUDENT)->firstOrFail();
        $student = User::factory()->create(['role_id' => $studentRole->id]);
        Student::create(['user_id' => $student->id, 'roll_number' => 'D-R', 'admission_number' => 'D-A', 'school_class_id' => $schoolClass->id, 'section_id' => $classSection->id, 'admission_date' => now()->toDateString(), 'program_id' => $program->id, 'status' => 'active']);
        $otherStudent = User::factory()->create(['role_id' => $studentRole->id]);
        Student::create(['user_id' => $otherStudent->id, 'roll_number' => 'OD-R', 'admission_number' => 'OD-A', 'school_class_id' => $schoolClass->id, 'section_id' => $classSection->id, 'admission_date' => now()->toDateString(), 'program_id' => $otherProgram->id, 'status' => 'active']);
        $dean = User::factory()->create(['role_id' => $deanRole->id, 'faculty_id' => $faculty->id]);

        $this->get('/dean/departments')->assertRedirect('/login');
        foreach (['departments', 'programs', 'courses', 'sections', 'students', 'staff', 'analytics', 'reports'] as $surface) {
            $this->actingAs($dean)->get('/dean/'.$surface)->assertOk();
        }
        $this->actingAs($dean)->get('/dean/students?department_id='.$otherDepartment->id)->assertOk()->assertSee('No faculty records match');
        $export = $this->actingAs($dean)->get('/dean/students/export');
        $export->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Computer Science', $export->streamedContent());
        $this->actingAs($dean)->get('/dean/departments/'.$otherDepartment->id)->assertNotFound();
        $this->actingAs($student)->get('/dean/departments')->assertForbidden();
    }
}
