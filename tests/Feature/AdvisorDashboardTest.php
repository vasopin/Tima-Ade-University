<?php

namespace Tests\Feature;

use App\Models\AdvisingAppointment;
use App\Models\AdvisingNote;
use App\Models\AdvisorAssignment;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\User;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class AdvisorDashboardTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_advisor_dashboard_is_limited_to_active_advisee_assignments(): void
    {
        $this->seed();
        [$advisor, $student, $otherStudent] = $this->advisingFixture();
        AdvisorAssignment::create(['student_id' => $student->id, 'advisor_id' => $advisor->id, 'assigned_by' => $advisor->id, 'assigned_at' => now(), 'is_active' => true]);
        AdvisorAssignment::create(['student_id' => $otherStudent->id, 'advisor_id' => User::factory()->create(['role_id' => Role::where('slug', Role::TEACHER)->value('id')])->id, 'assigned_by' => $advisor->id, 'assigned_at' => now(), 'is_active' => true]);

        $this->actingAs($advisor)->get(route('advising.dashboard'))
            ->assertOk()->assertViewIs('advising.dashboard')
            ->assertViewHas('stats', fn (array $stats) => $stats['assigned'] === 1)
            ->assertSee($student->user->name)->assertDontSee($otherStudent->user->name);
    }

    public function test_private_notes_and_appointment_mutations_are_scoped(): void
    {
        $this->seed();
        [$advisor, $student, $otherStudent] = $this->advisingFixture();
        AdvisorAssignment::create(['student_id' => $student->id, 'advisor_id' => $advisor->id, 'assigned_by' => $advisor->id, 'assigned_at' => now(), 'is_active' => true]);
        $private = AdvisingNote::create(['student_id' => $student->id, 'advisor_id' => $advisor->id, 'body' => 'Private follow-up', 'is_private' => true, 'follow_up_at' => now()->subDay()]);
        $appointment = AdvisingAppointment::create(['student_id' => $student->id, 'advisor_id' => $advisor->id, 'requested_by' => $advisor->id, 'scheduled_at' => now()->addDay(), 'duration_minutes' => 30, 'mode' => 'online', 'status' => 'requested', 'topic' => 'Progress']);

        $this->actingAs($advisor)->get(route('advising.dashboard'))->assertSee('Private follow-up');
        $this->actingAs($advisor)->patch(route('advising.appointments.update', $appointment), ['status' => 'confirmed'])->assertRedirect();
        $this->actingAs($advisor)->post(route('advising.notes.store'), ['student_id' => $otherStudent->id, 'body' => 'Unauthorized'])->assertForbidden();
        $this->actingAs($advisor)->patch(route('advising.appointments.update', $appointment), ['status' => 'cancelled'])->assertRedirect();
        $this->assertDatabaseHas('advising_appointments', ['id' => $appointment->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('advising_notes', ['id' => $private->id, 'is_private' => 1]);
    }

    public function test_academic_advisor_management_and_student_360_are_cross_advisor_scoped(): void
    {
        $this->seed();
        [$advisor, $student, $otherStudent] = $this->advisingFixture(Role::ACADEMIC_ADVISOR);
        $otherAdvisor = User::factory()->create(['role_id' => Role::where('slug', Role::ACADEMIC_ADVISOR)->value('id')]);
        AdvisorAssignment::create(['student_id' => $student->id, 'advisor_id' => $advisor->id, 'assigned_by' => $advisor->id, 'assigned_at' => now(), 'is_active' => true]);
        AdvisorAssignment::create(['student_id' => $otherStudent->id, 'advisor_id' => $otherAdvisor->id, 'assigned_by' => $advisor->id, 'assigned_at' => now(), 'is_active' => true]);

        $this->actingAs($advisor)->get(route('advising.index'))
            ->assertOk()
            ->assertSee($student->user->name)
            ->assertDontSee($otherStudent->user->name);
        $this->actingAs($advisor)->get(route('students.student-360.index'))
            ->assertOk()
            ->assertSee($student->user->name)
            ->assertDontSee($otherStudent->user->name);
        $this->actingAs($advisor)->get(route('students.student-360', $student))
            ->assertOk();
        $this->actingAs($advisor)->get(route('students.student-360', $otherStudent))
            ->assertForbidden();
    }

    private function advisingFixture(string $advisorRole = Role::TEACHER): array
    {
        $role = Role::where('slug', $advisorRole)->firstOrFail();
        $advisor = User::factory()->create(['role_id' => $role->id]);
        $campus = Campus::create(['name' => 'Advising Campus', 'code' => 'ADV']);
        $faculty = Faculty::create(['campus_id' => $campus->id, 'name' => 'Science', 'code' => 'ADVS']);
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Computing', 'code' => 'ADVC']);
        $program = Program::create(['department_id' => $department->id, 'name' => 'Computer Science', 'code' => 'ADV-CS', 'degree_type' => 'Bachelor']);
        $studentRole = Role::where('slug', Role::STUDENT)->firstOrFail();
        $schoolClass = SchoolClass::create(['name' => '100 Level']);
        $section = Section::create(['school_class_id' => $schoolClass->id, 'name' => 'A']);
        $createStudent = function (string $suffix) use ($studentRole, $schoolClass, $section, $program): Student {
            $studentUser = User::factory()->create(['role_id' => $studentRole->id]);
            return Student::create([
                'user_id' => $studentUser->id,
                'roll_number' => 'ADV-ROLL-'.$suffix,
                'admission_number' => 'ADV-ADM-'.$suffix,
                'school_class_id' => $schoolClass->id,
                'section_id' => $section->id,
                'admission_date' => now(),
                'program_id' => $program->id,
                'status' => 'active',
            ]);
        };
        $student = $createStudent('ONE');
        $otherStudent = $createStudent('TWO');

        return [$advisor, $student, $otherStudent];
    }
}
