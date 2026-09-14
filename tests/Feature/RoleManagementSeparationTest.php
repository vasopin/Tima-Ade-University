<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class RoleManagementSeparationTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_rbac_page_is_distinct_from_user_accounts_and_uses_authoritative_roles(): void
    {
        $this->seed();
        $superAdmin = User::where('email', 'superadmin@school.com')->firstOrFail();

        $this->actingAs($superAdmin)->get(route('super-admin.roles'))
            ->assertOk()
            ->assertViewIs('super-admin.roles')
            ->assertSee('Role definitions')
            ->assertSee('Manage User Accounts')
            ->assertSee(Role::where('slug', Role::FINANCE_OFFICER)->firstOrFail()->name);

        $this->actingAs($superAdmin)->get(route('users.index'))
            ->assertOk()
            ->assertViewIs('users.index')
            ->assertSee('User Accounts')
            ->assertDontSee('Role definitions');
    }

    public function test_only_super_admin_can_open_rbac_page_and_regular_admin_still_manages_accounts(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();
        $superAdmin = User::where('email', 'superadmin@school.com')->firstOrFail();
        $teacher = User::where('email', 'teacher1@school.com')->firstOrFail();

        $this->actingAs($admin)->get(route('super-admin.roles'))->assertForbidden();
        $this->actingAs($teacher)->get(route('super-admin.roles'))->assertForbidden();
        $this->actingAs($admin)->get(route('users.index'))->assertOk();
        $this->actingAs($superAdmin)->get(route('super-admin.roles'))->assertOk();
    }

    public function test_role_page_links_back_to_the_same_user_role_relationship(): void
    {
        $this->seed();
        $superAdmin = User::where('email', 'superadmin@school.com')->firstOrFail();
        $role = Role::where('slug', Role::REGISTRAR)->firstOrFail();

        $this->actingAs($superAdmin)->get(route('super-admin.roles'))
            ->assertSee(route('users.index', ['role_id' => $role->id]));
    }
}
