<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class DevelopmentDashboardAccountsTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_all_dashboard_accounts_use_their_authorized_login_portal_and_dashboard(): void
    {
        $this->seed();

        $portalAccounts = [
            'teacher1@school.com',
            'student1@school.com',
            'parent1@school.com',
        ];
        $adminAccounts = [
            'dashboard.super_admin@example.test',
            'dashboard.admin@example.test',
            'dashboard.staff@example.test',
            'dashboard.admissions_officer@example.test',
            'dashboard.finance_officer@example.test',
            'dashboard.registrar@example.test',
            'dashboard.hr_officer@example.test',
            'dashboard.librarian@example.test',
            'dashboard.president@example.test',
            'dashboard.chancellor@example.test',
            'dashboard.dean@example.test',
            'dashboard.department_head@example.test',
            'dashboard.academic_advisor@example.test',
        ];

        foreach ($portalAccounts as $email) {
            $user = User::where('email', $email)->firstOrFail();

            $this->post(route('university.login.post'), [
                'email' => $email,
                'password' => 'password',
            ])->assertRedirect(route('dashboard'));

            $this->assertAuthenticatedAs($user);
            $dashboard = $this->get(route('dashboard'))->assertOk();
            if ($user->role?->slug === 'academic_advisor') {
                $dashboard->assertViewIs('advising.dashboard');
            }
            $this->post(route('logout'));
        }

        foreach ($adminAccounts as $email) {
            $user = User::where('email', $email)->firstOrFail();

            $this->post(route('admin.login.post'), [
                'email' => $email,
                'password' => 'password',
            ])->assertRedirect(in_array($user->role?->slug, ['super_admin', 'admin', 'staff'], true)
                ? route('hemis')
                : route('dashboard'));

            $this->assertAuthenticatedAs($user);
            $this->get(route('dashboard'))->assertOk();
            $this->post(route('logout'));
        }
    }
}
