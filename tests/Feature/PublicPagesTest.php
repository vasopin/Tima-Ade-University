<?php

namespace Tests\Feature;

use App\Models\Notice;
use App\Models\Role;
use App\Models\User;
use App\Models\AdmissionApplication;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use \Tests\Concerns\ForceRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use \Tests\Concerns\ForceRefreshDatabase;

    public function test_all_public_pages_load_successfully(): void
    {
        $this->seed();

        $routes = [
            '/',
            '/about',
            '/academics',
            '/faculty',
            '/admissions',
            '/contact',
            '/notices',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }

        $this->get('/register')->assertRedirect('/login');
        $this->get(route('public.home'))
            ->assertSee('University Portal')
            ->assertSee(route('university.login'), false)
            ->assertSee('University Admin Sign In')
            ->assertSee(route('admin.login'), false);

        $this->get(route('public.home'))
            ->assertSee('aria-label="Sign in to the University Portal">University Portal Sign In', false)
            ->assertSee('aria-label="Sign in to the University Admin Portal">University Admin Sign In', false);
    }

    public function test_university_administration_portal_is_restricted_to_authorized_roles(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();
        $superAdmin = User::whereHas('role', fn ($query) => $query->where('slug', 'super_admin'))->firstOrFail();
        $student = User::whereHas('role', fn ($query) => $query->where('slug', 'student'))->firstOrFail();
        $teacher = User::whereHas('role', fn ($query) => $query->where('slug', 'teacher'))->firstOrFail();
        $parent = User::whereHas('role', fn ($query) => $query->where('slug', 'parent'))->firstOrFail();
        $staffRole = Role::where('slug', Role::STAFF)->firstOrFail();
        $staff = User::factory()->create(['role_id' => $staffRole->id, 'status' => 'active', 'is_active' => true]);

        $this->get(route('hemis'))->assertRedirect(route('login'));
        $this->actingAs($admin)->get(route('hemis'))->assertOk()->assertSee('University Administration Portal');
        $this->actingAs($admin)->get(route('hemis'))->assertSee('href="' . route('dashboard') . '"', false)->assertSee('Admin Dashboard');
        $this->actingAs($superAdmin)->get(route('hemis'))->assertOk()->assertSee('University Administration Portal');
        $this->actingAs($staff)->get(route('hemis'))->assertOk()->assertSee('University Administration Portal');
        foreach ([$student, $teacher, $parent] as $user) {
            $this->actingAs($user)->get(route('hemis'))
            ->assertForbidden();
        }

        $studentRecord = \App\Models\Student::firstOrFail();
        $this->actingAs($staff)->post(route('hemis.lookup'), ['student_id' => $studentRecord->student_id])
            ->assertOk()
            ->assertSee('Authorized record found')
            ->assertSee($studentRecord->student_id);
        $this->actingAs($teacher)->post(route('hemis.lookup'), ['student_id' => $studentRecord->admission_number])
            ->assertForbidden();

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertSee('Applications')
            ->assertDontSee('Student LMS');
        $this->actingAs($student)->get(route('dashboard'))
            ->assertSee('Student LMS')
            ->assertDontSee('Applications');
        $this->actingAs($teacher)->get(route('dashboard'))
            ->assertSee('Teacher LMS')
            ->assertDontSee('Applications');
        $this->actingAs($parent)->get(route('dashboard'))
            ->assertSee('University Portal')
            ->assertDontSee('Applications');
    }

    public function test_results_remains_available_to_role_scoped_users(): void
    {
        $this->seed();
        foreach (['student', 'teacher', 'parent', 'super_admin', 'admin'] as $role) {
            $user = User::whereHas('role', fn ($query) => $query->where('slug', $role))->firstOrFail();
            $this->actingAs($user)->get(route('exams.results'))->assertOk();
        }

        $staffRole = Role::where('slug', Role::STAFF)->firstOrFail();
        $staff = User::factory()->create(['role_id' => $staffRole->id, 'status' => 'active', 'is_active' => true]);
        $this->actingAs($staff)->get(route('exams.results'))->assertForbidden();
    }

    public function test_portal_links_preserve_context_through_the_existing_login_flow(): void
    {
        $this->get(route('hemis'))->assertRedirect(route('login'));
        $this->get(route('login'))->assertOk()->assertSee('Tima-Ade University');

        $this->flushSession();
        $this->get(route('exams.results'))->assertRedirect(route('login'));
        $this->get(route('login'))->assertOk()
            ->assertSee('Tima-Ade University')
            ->assertSee('University Portal')
            ->assertSee('EMAIL ADDRESS')
            ->assertSee('Login');
    }

    public function test_portal_sign_in_entry_points_are_distinct_and_share_authentication(): void
    {
        $this->get(route('university.login'))
            ->assertOk()
            ->assertSee('University Portal')
            ->assertSee('For students, teachers, and parents/guardians')
            ->assertSee(route('university.login.post'), false)
            ->assertSee(route('admin.login'), false);

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('University Admin Portal')
            ->assertSee('For super administrators, administrators, and staff')
            ->assertSee(route('admin.login.post'), false)
            ->assertSee(route('university.login'), false);
    }

    public function test_public_apply_now_uses_the_existing_admissions_inquiry_form(): void
    {
        $this->get(route('public.home'))
            ->assertOk()
            ->assertSee(route('public.apply'), false)
            ->assertSee('Apply Now');

        $this->get(route('public.admissions'))
            ->assertOk()
            ->assertSee(route('public.admissions.apply'), false)
            ->assertSee('Online admission form');
    }

    public function test_apply_page_shows_only_active_programs_with_academic_context(): void
    {
        $this->seed();
        $faculty = Faculty::create(['name' => 'Faculty of Science', 'code' => 'FOS', 'is_active' => true]);
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Department of Biology', 'code' => 'BIO', 'is_active' => true]);
        $active = Program::create([
            'department_id' => $department->id,
            'name' => 'B.Sc. Biology',
            'code' => 'BSC-BIO',
            'degree_type' => 'Bachelor',
            'is_active' => true,
        ]);
        $inactive = Program::create([
            'department_id' => $department->id,
            'name' => 'B.Sc. Archived Biology',
            'code' => 'BSC-ABIO',
            'degree_type' => 'Bachelor',
            'is_active' => false,
        ]);

        $this->get(route('public.apply'))
            ->assertOk()
            ->assertSee($active->name)
            ->assertSee($active->code)
            ->assertSee($active->degree_type)
            ->assertDontSee($inactive->name)
            ->assertDontSee($inactive->code);
    }

    public function test_official_program_catalog_is_seeded_and_available_to_apply_now(): void
    {
        $this->seed();

        $programs = Program::query()->where('is_active', true)->get()->map(fn ($program) => trim($program->name));

        $this->assertCount(51, $programs->unique()->values());
        $this->assertTrue($programs->contains('Accounting'));
        $this->assertTrue($programs->contains('Economics'));
        $this->assertCount(1, Program::query()->whereRaw('LOWER(TRIM(name)) = ?', ['economics'])->get());

        $this->get(route('public.apply'))
            ->assertOk()
            ->assertSee('Accounting')
            ->assertSee('Computer Science')
            ->assertSee('Logistics and Supply Chain Management');
    }

    public function test_apply_page_explains_when_no_active_programs_are_configured(): void
    {
        $this->seed();
        Program::query()->update(['is_active' => false]);

        $this->get(route('public.apply'))
            ->assertOk()
            ->assertSee('No programs are currently available')
            ->assertSee('Applications for academic programs are not yet open.')
            ->assertSee('disabled', false);
    }

    public function test_home_page_uses_html5_promo_video_when_local_mp4_exists(): void
    {
        $videoPath = public_path('videos/home.mp4');
        if (! is_dir(dirname($videoPath))) {
            mkdir(dirname($videoPath), 0777, true);
        }
        file_put_contents($videoPath, 'placeholder-video');

        $this->get(route('public.home'))
            ->assertOk()
            ->assertSee('<video', false)
            ->assertSee('autoplay', false)
            ->assertSee('muted', false)
            ->assertSee('loop', false)
            ->assertSee('playsinline', false)
            ->assertDontSee('youtube-nocookie.com', false);

        @unlink($videoPath);
    }

    public function test_apply_workflow_requires_steps_and_creates_submitted_application(): void
    {
        $this->seed();
        $this->post(route('public.apply.education'), [])->assertStatus(422);

        $this->post(route('public.apply.basic'), [
            'name' => 'Applicant Example', 'email' => 'applicant@example.com', 'phone' => '123456789',
            'grade_interested' => 'Grade 10', 'message' => 'Application note',
        ])->assertRedirect(route('public.apply', ['step' => 2]));
        $this->post(route('public.apply.education'), [
            'previous_institution' => 'Previous School', 'qualification' => 'Secondary',
            'field_of_study' => 'General', 'graduation_year' => '2025',
        ])->assertRedirect(route('public.apply', ['step' => 3]));
        $this->post(route('public.apply.documents'))->assertRedirect(route('public.apply', ['step' => 4]));
        $this->post(route('public.apply.submit'), ['confirmation' => '1'])->assertOk()->assertSee('Application Submitted');

        $application = AdmissionApplication::where('email', 'applicant@example.com')->firstOrFail();
        $this->assertSame('submitted', $application->status);
        $this->assertNotEmpty($application->reference);
    }

    public function test_valid_program_ids_are_accepted_and_invalid_program_ids_are_rejected(): void
    {
        $this->seed();

        $faculty = Faculty::query()->firstOrCreate([
            'name' => 'Faculty of Computing',
            'code' => 'FOC',
            'is_active' => true,
        ]);

        $department = Department::query()->firstOrCreate([
            'faculty_id' => $faculty->id,
            'name' => 'Department of Software Engineering',
            'code' => 'DSE',
            'is_active' => true,
        ]);

        $program = Program::query()->firstOrCreate([
            'code' => 'BSSE-TEST',
        ], [
            'department_id' => $department->id,
            'name' => 'B.Sc. Software Engineering',
            'degree_type' => 'Bachelor',
            'duration_years' => 4,
            'is_active' => true,
        ]);

        $this->post(route('public.apply.basic'), [
            'name' => 'Program Applicant',
            'email' => 'program@example.com',
            'phone' => '1234567890',
            'program_id' => $program->id,
            'message' => 'Program-aware application',
        ])->assertRedirect(route('public.apply', ['step' => 2]));

        $this->from(route('public.apply', ['step' => 1]))
            ->post(route('public.apply.basic'), [
                'name' => 'Invalid Program Applicant',
                'email' => 'invalid-program@example.com',
                'phone' => '1234567891',
                'program_id' => 999999,
                'message' => 'Bad program selection',
            ])
            ->assertRedirect(route('public.apply', ['step' => 1]))
            ->assertSessionHasErrors('program_id');
    }

    public function test_tampered_admission_sessions_cannot_submit_inactive_programs(): void
    {
        $this->seed();

        $faculty = Faculty::query()->firstOrCreate([
            'name' => 'Faculty of Health Sciences',
            'code' => 'FHS',
            'is_active' => true,
        ]);

        $department = Department::query()->firstOrCreate([
            'faculty_id' => $faculty->id,
            'name' => 'Department of Public Health',
            'code' => 'DPH',
            'is_active' => true,
        ]);

        $program = Program::query()->create([
            'department_id' => $department->id,
            'name' => 'B.Sc. Public Health',
            'code' => 'BSC-PH',
            'degree_type' => 'Bachelor',
            'duration_years' => 4,
            'is_active' => false,
        ]);

        session()->put('admission_application', [
            'basic' => [
                'name' => 'Tampered Applicant',
                'email' => 'tampered@example.com',
                'phone' => '2345678901',
                'program_id' => $program->id,
                'grade_interested' => $program->name,
                'message' => 'Attempted stale program submit.',
            ],
            'education' => [
                'previous_institution' => 'Test School',
                'qualification' => 'Secondary',
                'field_of_study' => 'Health',
                'graduation_year' => 2025,
            ],
            'documents' => [],
        ]);

        $this->from(route('public.apply', ['step' => 4]))
            ->post(route('public.apply.submit'), ['confirmation' => '1'])
            ->assertRedirect(route('public.apply', ['step' => 4]))
            ->assertSessionHasErrors('program_id');

        $this->assertDatabaseMissing('admission_applications', ['email' => 'tampered@example.com']);
    }

    public function test_only_administrators_can_review_and_decide_applications(): void
    {
        $this->seed();
        $application = AdmissionApplication::create([
            'name' => 'Review Applicant', 'email' => 'review@example.com', 'phone' => '123456789',
            'grade_interested' => 'Grade 10', 'education' => [], 'documents' => [],
        ]);
        $student = User::whereHas('role', fn ($query) => $query->where('slug', 'student'))->firstOrFail();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();

        $this->actingAs($student)->get(route('admin.applications'))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.applications'))->assertOk()->assertSee($application->reference);
        $this->actingAs($admin)->post(route('admin.applications.approve', $application))->assertRedirect();
        $this->assertSame('approved', $application->fresh()->status);
    }

    public function test_admin_can_publish_notice_and_public_board_displays_it(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.notices.store'), [
            'title' => 'Library Hours Updated',
            'content' => 'The library is open until 9 PM during examinations.',
            'category' => 'Academic',
            'published_date' => today()->toDateString(),
            'is_pinned' => '1',
            'is_active' => '1',
        ])->assertRedirect(route('admin.notices.index'));

        $notice = Notice::where('title', 'Library Hours Updated')->firstOrFail();
        $this->assertSame('Academic', $notice->category);
        $this->assertTrue($notice->is_pinned);
        $this->assertTrue($notice->is_active);

        $this->get(route('public.notices'))
            ->assertOk()
            ->assertSee('Library Hours Updated');

        $notice->update(['is_active' => false]);
        $this->get(route('public.notices.single', $notice))->assertNotFound();
    }

    public function test_future_dated_active_notice_is_not_publicly_visible(): void
    {
        $this->seed();
        $notice = Notice::create([
            'title' => 'Future Academic Calendar',
            'content' => 'This notice is scheduled for a later date.',
            'category' => 'Academic',
            'published_date' => today()->addDay(),
            'is_pinned' => false,
            'is_active' => true,
        ]);

        $this->get(route('public.notices'))
            ->assertOk()
            ->assertDontSee('Future Academic Calendar');
        $this->get(route('public.notices.single', $notice))->assertNotFound();
    }

    public function test_admin_can_upload_video_notice_and_public_board_renders_it(): void
    {
        Storage::fake('public');
        $this->seed();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.notices.store'), [
            'title' => 'Graduation Ceremony 2026',
            'content' => 'Join us as we celebrate our graduating students.',
            'category' => 'Graduation',
            'published_date' => today()->toDateString(),
            'status' => 'published',
            'audience' => 'everyone',
            'is_pinned' => '1',
            'video' => UploadedFile::fake()->create('graduation.mp4', 1024, 'video/mp4'),
        ])->assertRedirect(route('admin.notices.index'));

        $notice = Notice::where('title', 'Graduation Ceremony 2026')->firstOrFail();
        $this->assertSame('published', $notice->status);
        Storage::disk('public')->assertExists($notice->video_path);
        $this->get(route('public.notices.single', $notice))->assertOk()->assertSee('video/mp4', false);
    }

    public function test_teacher_cannot_manage_video_notices(): void
    {
        $this->seed();
        $teacher = User::whereHas('role', fn ($query) => $query->where('slug', 'teacher'))->firstOrFail();

        $this->actingAs($teacher)->get(route('admin.notices.create'))->assertForbidden();
    }

    public function test_admin_can_open_video_management_page_and_teacher_cannot(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();
        $teacher = User::whereHas('role', fn ($query) => $query->where('slug', 'teacher'))->firstOrFail();

        $this->actingAs($admin)->get(route('admin.notices.videos'))
            ->assertOk()->assertSee('Notice Board Video Management')->assertSee('Upload Video');
        $this->actingAs($teacher)->get(route('admin.notices.videos'))->assertForbidden();
    }
}
