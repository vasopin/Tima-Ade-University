<?php

namespace Tests\Feature;

use \Tests\Concerns\ForceRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class AuthTest extends TestCase
{
    use \Tests\Concerns\ForceRefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->seed();
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_login_screen(): void
    {
        $this->seed();
        $user = User::where('email', 'admin@school.com')->first();

        $response = $this->post('/login', [
            'email' => 'admin@school.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('hemis'));
    }

    public function test_login_pages_include_accessible_portal_visual_markup(): void
    {
        $this->seed();

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('University Admin Portal')
            ->assertSee('auth-visual')
            ->assertSee('prefers-reduced-motion');

        $this->get(route('university.login'))
            ->assertOk()
            ->assertSee('University Portal')
            ->assertSee('auth-visual')
            ->assertSee('prefers-reduced-motion');
    }

    public function test_login_returns_each_role_to_its_separate_portal(): void
    {
        $this->seed();
        $student = User::where('email', 'student1@school.com')->firstOrFail();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();

        $this->post('/login', ['email' => $student->email, 'password' => 'password'])
            ->assertRedirect(route('student.welcome'));

        $this->post('/logout');
        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('hemis'));
    }

    public function test_non_admin_roles_can_view_their_welcome_screen(): void
    {
        $this->seed();
        $teacher = User::where('email', 'teacher1@school.com')->firstOrFail();

        $this->post('/login', ['email' => $teacher->email, 'password' => 'password'])
            ->assertRedirect(route('teacher.welcome'));

        $this->get(route('teacher.welcome'))
            ->assertOk()
            ->assertSee('Go to Dashboard');
    }

    public function test_authenticated_admin_opening_admin_sign_in_reaches_administration_portal(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();
        $staffRole = Role::where('slug', Role::STAFF)->firstOrFail();
        $staff = User::factory()->create(['role_id' => $staffRole->id, 'status' => 'active', 'is_active' => true]);

        $this->actingAs($admin)->get(route('admin.login'))->assertRedirect(route('hemis'));
        $this->actingAs($staff)->get(route('admin.login'))->assertRedirect(route('staff.welcome'));
    }

    public function test_every_role_can_only_sign_in_through_its_authorized_portal(): void
    {
        $this->seed();
        $users = [
            'super_admin' => User::whereHas('role', fn ($query) => $query->where('slug', 'super_admin'))->firstOrFail(),
            'admin' => User::where('email', 'admin@school.com')->firstOrFail(),
            'staff' => User::factory()->create(['role_id' => Role::where('slug', Role::STAFF)->value('id'), 'status' => 'active', 'is_active' => true]),
            'student' => User::where('email', 'student1@school.com')->firstOrFail(),
            'teacher' => User::where('email', 'teacher1@school.com')->firstOrFail(),
            'parent' => User::where('email', 'parent1@school.com')->firstOrFail(),
        ];
        $users['super_admin']->update(['password' => Hash::make('password')]);

        foreach (['super_admin', 'admin'] as $role) {
            $this->get(route('admin.login'));
            $this->post(route('login.post'), ['email' => $users[$role]->email, 'password' => 'password'])
                ->assertRedirect(route('hemis'));
            $this->post(route('logout'));
        }

        $this->get(route('admin.login'));
        $this->post(route('login.post'), ['email' => $users['staff']->email, 'password' => 'password'])
            ->assertRedirect(route('staff.welcome'));
        $this->post(route('logout'));

        foreach (['student', 'teacher', 'parent'] as $role) {
            $this->get(route('admin.login'));
            $this->post(route('login.post'), ['email' => $users[$role]->email, 'password' => 'password'])
                ->assertRedirect(route('admin.login'));
            $this->assertGuest();
            $this->get(route('admin.login'))
                ->assertSee('Please use the University Portal Sign-In.')
                ->assertSee(route('university.login'));
        }

        foreach (['student', 'teacher', 'parent'] as $role) {
            $this->get(route('university.login'));
            $this->post(route('login.post'), ['email' => $users[$role]->email, 'password' => 'password'])
                ->assertRedirect(route($role . '.welcome'));
            $this->post(route('logout'));
        }

        foreach (['super_admin', 'admin', 'staff'] as $role) {
            $this->get(route('university.login'));
            $this->post(route('login.post'), ['email' => $users[$role]->email, 'password' => 'password'])
                ->assertRedirect(route('university.login'));
            $this->assertGuest();
            $this->get(route('university.login'))
                ->assertSee('You cannot log in through the University Portal Sign-In. Please use the University Admin Sign-In.')
                ->assertSee(route('admin.login'));
        }
    }

    public function test_direct_portal_login_endpoints_enforce_role_separation(): void
    {
        $this->seed();
        $roles = [
            'super_admin' => User::whereHas('role', fn ($query) => $query->where('slug', 'super_admin'))->firstOrFail(),
            'admin' => User::where('email', 'admin@school.com')->firstOrFail(),
            'staff' => User::factory()->create(['role_id' => Role::where('slug', Role::STAFF)->value('id'), 'status' => 'active', 'is_active' => true]),
            'admissions_officer' => User::factory()->create(['role_id' => Role::firstOrCreate(['slug' => Role::ADMISSIONS_OFFICER], ['name' => 'Admissions Officer'])->id, 'status' => 'active', 'is_active' => true]),
            'finance_officer' => User::factory()->create(['role_id' => Role::firstOrCreate(['slug' => Role::FINANCE_OFFICER], ['name' => 'Finance Officer'])->id, 'status' => 'active', 'is_active' => true]),
            'registrar' => User::factory()->create(['role_id' => Role::firstOrCreate(['slug' => Role::REGISTRAR], ['name' => 'Registrar'])->id, 'status' => 'active', 'is_active' => true]),
            'hr_officer' => User::factory()->create(['role_id' => Role::firstOrCreate(['slug' => Role::HR_OFFICER], ['name' => 'HR Officer'])->id, 'status' => 'active', 'is_active' => true]),
            'librarian' => User::factory()->create(['role_id' => Role::firstOrCreate(['slug' => Role::LIBRARIAN], ['name' => 'Librarian'])->id, 'status' => 'active', 'is_active' => true]),
            'teacher' => User::where('email', 'teacher1@school.com')->firstOrFail(),
            'student' => User::where('email', 'student1@school.com')->firstOrFail(),
            'parent' => User::where('email', 'parent1@school.com')->firstOrFail(),
        ];
        $roles['super_admin']->update(['password' => Hash::make('password')]);

        foreach (['teacher', 'student', 'parent'] as $role) {
            $this->post(route('university.login.post'), ['email' => $roles[$role]->email, 'password' => 'password'])
                ->assertRedirect(route($role . '.welcome'));
            $this->assertAuthenticatedAs($roles[$role]);
            $this->post('/logout');
        }

        foreach (['super_admin', 'admin'] as $role) {
            $this->post(route('admin.login.post'), ['email' => $roles[$role]->email, 'password' => 'password'])
                ->assertRedirect(route('hemis'));
            $this->assertAuthenticatedAs($roles[$role]);
            $this->post('/logout');
        }

        $this->post(route('admin.login.post'), ['email' => $roles['staff']->email, 'password' => 'password'])
            ->assertRedirect(route('staff.welcome'));
        $this->assertAuthenticatedAs($roles['staff']);
        $this->post('/logout');

        foreach (['admissions_officer', 'finance_officer', 'registrar', 'hr_officer', 'librarian'] as $role) {
            $this->post(route('admin.login.post'), ['email' => $roles[$role]->email, 'password' => 'password'])
                ->assertRedirect(route($role . '.welcome'));
            $this->assertAuthenticatedAs($roles[$role]);
            $this->post('/logout');
        }

        foreach (['super_admin', 'admin', 'staff', 'admissions_officer', 'finance_officer', 'registrar', 'hr_officer', 'librarian'] as $role) {
            $this->post(route('university.login.post'), ['email' => $roles[$role]->email, 'password' => 'password'])
                ->assertRedirect(route('university.login'));
            $this->assertGuest();
        }

        foreach (['teacher', 'student', 'parent'] as $role) {
            $this->post(route('admin.login.post'), ['email' => $roles[$role]->email, 'password' => 'password'])
                ->assertRedirect(route('admin.login'));
            $this->assertGuest();
        }
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $this->seed();
        $user = User::where('email', 'admin@school.com')->first();

        $this->post('/login', [
            'email' => 'admin@school.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors([
            'auth' => 'Your email or password is incorrect. Please check your details and try again.',
        ]);

        $this->assertGuest();
    }

    public function test_invalid_password_error_is_displayed_on_each_login_flow(): void
    {
        $this->seed();
        $student = User::where('email', 'student1@school.com')->firstOrFail();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();
        $message = 'Your email or password is incorrect. Please check your details and try again.';

        $this->from(route('university.login'))
            ->post(route('university.login.post'), [
                'email' => $student->email,
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('university.login'))
            ->assertSessionHasErrors(['auth' => $message]);

        $this->from(route('admin.login'))
            ->post(route('admin.login.post'), [
                'email' => $admin->email,
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors(['auth' => $message]);
    }

    public function test_login_validation_messages_are_attached_to_the_correct_fields(): void
    {
        $this->seed();

        $this->from(route('university.login'))
            ->post(route('university.login.post'), ['email' => '', 'password' => 'password'])
            ->assertRedirect(route('university.login'))
            ->assertSessionHasErrors(['email' => 'Please enter your email.'])
            ->assertSessionDoesntHaveErrors('password');

        $this->from(route('university.login'))
            ->post(route('university.login.post'), ['email' => 'not-an-email', 'password' => 'password'])
            ->assertRedirect(route('university.login'))
            ->assertSessionHasErrors(['email' => 'Please enter a valid email address.'])
            ->assertSessionDoesntHaveErrors('password');

        $this->from(route('university.login'))
            ->post(route('university.login.post'), ['email' => 'student1@school.com', 'password' => ''])
            ->assertRedirect(route('university.login'))
            ->assertSessionHasErrors(['password' => 'Please enter your password.'])
            ->assertSessionDoesntHaveErrors('email');
    }

    public function test_users_can_authenticate_with_normalized_email_input(): void
    {
        $this->seed();

        $this->post('/login', [
            'email' => '  ADMIN@SCHOOL.COM  ',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs(User::where('email', 'admin@school.com')->first());
    }
    public function test_unknown_email_cannot_authenticate(): void
    {
        $this->seed();

        $this->post('/login', [
            'email' => 'unknown-user@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_login_uses_the_web_guard_and_users_provider(): void
    {
        $this->assertSame('web', config('auth.defaults.guard'));
        $this->assertSame('eloquent', config('auth.guards.web.driver') === 'session' ? config('auth.providers.users.driver') : null);
        $this->assertSame(User::class, config('auth.providers.users.model'));
    }

    public function test_public_registration_is_redirected_to_login(): void
    {
        $this->get('/register')->assertRedirect('/login');
    }

    public function test_public_registration_post_does_not_create_an_account(): void
    {
        $this->seed();
        $email = 'public-registration-blocked@example.com';
        $userCount = User::count();

        $this->post('/register', [
            'name' => 'Public Registration Attempt',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'role_slug' => 'student',
            'terms' => 'on',
        ])->assertRedirect('/login');

        $this->assertSame($userCount, User::count());
        $this->assertDatabaseMissing('users', ['email' => $email]);
    }

    public function test_public_navigation_does_not_expose_registration(): void
    {
        $this->get('/login')->assertDontSee('Create an Account');
        $this->get('/')->assertDontSee('>Register<');
    }

    public function test_non_admin_personal_information_is_read_only_but_password_changes_remain_available(): void
    {
        $this->seed();
        $staffRole = Role::where('slug', Role::STAFF)->firstOrFail();
        $staff = User::factory()->create([
            'role_id' => $staffRole->id,
            'status' => 'active',
            'is_active' => true,
            'name' => 'Staff Member',
            'email' => 'staff-profile@example.com',
            'phone' => '+1-555-0700',
        ]);

        $this->actingAs($staff)
            ->get('/profile')
            ->assertOk()
            ->assertSee('Personal information is managed by an administrator.')
            ->assertSee('readonly-name')
            ->assertDontSee('name="avatar"');

        $this->actingAs($staff)->put('/profile', [
            'name' => 'Changed Staff Name',
            'email' => 'changed-staff@example.com',
            'phone' => '+1-555-0799',
        ])->assertForbidden();

        $this->assertSame('Staff Member', $staff->fresh()->name);
        $this->assertSame('staff-profile@example.com', $staff->fresh()->email);
        $this->assertSame('+1-555-0700', $staff->fresh()->phone);

        $this->actingAs($staff)->put('/profile/password', [
            'current_password' => 'password',
            'password' => 'new-staff-password',
            'password_confirmation' => 'new-staff-password',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('new-staff-password', $staff->fresh()->password));
    }

    public function test_admin_can_update_another_users_personal_information_and_photo(): void
    {
        Storage::fake('public');
        $this->seed();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();
        $teacher = User::where('email', 'teacher1@school.com')->firstOrFail();

        $this->actingAs($admin)->put('/users/' . $teacher->id, [
            'name' => 'Updated Faculty Name',
            'email' => $teacher->email,
            'phone' => '+1-555-0788',
            'role_id' => $teacher->role_id,
            'status' => 'active',
            'avatar' => UploadedFile::fake()->create('faculty.jpg', 100, 'image/jpeg'),
        ])->assertRedirect('/users');

        $updated = $teacher->fresh();
        $this->assertSame('Updated Faculty Name', $updated->name);
        $this->assertSame('+1-555-0788', $updated->phone);
        $this->assertNotNull($updated->avatar);
        Storage::disk('public')->assertExists($updated->avatar);
    }

    public function test_teacher_student_and_parent_personal_information_is_read_only(): void
    {
        $this->seed();

        foreach (['teacher1@school.com', 'student1@school.com', 'parent1@school.com'] as $email) {
            $user = User::where('email', $email)->firstOrFail();

            $this->actingAs($user)->put('/profile', [
                'name' => 'Unauthorized Personal Change',
                'email' => 'unauthorized-' . $user->id . '@example.com',
                'phone' => '+1-555-0799',
            ])->assertForbidden();

            $unchanged = $user->fresh();
            $this->assertNotSame('Unauthorized Personal Change', $unchanged->name);
            $this->assertSame($email, $unchanged->email);
        }
    }
}
