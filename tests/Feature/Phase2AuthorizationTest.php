<?php

namespace Tests\Feature;

use \Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class Phase2AuthorizationTest extends TestCase
{
    use ForceRefreshDatabase;

    private function createUserWithRole(string $roleSlug): User
    {
        $role = Role::where('slug', $roleSlug)->first();
        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_registrar_can_access_dashboard(): void
    {
        $this->seed();
        $registrar = $this->createUserWithRole('registrar');

        $response = $this->actingAs($registrar)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.registrar');
    }

    public function test_hr_officer_can_access_dashboard(): void
    {
        $this->seed();
        $hrOfficer = $this->createUserWithRole('hr_officer');

        $response = $this->actingAs($hrOfficer)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.hr-officer');
    }

    public function test_librarian_can_access_dashboard(): void
    {
        $this->seed();
        $librarian = $this->createUserWithRole('librarian');

        $response = $this->actingAs($librarian)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.librarian');
    }

    public function test_student_cannot_access_registrar_dashboard(): void
    {
        $this->seed();
        $student = User::where('email', 'student1@school.com')->first();

        $response = $this->actingAs($student)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.student');
    }

    public function test_teacher_cannot_access_hr_dashboard(): void
    {
        $this->seed();
        $teacher = User::where('email', 'teacher1@school.com')->first();

        $response = $this->actingAs($teacher)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.teacher');
    }

    public function test_staff_cannot_access_librarian_dashboard(): void
    {
        $this->seed();
        $staff = User::where('email', 'staff1@school.com')->first();

        $response = $this->actingAs($staff)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.staff');
    }

    public function test_registrar_has_isRegistrar_method(): void
    {
        $this->seed();
        $registrar = $this->createUserWithRole('registrar');

        $this->assertTrue($registrar->isRegistrar());
        $this->assertFalse($registrar->isAdmin());
        $this->assertFalse($registrar->isStudent());
    }

    public function test_hr_officer_has_isHROfficer_method(): void
    {
        $this->seed();
        $hrOfficer = $this->createUserWithRole('hr_officer');

        $this->assertTrue($hrOfficer->isHROfficer());
        $this->assertFalse($hrOfficer->isAdmin());
        $this->assertFalse($hrOfficer->isTeacher());
    }

    public function test_librarian_has_isLibrarian_method(): void
    {
        $this->seed();
        $librarian = $this->createUserWithRole('librarian');

        $this->assertTrue($librarian->isLibrarian());
        $this->assertFalse($librarian->isStaff());
        $this->assertFalse($librarian->isStudent());
    }

    public function test_all_phase2_roles_exist_in_database(): void
    {
        $this->seed();

        $roles = ['registrar', 'hr_officer', 'librarian'];
        foreach ($roles as $slug) {
            $role = Role::where('slug', $slug)->first();
            $this->assertNotNull($role, "Role {$slug} not found in database");
        }
    }
}
