<?php

namespace Tests\Feature;

use \Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class RoleAccessTest extends TestCase
{
    use \Tests\Concerns\ForceRefreshDatabase;

    public function test_unauthenticated_user_cannot_access_protected_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_cannot_access_students_management(): void
    {
        $response = $this->get('/students');
        $response->assertRedirect('/login');
    }

    public function test_admin_user_can_access_dashboard_and_modules(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@school.com')->first();

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);

        $responseStudents = $this->actingAs($admin)->get('/students');
        $responseStudents->assertStatus(200);

        $responseTeachers = $this->actingAs($admin)->get('/teachers');
        $responseTeachers->assertStatus(200);
    }

    public function test_api_user_endpoint_returns_authenticated_role(): void
    {
        $this->seed();
        $teacher = User::where('email', 'teacher1@school.com')->first();

        $response = $this->actingAs($teacher)->getJson('/api/user');
        $response->assertStatus(200)
            ->assertJson([
                'email' => 'teacher1@school.com',
                'role'  => 'teacher',
            ]);
    }

    public function test_api_user_endpoint_returns_only_the_authenticated_students_id(): void
    {
        $this->seed();
        $student = User::where('email', 'student1@school.com')->firstOrFail();

        $this->actingAs($student)->getJson('/api/user')
            ->assertOk()
            ->assertJson([
                'email' => 'student1@school.com',
                'student_id' => $student->student->student_id,
            ]);
    }

    public function test_admission_portal_allows_only_administrators_and_staff(): void
    {
        foreach (['super_admin', 'admin', 'staff'] as $roleSlug) {
            $this->actingAs($this->userWithRole($roleSlug))
                ->get('/admin/applications')
                ->assertOk();
        }

        foreach (['student', 'teacher', 'parent'] as $roleSlug) {
            $this->actingAs($this->userWithRole($roleSlug))
                ->get('/admin/applications')
                ->assertForbidden();
        }
    }

    public function test_non_admin_roles_cannot_access_directory_management_routes(): void
    {
        $this->seed();

        $teacher = User::where('email', 'teacher1@school.com')->firstOrFail();
        $student = User::where('email', 'student1@school.com')->firstOrFail();
        $parent = User::where('email', 'parent1@school.com')->firstOrFail();
        $staff = User::where('email', 'staff1@school.com')->firstOrFail();

        foreach ([$teacher, $student, $parent] as $user) {
            $this->actingAs($user)->get('/students')->assertForbidden();
            $this->actingAs($user)->get('/teachers')->assertForbidden();
            $this->actingAs($user)->get('/parents')->assertForbidden();
        }

        $this->actingAs($staff)->get('/students')->assertOk();
        $this->actingAs($staff)->get('/parents')->assertOk();
        $this->actingAs($staff)->get('/teachers')->assertForbidden();
    }

    private function userWithRole(string $roleSlug): User
    {
        $role = Role::create([
            'name' => ucwords(str_replace('_', ' ', $roleSlug)),
            'slug' => $roleSlug,
        ]);

        return User::factory()->create(['role_id' => $role->id]);
    }
}
