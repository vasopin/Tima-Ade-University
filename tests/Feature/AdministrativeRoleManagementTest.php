<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class AdministrativeRoleManagementTest extends TestCase
{
    use ForceRefreshDatabase;

    private array $rolePaths = [
        Role::ADMISSIONS_OFFICER => 'admissions-officers',
        Role::FINANCE_OFFICER => 'finance-officers',
        Role::REGISTRAR => 'registrars',
        Role::HR_OFFICER => 'hr-officers',
        Role::LIBRARIAN => 'librarians',
        Role::PRESIDENT => 'presidents',
        Role::CHANCELLOR => 'chancellors',
        Role::DEAN => 'deans',
        Role::DEPARTMENT_HEAD => 'department-heads',
        Role::ACADEMIC_ADVISOR => 'academic-advisors',
    ];

    public function test_admin_and_super_admin_can_open_each_dedicated_management_page(): void
    {
        $this->seed();

        foreach (['admin@school.com', 'superadmin@school.com'] as $email) {
            $manager = User::where('email', $email)->firstOrFail();
            foreach ($this->rolePaths as $roleSlug => $path) {
                $this->actingAs($manager)->get('/' . $path)
                    ->assertOk()
                    ->assertSee(Role::where('slug', $roleSlug)->firstOrFail()->name . ' Management')
                    ->assertSee('Add ' . Role::where('slug', $roleSlug)->firstOrFail()->name);

                $this->actingAs($manager)->get('/' . $path . '/create')
                    ->assertOk()
                    ->assertSee('Add ' . Role::where('slug', $roleSlug)->firstOrFail()->name);
            }
        }
    }

    public function test_dedicated_creation_assigns_the_existing_role(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();

        foreach ($this->rolePaths as $roleSlug => $path) {
            $role = Role::where('slug', $roleSlug)->firstOrFail();
            $email = 'dedicated-' . $roleSlug . '@example.com';

            $this->actingAs($admin)->post('/' . $path, [
                'name' => 'Dedicated ' . $role->name,
                'email' => $email,
                'password' => 'password',
                'status' => 'active',
            ])->assertRedirect('/' . $path);

            $this->assertSame($roleSlug, User::where('email', $email)->firstOrFail()->role->slug);
        }
    }

    public function test_non_admin_roles_cannot_access_dedicated_management_pages(): void
    {
        $this->seed();
        $roles = [
            'staff',
            'teacher',
            'student',
            'parent',
            'admissions_officer',
            'finance_officer',
            'registrar',
            'hr_officer',
            'librarian',
            'president',
            'chancellor',
            'dean',
            'department_head',
            'academic_advisor',
        ];

        foreach ($roles as $roleSlug) {
            $user = User::whereHas('role', fn ($query) => $query->where('slug', $roleSlug))->first();
            $user ??= User::factory()->create(['role_id' => Role::where('slug', $roleSlug)->firstOrFail()->id]);
            foreach ($this->rolePaths as $path) {
                $this->actingAs($user)->get('/' . $path)->assertForbidden();
                $this->actingAs($user)->post('/' . $path, [])->assertForbidden();
            }
        }
    }

    public function test_user_from_another_role_cannot_be_viewed_or_updated_through_dedicated_route(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();
        $registrar = User::whereHas('role', fn ($query) => $query->where('slug', Role::REGISTRAR))->first();
        $registrar ??= User::factory()->create(['role_id' => Role::where('slug', Role::REGISTRAR)->firstOrFail()->id]);

        $this->actingAs($admin)->get('/' . $this->rolePaths[Role::LIBRARIAN] . '/' . $registrar->id)->assertNotFound();
        $this->actingAs($admin)->put('/' . $this->rolePaths[Role::LIBRARIAN] . '/' . $registrar->id, [
            'name' => 'Wrong Role',
            'email' => 'wrong-role@example.com',
            'status' => 'active',
        ])->assertNotFound();
    }
}