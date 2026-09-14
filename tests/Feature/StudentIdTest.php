<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use \Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class StudentIdTest extends TestCase
{
    use \Tests\Concerns\ForceRefreshDatabase;

    public function test_students_receive_unique_ids_in_the_official_format(): void
    {
        $this->seed();
        $students = Student::orderBy('id')->get();

        $this->assertCount($students->count(), $students->pluck('student_id')->unique());
        $this->assertTrue($students->every(fn (Student $student) => preg_match('/^TAU-\d{4}-\d{6}$/', $student->student_id) === 1));
    }

    public function test_new_student_receives_the_next_distinct_id(): void
    {
        $this->seed();
        $existingStudent = Student::firstOrFail();
        $newUser = User::factory()->create([
            'role_id' => Role::where('slug', Role::STUDENT)->value('id'),
        ]);

        $newStudent = Student::create([
            'user_id' => $newUser->id,
            'roll_number' => 'ROLL-NEW',
            'admission_number' => 'ADM-NEW',
            'school_class_id' => $existingStudent->school_class_id,
            'section_id' => $existingStudent->section_id,
            'admission_date' => '2024-01-15',
            'status' => 'active',
        ]);

        $this->assertMatchesRegularExpression('/^TAU-2024-\d{6}$/', $newStudent->student_id);
        $this->assertNotSame($existingStudent->student_id, $newStudent->student_id);
    }

    public function test_student_id_is_unchanged_when_the_profile_is_updated(): void
    {
        $this->seed();
        $student = Student::firstOrFail();
        $studentId = $student->student_id;

        $student->update(['address' => 'Updated address']);

        $this->assertSame($studentId, $student->fresh()->student_id);
    }

    public function test_authorized_directory_searches_by_student_id(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();
        $student = Student::with('user')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('students.index', ['search' => $student->student_id]))
            ->assertOk()
            ->assertSee($student->student_id)
            ->assertSee($student->user->name);
    }

    public function test_student_can_see_only_their_own_profile_and_id(): void
    {
        $this->seed();
        $studentUser = User::where('email', 'student1@school.com')->firstOrFail();
        $ownStudent = $studentUser->student;
        $otherStudent = Student::where('id', '!=', $ownStudent->id)->firstOrFail();

        $this->actingAs($studentUser)
            ->get(route('students.show', $ownStudent))
            ->assertOk()
            ->assertSee($ownStudent->student_id);

        $this->actingAs($studentUser)
            ->get(route('students.show', $otherStudent))
            ->assertForbidden();
    }

    public function test_admin_and_staff_can_view_student_ids(): void
    {
        $this->seed();
        $student = Student::firstOrFail();
        $staff = User::factory()->create([
            'role_id' => Role::where('slug', Role::STAFF)->value('id'),
            'status' => 'active',
            'is_active' => true,
        ]);

        foreach ([
            User::where('email', 'superadmin@school.com')->firstOrFail(),
            User::where('email', 'admin@school.com')->firstOrFail(),
            $staff,
        ] as $user) {
            $this->actingAs($user)
                ->get(route('students.show', $student))
                ->assertOk()
                ->assertSee($student->student_id);
        }
    }

    public function test_client_submitted_student_id_does_not_override_the_official_id(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();
        $student = Student::firstOrFail();
        $originalId = $student->student_id;

        $this->actingAs($admin)->put(route('students.update', $student), [
            'name' => $student->user->name,
            'email' => $student->user->email,
            'phone' => $student->user->phone,
            'student_id' => 'TAU-2099-999999',
            'roll_number' => $student->roll_number,
            'school_class_id' => $student->school_class_id,
            'section_id' => $student->section_id,
            'admission_date' => $student->admission_date->toDateString(),
            'status' => $student->status,
        ])->assertRedirect();

        $this->assertSame($originalId, $student->fresh()->student_id);
    }
}
