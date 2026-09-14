<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class Student360Test extends TestCase
{
    use ForceRefreshDatabase;

    public function test_student_360_enforces_student_ownership_and_staff_access(): void
    {
        $this->seed();
        $students = Student::with('user')->limit(2)->get();
        $this->assertGreaterThanOrEqual(2, $students->count());

        $this->actingAs($students[0]->user)->get(route('students.student-360', $students[1]))
            ->assertForbidden();
        $this->actingAs($students[0]->user)->get(route('students.student-360', $students[0]))
            ->assertOk()->assertViewIs('students.student-360');

        $admin = User::factory()->create(['role_id' => Role::where('slug', Role::ADMIN)->value('id')]);
        $this->actingAs($admin)->get(route('students.student-360', $students[1]))
            ->assertOk();
    }

    public function test_student_360_requires_authentication(): void
    {
        $this->seed();
        $student = Student::firstOrFail();
        $this->get(route('students.student-360', $student))->assertRedirect();
    }
}
