<?php

namespace Tests\Feature;

use \Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\AdmissionApplication;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AdmissionsOfficerAccessTest extends TestCase
{
    use ForceRefreshDatabase;

    private function createAdmissionsOfficer(): User
    {
        $role = Role::where('slug', 'admissions_officer')->firstOrCreate([
            'name' => 'Admissions Officer',
            'slug' => 'admissions_officer',
        ]);
        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_admissions_officer_can_access_dashboard(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();

        $response = $this->actingAs($officer)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.admissions-officer');
    }

    public function test_dashboard_shows_real_admissions_work_metrics(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();
        AdmissionApplication::factory()->create([
            'status' => 'submitted',
            'documents' => [['name' => 'transcript.pdf', 'path' => 'admission-documents/transcript.pdf', 'status' => 'submitted']],
            'created_at' => now(),
        ]);
        AdmissionApplication::factory()->create(['status' => 'under_review', 'created_at' => now()->subDay()]);

        $this->actingAs($officer)->get('/dashboard')
            ->assertOk()
            ->assertSee('New Applications')
            ->assertSee('Documents Pending')
            ->assertSee('Applications needing review')
            ->assertSee('Submitted');
    }

    public function test_admissions_officer_can_access_applications_list(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();

        $response = $this->actingAs($officer)->get('/admin/applications');
        $response->assertStatus(200);
    }

    public function test_admissions_officer_reports_page_is_separate_and_uses_real_filtered_data(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();
        $approved = AdmissionApplication::factory()->create(['status' => 'approved', 'grade_interested' => 'Science']);
        AdmissionApplication::factory()->create(['status' => 'submitted', 'grade_interested' => 'Arts']);

        $this->actingAs($officer)->get(route('admin.reports', ['status' => 'approved']))
            ->assertOk()
            ->assertViewIs('admin.applications.reports')
            ->assertSee('Admissions Reports')
            ->assertSee('Total Applicants')
            ->assertSee($approved->reference)
            ->assertDontSee('Admissions Applications');

        $this->actingAs(User::where('email', 'student1@school.com')->firstOrFail())
            ->get(route('admin.reports'))
            ->assertForbidden();
    }

    public function test_admissions_officer_reviews_page_is_separate_and_lists_reviewable_applications(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();
        $submitted = AdmissionApplication::factory()->create(['name' => 'Submitted Review Applicant', 'status' => 'submitted']);
        $underReview = AdmissionApplication::factory()->create(['name' => 'Active Review Applicant', 'status' => 'under_review']);
        $approved = AdmissionApplication::factory()->create(['name' => 'Completed Applicant', 'status' => 'approved']);

        $response = $this->actingAs($officer)->get(route('admin.reviews'));

        $response->assertOk()
            ->assertViewIs('admin.reviews.index')
            ->assertSee('Application Reviews')
            ->assertDontSee('Admissions Applications')
            ->assertSee($submitted->reference)
            ->assertSee($underReview->reference)
            ->assertDontSee($approved->reference);

        $this->actingAs(User::where('email', 'student1@school.com')->firstOrFail())
            ->get(route('admin.reviews'))
            ->assertForbidden();
    }

    public function test_admissions_officer_interviews_page_is_separate_and_truthfully_empty(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();

        $this->actingAs($officer)->get(route('admin.interviews'))
            ->assertOk()
            ->assertViewIs('admin.interviews.index')
            ->assertSee('Interviews')
            ->assertSee('No interview records')
            ->assertSee('Interview scheduling is not currently part of the admissions data model.');

        $this->actingAs(User::where('email', 'student1@school.com')->firstOrFail())
            ->get(route('admin.interviews'))
            ->assertForbidden();
    }

    public function test_admissions_officer_decisions_page_lists_real_decisions_and_pending_items(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();
        $pending = AdmissionApplication::factory()->create(['name' => 'Pending Decision Applicant', 'status' => 'under_review']);
        $approved = AdmissionApplication::factory()->create([
            'name' => 'Approved Decision Applicant',
            'status' => 'approved',
            'decided_by' => $officer->id,
            'decided_at' => now(),
        ]);
        $submitted = AdmissionApplication::factory()->create(['name' => 'Not Yet Decided Applicant', 'status' => 'submitted']);

        $this->actingAs($officer)->get(route('admin.decisions'))
            ->assertOk()
            ->assertViewIs('admin.decisions.index')
            ->assertSee('Admission Decisions')
            ->assertSee($pending->reference)
            ->assertSee($approved->reference)
            ->assertDontSee($submitted->reference)
            ->assertSee($officer->name);

        $this->actingAs($officer)->get(route('admin.decisions', ['status' => 'approved']))
            ->assertOk()
            ->assertSee($approved->reference)
            ->assertDontSee($pending->reference);

        $this->actingAs(User::where('email', 'student1@school.com')->firstOrFail())
            ->get(route('admin.decisions'))
            ->assertForbidden();
    }

    public function test_admissions_officer_can_search_and_view_applicant_profile(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();
        $application = AdmissionApplication::factory()->create([
            'name' => 'Profile Applicant',
            'email' => 'profile@example.com',
            'education' => ['qualification' => 'Secondary certificate'],
            'status' => 'submitted',
        ]);

        $this->actingAs($officer)->get('/admin/applicants?search=profile@example.com')
            ->assertOk()->assertSee('Profile Applicant')->assertSee('1');
        $this->actingAs($officer)->get('/admin/applicants?status=submitted')
            ->assertOk()->assertSee('Profile Applicant');
        $this->actingAs($officer)->get('/admin/applicants/' . $application->id)
            ->assertOk()->assertSee('Personal Information')->assertSee('Application History')->assertSee($application->reference);
    }

    public function test_unauthorized_user_cannot_access_applicant_directory_or_profile(): void
    {
        $this->seed();
        $application = AdmissionApplication::factory()->create();
        $student = User::where('email', 'student1@school.com')->firstOrFail();

        $this->actingAs($student)->get('/admin/applicants')->assertForbidden();
        $this->actingAs($student)->get('/admin/applicants/' . $application->id)->assertForbidden();
    }

    public function test_admissions_officer_can_view_application_details(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();
        $application = AdmissionApplication::factory()->create();

        $response = $this->actingAs($officer)->get("/admin/applications/{$application->id}");
        $response->assertStatus(200);
    }

    public function test_admissions_officer_can_approve_application(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();
        $application = AdmissionApplication::factory()->create(['status' => 'submitted']);

        $response = $this->actingAs($officer)->post("/admin/applications/{$application->id}/approve");
        $response->assertRedirect();

        $this->assertDatabaseHas('admission_applications', [
            'id' => $application->id,
            'status' => 'approved',
            'decided_by' => $officer->id,
        ]);
    }

    public function test_admissions_officer_can_decline_application(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();
        $application = AdmissionApplication::factory()->create(['status' => 'submitted']);

        $response = $this->actingAs($officer)->post("/admin/applications/{$application->id}/decline", [
            'decision_reason' => 'Does not meet requirements.',
        ]);
        $response->assertRedirect();

        $this->assertDatabaseHas('admission_applications', [
            'id' => $application->id,
            'status' => 'declined',
            'decided_by' => $officer->id,
            'decision_reason' => 'Does not meet requirements.',
        ]);
    }

    public function test_unauthorized_user_cannot_change_application_status(): void
    {
        $this->seed();
        $application = AdmissionApplication::factory()->create(['status' => 'submitted']);
        $student = User::where('email', 'student1@school.com')->firstOrFail();

        $this->actingAs($student)->post('/admin/applications/' . $application->id . '/approve')->assertForbidden();
        $this->assertSame('submitted', $application->fresh()->status);
    }

    public function test_invalid_application_status_transition_is_rejected(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();
        $application = AdmissionApplication::factory()->create(['status' => 'approved']);

        $this->actingAs($officer)->post('/admin/applications/' . $application->id . '/approve')->assertStatus(422);
        $this->assertSame('approved', $application->fresh()->status);
    }

    public function test_student_cannot_access_admissions_applications(): void
    {
        $this->seed();
        $student = User::where('email', 'student1@school.com')->first();

        $response = $this->actingAs($student)->get('/admin/applications');
        $response->assertForbidden();
    }

    public function test_teacher_cannot_access_admissions_applications(): void
    {
        $this->seed();
        $teacher = User::where('email', 'teacher1@school.com')->first();

        $response = $this->actingAs($teacher)->get('/admin/applications');
        $response->assertForbidden();
    }

    public function test_parent_cannot_access_admissions_applications(): void
    {
        $this->seed();
        $parent = User::where('email', 'parent1@school.com')->first();

        $response = $this->actingAs($parent)->get('/admin/applications');
        $response->assertForbidden();
    }

    public function test_admissions_officer_cannot_access_other_admin_functions(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();

        // Assuming /students is restricted to admin/staff
        $response = $this->actingAs($officer)->get('/students');
        $response->assertForbidden();
    }

    public function test_applicant_documents_are_stored_privately_and_downloadable_by_admissions_staff(): void
    {
        $this->seed();
        Storage::fake('local');
        $request = $this->withSession([
            'admission_application.basic' => [
                'name' => 'Document Applicant', 'email' => 'documents@example.com', 'phone' => '555', 'grade_interested' => 'Grade 9', 'message' => null,
            ],
            'admission_application.education' => [],
        ]);

        $request->post('/apply/documents', ['documents' => [UploadedFile::fake()->create('transcript.pdf', 100, 'application/pdf')]])
            ->assertRedirect('/apply?step=4');

        $documents = session('admission_application.documents');
        $this->assertCount(1, $documents);
        Storage::disk('local')->assertExists($documents[0]['path']);

        $application = AdmissionApplication::factory()->create(['documents' => $documents]);
        $officer = $this->createAdmissionsOfficer();
        $this->actingAs($officer)->get('/admin/applications/' . $application->id . '/documents/0')->assertOk();
        $this->actingAs(User::where('email', 'student1@school.com')->firstOrFail())->get('/admin/applications/' . $application->id . '/documents/0')->assertForbidden();
    }

    public function test_admissions_officer_can_move_submitted_application_to_review(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();
        $application = AdmissionApplication::factory()->create(['status' => 'submitted']);

        $this->actingAs($officer)->post('/admin/applications/' . $application->id . '/review')
            ->assertRedirect();
        $this->assertDatabaseHas('admission_applications', ['id' => $application->id, 'status' => 'under_review']);
    }

    public function test_admissions_officer_can_verify_application_document(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();
        $application = AdmissionApplication::factory()->create(['documents' => [['name' => 'id.pdf', 'path' => 'id.pdf', 'status' => 'submitted']]]);

        $this->actingAs($officer)->patch('/admin/applications/' . $application->id . '/documents/0', [
            'status' => 'verified', 'verification_notes' => 'Document is legible.',
        ])->assertRedirect();

        $this->assertSame('verified', $application->fresh()->documents[0]['status']);
        $this->assertSame('Document is legible.', $application->fresh()->documents[0]['verification_notes']);
    }

    public function test_admissions_officer_can_view_a_private_document_inline(): void
    {
        $this->seed();
        Storage::fake('local');
        Storage::disk('local')->put('admission-documents/id.txt', 'private document content');
        $application = AdmissionApplication::factory()->create(['documents' => [['name' => 'id.txt', 'path' => 'admission-documents/id.txt', 'status' => 'submitted']]]);
        $officer = $this->createAdmissionsOfficer();

        $this->actingAs($officer)->get('/admin/applications/' . $application->id . '/documents/0?view=1')
            ->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename="id.txt"')
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function test_documents_page_lists_real_documents_and_supports_correction_status(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();
        $application = AdmissionApplication::factory()->create(['documents' => [['name' => 'certificate.pdf', 'path' => 'certificate.pdf', 'status' => 'submitted']]]);

        $this->actingAs($officer)->get('/admin/applications?view=documents')
            ->assertOk()->assertSee('certificate.pdf')->assertSee($application->reference);
        $this->actingAs($officer)->patch('/admin/applications/' . $application->id . '/documents/0', [
            'status' => 'requires_correction', 'verification_notes' => 'Please upload a clearer copy.',
        ])->assertRedirect();
        $this->assertSame('requires_correction', $application->fresh()->documents[0]['status']);
    }

    public function test_document_access_rejects_invalid_index_and_unauthorized_update(): void
    {
        $this->seed();
        $application = AdmissionApplication::factory()->create(['documents' => [['name' => 'id.pdf', 'path' => 'id.pdf', 'status' => 'submitted']]]);
        $officer = $this->createAdmissionsOfficer();
        $student = User::where('email', 'student1@school.com')->firstOrFail();

        $this->actingAs($officer)->get('/admin/applications/' . $application->id . '/documents/8')->assertNotFound();
        $this->actingAs($student)->patch('/admin/applications/' . $application->id . '/documents/0', ['status' => 'verified'])->assertForbidden();
        $this->assertSame('submitted', $application->fresh()->documents[0]['status']);
    }

    public function test_documents_page_tracks_real_document_statistics_and_searches_document_names(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();

        $documentMatch = AdmissionApplication::factory()->create([
            'name' => 'Document Search Applicant',
            'documents' => [
                ['name' => 'transcript.pdf', 'path' => 'admission-documents/transcript.pdf', 'status' => 'submitted'],
                ['name' => 'passport.jpg', 'path' => 'admission-documents/passport.jpg', 'status' => 'verified'],
            ],
        ]);
        AdmissionApplication::factory()->create([
            'name' => 'Other Applicant',
            'documents' => [
                ['name' => 'certificate.pdf', 'path' => 'admission-documents/certificate.pdf', 'status' => 'requires_correction'],
            ],
        ]);

        $response = $this->actingAs($officer)->get('/admin/applications?view=documents');
        $response->assertOk()
            ->assertSee('Total Documents')
            ->assertSee('Pending Review')
            ->assertSee('Verified')
            ->assertSee('Missing Documents');

        $this->actingAs($officer)->get('/admin/applications?view=documents&search=transcript')
            ->assertOk()->assertSee('transcript.pdf')->assertDontSee('certificate.pdf');

        $this->assertDatabaseHas('admission_applications', ['id' => $documentMatch->id]);
    }

    public function test_admissions_officer_can_search_and_filter_applications(): void
    {
        $this->seed();
        $officer = $this->createAdmissionsOfficer();
        $match = AdmissionApplication::factory()->create(['name' => 'Searchable Applicant', 'status' => 'under_review']);
        AdmissionApplication::factory()->create(['name' => 'Other Applicant', 'status' => 'submitted']);

        $this->actingAs($officer)->get('/admin/applications?search=Searchable')
            ->assertOk()->assertSee($match->reference)->assertDontSee('Other Applicant');
        $this->actingAs($officer)->get('/admin/applications?status=under_review')
            ->assertOk()->assertSee($match->reference);

        $documentMatch = AdmissionApplication::factory()->create([
            'documents' => [['name' => 'id.pdf', 'path' => 'id.pdf', 'status' => 'verified']],
        ]);
        $this->actingAs($officer)->get('/admin/applications?document_status=verified')
            ->assertOk()->assertSee($documentMatch->reference)->assertDontSee($match->reference);
    }
}
