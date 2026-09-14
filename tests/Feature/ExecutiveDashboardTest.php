<?php

namespace Tests\Feature;

use App\Models\AdmissionApplication;
use App\Models\AcademicYear;
use App\Models\Role;
use App\Models\User;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class ExecutiveDashboardTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_president_can_access_the_real_data_executive_dashboard(): void
    {
        $this->seed();
        $role = Role::firstOrCreate(['slug' => Role::PRESIDENT], ['name' => 'President']);
        $president = User::factory()->create(['role_id' => $role->id]);
        AdmissionApplication::create([
            'name' => 'Executive Dashboard Applicant',
            'email' => 'executive-applicant@example.com',
            'phone' => '08000000000',
            'grade_interested' => '100 Level',
            'status' => 'approved',
        ]);
        AcademicYear::create([
            'name' => '2026/2027',
            'code' => 'EXEC-2026-27',
            'starts_on' => '2026-09-01',
            'ends_on' => '2027-07-31',
            'is_current' => true,
            'status' => 'open',
        ]);

        $this->actingAs($president)->get('/dashboard?from=2026-09-01&to=2026-12-31')
            ->assertOk()
            ->assertViewIs('dashboard.executive')
            ->assertViewHas('stats', fn (array $stats) => $stats['applicants'] === 1 && $stats['admissions_conversion'] === 100.0)
            ->assertSee('Executive Leadership Dashboard');
    }


    public function test_president_uses_admin_login_and_sees_executive_navigation(): void
    {
        $this->seed();
        $role = Role::firstOrCreate(['slug' => Role::PRESIDENT], ['name' => 'President']);
        $president = User::factory()->create([
            'role_id' => $role->id,
            'email' => 'president-auth@example.test',
            'password' => bcrypt('secret-password'),
        ]);

        $this->post('/admin/login', ['email' => $president->email, 'password' => 'secret-password'])
            ->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($president);
        $this->get('/dashboard')->assertOk()
            ->assertSee('Executive Oversight')
            ->assertSee('Executive Dashboard')
            ->assertSee('My Profile');
        $this->get('/portal/login')->assertRedirect('/dashboard');
    }

    public function test_chancellor_can_access_the_same_executive_scope(): void
    {
        $this->seed();
        $role = Role::firstOrCreate(['slug' => Role::CHANCELLOR], ['name' => 'Chancellor']);
        $chancellor = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($chancellor)->get('/dashboard')->assertOk()->assertViewIs('dashboard.executive');
    }

    public function test_ordinary_users_cannot_access_executive_dashboard(): void
    {
        $this->seed();
        foreach (['student', 'teacher', 'staff'] as $slug) {
            $role = Role::where('slug', $slug)->firstOrFail();
            $user = User::factory()->create(['role_id' => $role->id]);
            $response = $this->actingAs($user)->get('/dashboard')->assertOk();
            $this->assertNotSame('dashboard.executive', $response->original->name());
        }
    }

    public function test_unauthenticated_users_are_redirected(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_executives_can_access_university_wide_read_only_surfaces(): void
    {
        $this->seed();
        $role = Role::firstOrCreate(['slug' => Role::PRESIDENT], ['name' => 'President']);
        $president = User::factory()->create(['role_id' => $role->id]);

        foreach ([
            'executive.faculties' => 'Faculties',
            'executive.departments' => 'Departments',
            'executive.programs' => 'Programs',
            'executive.students' => 'Students',
            'executive.academic-performance' => 'Academic Performance',
            'executive.analytics' => 'Executive Analytics',
            'executive.reports' => 'Executive Reports',
        ] as $route => $heading) {
            $this->actingAs($president)->get(route($route))
                ->assertOk()
                ->assertSee($heading);
        }
    }

    public function test_non_executives_cannot_access_read_only_surfaces(): void
    {
        $this->seed();
        $role = Role::where('slug', Role::STAFF)->firstOrFail();
        $staff = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($staff)->get(route('executive.faculties'))->assertForbidden();
        $this->actingAs($staff)->get(route('executive.reports'))->assertForbidden();
    }

}
