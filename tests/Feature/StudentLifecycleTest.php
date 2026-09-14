<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Student;
use App\Models\StudentHold;
use App\Models\User;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class StudentLifecycleTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_registrar_can_change_status_and_history_is_preserved(): void
    {
        $this->seed();
        $student = Student::firstOrFail();
        $registrar = User::factory()->create(['role_id' => Role::where('slug', Role::REGISTRAR)->value('id')]);

        $this->actingAs($registrar)->post(route('students.lifecycle-status', $student), [
            'status' => 'leave', 'effective_date' => now()->toDateString(), 'reason' => 'Approved leave',
        ])->assertRedirect();

        $this->assertDatabaseHas('students', ['id' => $student->id, 'lifecycle_status' => 'leave']);
        $this->assertDatabaseHas('student_status_histories', ['student_id' => $student->id, 'to_status' => 'leave']);
    }

    public function test_active_registration_hold_is_released_by_registrar_only(): void
    {
        $this->seed();
        $student = Student::firstOrFail();
        $registrar = User::factory()->create(['role_id' => Role::where('slug', Role::REGISTRAR)->value('id')]);
        $hold = StudentHold::create(['student_id' => $student->id, 'type' => 'registration', 'reason' => 'Review', 'status' => 'active', 'effective_date' => now()->toDateString(), 'created_by' => $registrar->id]);

        $this->actingAs($student->user)->post(route('students.holds.release', $hold))->assertForbidden();
        $this->actingAs($registrar)->post(route('students.holds.release', $hold))->assertRedirect();
        $this->assertDatabaseHas('student_holds', ['id' => $hold->id, 'status' => 'released']);
    }
}
