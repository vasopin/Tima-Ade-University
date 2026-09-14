<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use \Tests\Concerns\ForceRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminAccountProvisioningTest extends TestCase
{
    use \Tests\Concerns\ForceRefreshDatabase;

    public function test_admin_and_super_admin_can_create_teacher_student_and_parent_accounts(): void
    {
        $this->seed();
        $schoolClass = \App\Models\SchoolClass::first();
        $section = \App\Models\Section::where('school_class_id', $schoolClass->id)->first();
        $student = Student::first();

        foreach (['admin@school.com', 'superadmin@school.com'] as $adminEmail) {
            $admin = User::where('email', $adminEmail)->first();
            $suffix = Str::lower(Str::random(8));
            $teacherEmail = "teacher-{$suffix}@example.com";
            $studentEmail = "student-{$suffix}@example.com";
            $parentEmail = "parent-{$suffix}@example.com";

            $this->actingAs($admin)->post('/teachers', [
                'name' => 'Provisioned Teacher',
                'email' => $teacherEmail,
                'password' => 'password',
                'employee_id' => "TCH-{$suffix}",
            ])->assertRedirect('/teachers');

            $this->actingAs($admin)->post('/students', [
                'name' => 'Provisioned Student',
                'email' => $studentEmail,
                'password' => 'password',
                'roll_number' => "STU-{$suffix}",
                'admission_number' => "ADM-{$suffix}",
                'school_class_id' => $schoolClass->id,
                'section_id' => $section->id,
                'admission_date' => now()->toDateString(),
                'status' => 'active',
            ])->assertRedirect('/students');

            $this->actingAs($admin)->post('/parents', [
                'name' => 'Provisioned Parent',
                'email' => $parentEmail,
                'password' => 'password',
                'relationship' => 'guardian',
                'student_ids' => [$student->id],
            ])->assertRedirect('/parents');

            $teacher = User::where('email', $teacherEmail)->first();
            $provisionedStudent = User::where('email', $studentEmail)->first();
            $parent = User::where('email', $parentEmail)->first();

            $this->assertSame('teacher', $teacher->role->slug);
            $this->assertTrue(Hash::check('password', $teacher->password));
            $this->assertInstanceOf(Teacher::class, $teacher->teacher);
            $this->assertSame('student', $provisionedStudent->role->slug);
            $this->assertTrue(Hash::check('password', $provisionedStudent->password));
            $this->assertInstanceOf(Student::class, $provisionedStudent->student);
            $this->assertSame('parent', $parent->role->slug);
            $this->assertTrue(Hash::check('password', $parent->password));
            $this->assertTrue($parent->guardian->students->contains($student));

            foreach ([$teacherEmail, $studentEmail, $parentEmail] as $email) {
                $this->post('/login', [
                    'email' => $email,
                    'password' => 'password',
                ])->assertRedirect('/dashboard');

                $this->get('/dashboard')->assertOk();
                $this->post('/logout')->assertRedirect('/login');
            }
        }
    }

    public function test_non_admin_roles_cannot_create_accounts(): void
    {
        $this->seed();
        $teacher = User::where('email', 'teacher1@school.com')->first();

        $this->actingAs($teacher)->get('/teachers/create')->assertForbidden();
        $this->actingAs($teacher)->get('/students/create')->assertForbidden();
        $this->actingAs($teacher)->get('/parents/create')->assertForbidden();
    }

    public function test_generic_user_form_cannot_create_privileged_accounts(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@school.com')->first();
        $superAdminRole = Role::where('slug', 'super_admin')->first();

        $this->actingAs($admin)->from('/users/create')->post('/users', [
            'name' => 'Unauthorized Privileged Account',
            'email' => 'privileged-attempt@example.com',
            'password' => 'password',
            'role_id' => $superAdminRole->id,
            'status' => 'active',
        ])->assertSessionHasErrors('role_id');

        $this->assertDatabaseMissing('users', ['email' => 'privileged-attempt@example.com']);
    }

    public function test_admin_and_super_admin_can_create_staff_accounts(): void
    {
        $this->seed();
        $staffRole = Role::where('slug', Role::STAFF)->firstOrFail();

        foreach (['admin@school.com', 'superadmin@school.com'] as $adminEmail) {
            $staffEmail = 'staff-' . Str::lower(Str::random(8)) . '@example.com';

            $this->actingAs(User::where('email', $adminEmail)->first())
                ->post('/users', [
                    'name' => 'Provisioned Staff',
                    'email' => $staffEmail,
                    'phone' => '+1-555-0100',
                    'password' => 'password',
                    'role_id' => $staffRole->id,
                    'status' => 'active',
                ])
                ->assertRedirect('/users');

            $staff = User::where('email', $staffEmail)->firstOrFail();
            $this->assertSame(Role::STAFF, $staff->role->slug);
            $this->assertTrue(Hash::check('password', $staff->password));
        }
    }

    public function test_admin_and_super_admin_can_create_specialized_accounts(): void
    {
        $this->seed();

        foreach (['admin@school.com', 'superadmin@school.com'] as $adminEmail) {
            foreach ([Role::REGISTRAR, Role::HR_OFFICER, Role::LIBRARIAN, Role::ADMISSIONS_OFFICER, Role::FINANCE_OFFICER] as $roleSlug) {
                $email = $roleSlug . '-' . Str::lower(Str::random(8)) . '@example.com';
                $role = Role::where('slug', $roleSlug)->firstOrFail();

                $this->actingAs(User::where('email', $adminEmail)->firstOrFail())
                    ->post('/users', [
                        'name' => ucfirst(str_replace('_', ' ', $roleSlug)),
                        'email' => $email,
                        'password' => 'password',
                        'role_id' => $role->id,
                        'status' => 'active',
                    ])
                    ->assertRedirect('/users');

                $this->assertSame($roleSlug, User::where('email', $email)->firstOrFail()->role->slug);
            }
        }
    }

    public function test_created_specialized_accounts_use_admin_portal_and_reach_their_dashboards(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();

        foreach ([Role::ADMISSIONS_OFFICER, Role::FINANCE_OFFICER, Role::REGISTRAR, Role::HR_OFFICER, Role::LIBRARIAN] as $roleSlug) {
            $role = Role::where('slug', $roleSlug)->firstOrFail();
            $email = 'created-' . $roleSlug . '@example.com';

            $this->actingAs($admin)->post('/users', [
                'name' => 'Created ' . $role->name,
                'email' => $email,
                'password' => 'password',
                'role_id' => $role->id,
                'status' => 'active',
            ])->assertRedirect('/users');

            $created = User::where('email', $email)->firstOrFail();
            $this->post('/logout');
            $this->post(route('admin.login.post'), ['email' => $email, 'password' => 'password'])
                ->assertRedirect(route('dashboard'));
            $this->assertAuthenticatedAs($created);
            $this->get('/dashboard')->assertViewIs('dashboard.' . str_replace('_', '-', $roleSlug));
            $this->post('/logout');
            $this->post(route('university.login.post'), ['email' => $email, 'password' => 'password'])
                ->assertRedirect(route('university.login'));
            $this->assertGuest();
        }
    }

    public function test_staff_reaches_staff_dashboard_and_cannot_access_admin_surfaces(): void
    {
        $this->seed();
        $staffRole = Role::where('slug', Role::STAFF)->firstOrFail();
        $staff = User::factory()->create([
            'role_id' => $staffRole->id,
            'status' => 'active',
            'is_active' => true,
        ]);

        $this->post('/login', [
            'email' => $staff->email,
            'password' => 'password',
        ])->assertRedirect(route('hemis'));

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Staff Workspace')
            ->assertDontSee('Admin Dashboard');

        $this->get('/users')->assertForbidden();
        $this->get('/settings')->assertForbidden();
    }

    public function test_admin_cannot_assign_super_admin_role_when_editing_an_account(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();
        $target = User::where('email', 'teacher1@school.com')->firstOrFail();
        $superAdminRole = Role::where('slug', Role::SUPER_ADMIN)->firstOrFail();

        $this->actingAs($admin)
            ->put('/users/' . $target->id, [
                'name' => $target->name,
                'email' => $target->email,
                'phone' => $target->phone,
                'role_id' => $superAdminRole->id,
                'status' => 'active',
            ])
            ->assertForbidden();

        $this->assertSame(Role::TEACHER, $target->fresh()->role->slug);
    }

    public function test_staff_can_manage_student_and_parent_records_without_privileged_access(): void
    {
        $this->seed();
        $staffRole = Role::where('slug', Role::STAFF)->firstOrFail();
        $staff = User::factory()->create([
            'role_id' => $staffRole->id,
            'status' => 'active',
            'is_active' => true,
        ]);
        $student = Student::firstOrFail();
        $parent = $student->guardians()->first();

        $this->actingAs($staff)
            ->get('/students')
            ->assertOk()
            ->assertSee('Students Directory')
            ->assertSee('Add New Student');

        $this->actingAs($staff)
            ->get('/students/' . $student->id)
            ->assertOk()
            ->assertSee($student->user->name)
            ->assertDontSee('Fee Billing & Payment History')
            ->assertSee('Edit Profile');

        $this->actingAs($staff)
            ->get('/parents')
            ->assertOk()
            ->assertSee('Parents & Guardians')
            ->assertSee('Add Parent/Guardian');

        if ($parent) {
            $this->actingAs($staff)
                ->get('/parents/' . $parent->id)
                ->assertOk()
                ->assertSee($parent->user->name)
                ->assertSee('Edit Details');
        }

        $this->actingAs($staff)->get('/students/create')->assertOk();
        $this->actingAs($staff)->get('/parents/create')->assertOk();
        $this->actingAs($staff)->delete('/students/' . $student->id)->assertForbidden();
        if ($parent) {
            $this->actingAs($staff)->delete('/parents/' . $parent->id)->assertForbidden();
        }
        $this->actingAs($staff)->get('/facilities#campus-access')->assertOk();
    }

    public function test_staff_can_create_and_update_student_and_parent_records_without_changing_passwords(): void
    {
        $this->seed();
        $staff = User::where('email', 'staff@school.com')->first();
        if (!$staff) {
            $staffRole = Role::where('slug', Role::STAFF)->firstOrFail();
            $staff = User::factory()->create([
                'role_id' => $staffRole->id,
                'status' => 'active',
                'is_active' => true,
            ]);
        }
        $schoolClass = \App\Models\SchoolClass::firstOrFail();
        $section = \App\Models\Section::where('school_class_id', $schoolClass->id)->firstOrFail();
        $suffix = Str::lower(Str::random(8));
        $studentEmail = "staff-created-student-{$suffix}@example.com";

        $this->actingAs($staff)->post('/students', [
            'name' => 'Staff Created Student',
            'email' => $studentEmail,
            'password' => 'studentpass',
            'phone' => '+1-555-0111',
            'roll_number' => "STAFF-{$suffix}",
            'admission_number' => "STAFF-ADM-{$suffix}",
            'school_class_id' => $schoolClass->id,
            'section_id' => $section->id,
            'admission_date' => now()->toDateString(),
            'status' => 'active',
        ])->assertRedirect('/students');

        $student = User::where('email', $studentEmail)->firstOrFail();
        $studentRecord = $student->student;
        $this->assertTrue(Hash::check('studentpass', $student->password));

        $this->actingAs($staff)->put('/students/' . $studentRecord->id, [
            'name' => 'Updated Staff Student',
            'email' => $studentEmail,
            'phone' => '+1-555-0222',
            'roll_number' => $studentRecord->roll_number,
            'school_class_id' => $schoolClass->id,
            'section_id' => $section->id,
            'admission_date' => $studentRecord->admission_date->toDateString(),
            'status' => 'graduated',
            'address' => 'Updated address',
        ])->assertRedirect('/students');

        $this->assertSame('active', $studentRecord->fresh()->status);
        $this->assertTrue(Hash::check('studentpass', $student->fresh()->password));

        $parentEmail = "staff-created-parent-{$suffix}@example.com";
        $this->actingAs($staff)->post('/parents', [
            'name' => 'Staff Created Parent',
            'email' => $parentEmail,
            'phone' => '+1-555-0333',
            'password' => 'parentpass',
            'relationship' => 'guardian',
            'student_ids' => [$studentRecord->id],
        ])->assertRedirect('/parents');

        $parentUser = User::where('email', $parentEmail)->firstOrFail();
        $parent = $parentUser->guardian;
        $this->assertTrue($parent->students->contains($studentRecord));
        $this->assertTrue(Hash::check('parentpass', $parentUser->password));

        $this->actingAs($staff)->put('/parents/' . $parent->id, [
            'name' => 'Updated Staff Parent',
            'email' => $parentEmail,
            'phone' => '+1-555-0444',
            'relationship' => 'guardian',
            'student_ids' => [$studentRecord->id],
        ])->assertRedirect('/parents');

        $this->assertTrue(Hash::check('parentpass', $parentUser->fresh()->password));
        $this->assertSame('Staff Created Parent', $parentUser->fresh()->name);
        $this->assertSame('+1-555-0333', $parentUser->fresh()->phone);
    }
}
