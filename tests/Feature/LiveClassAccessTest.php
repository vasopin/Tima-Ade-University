<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use \Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class LiveClassAccessTest extends TestCase
{
    use \Tests\Concerns\ForceRefreshDatabase;

    public function test_teacher_live_class_page_renders_assigned_classes(): void
    {
        $this->seedRoles();

        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', 'teacher')->first()->id]);
        Teacher::create([
            'user_id' => $teacherUser->id,
            'employee_id' => 'T-1000',
            'specialization' => 'Computer Science',
        ]);

        $schoolClass = SchoolClass::create(['name' => 'Grade 9', 'grade_level' => '9', 'is_active' => true]);
        $subject = Subject::create(['name' => 'Computer Science', 'code' => 'CS-101', 'is_active' => true]);
        $schoolClass->subjects()->attach($subject->id, ['teacher_id' => $teacherUser->id]);

        $this->actingAs($teacherUser)
            ->get('/teacher/live-classes')
            ->assertOk()
            ->assertSee('Grade 9')
            ->assertSee('Computer Science');
    }

    public function test_teacher_live_class_page_only_lists_their_assigned_course_options(): void
    {
        $this->seedRoles();

        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', 'teacher')->first()->id]);
        $otherTeacher = User::factory()->create(['role_id' => Role::where('slug', 'teacher')->first()->id]);
        Teacher::create([
            'user_id' => $teacherUser->id,
            'employee_id' => 'T-1002',
            'specialization' => 'Mathematics',
        ]);

        $assignedClass = SchoolClass::create(['name' => 'Assigned Class', 'grade_level' => '9', 'is_active' => true]);
        $assignedSubject = Subject::create(['name' => 'Assigned Subject', 'code' => 'ASSIGNED-101', 'is_active' => true]);
        $assignedClass->subjects()->attach($assignedSubject->id, ['teacher_id' => $teacherUser->id]);

        $otherClass = SchoolClass::create(['name' => 'Other Class', 'grade_level' => '10', 'is_active' => true]);
        $otherSubject = Subject::create(['name' => 'Other Subject', 'code' => 'OTHER-101', 'is_active' => true]);
        $otherClass->subjects()->attach($otherSubject->id, ['teacher_id' => $otherTeacher->id]);

        $response = $this->actingAs($teacherUser)->get('/teacher/live-classes');

        $response->assertOk()
            ->assertSee('value="' . $assignedClass->id . '"', false)
            ->assertSee('value="' . $assignedSubject->id . '"', false)
            ->assertDontSee('value="' . $otherClass->id . '"', false)
            ->assertDontSee('value="' . $otherSubject->id . '"', false);
    }

    public function test_teacher_can_create_a_live_class_for_their_course(): void
    {
        $this->seedRoles();

        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', 'teacher')->first()->id]);
        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'employee_id' => 'T-1001',
            'specialization' => 'Computer Science',
        ]);

        $schoolClass = SchoolClass::create(['name' => 'Grade 10', 'grade_level' => '10', 'is_active' => true]);
        $section = \App\Models\Section::create(['school_class_id' => $schoolClass->id, 'name' => 'A', 'capacity' => 40]);
        $subject = Subject::create(['name' => 'Mathematics', 'code' => 'MATH-101', 'is_active' => true]);
        $schoolClass->subjects()->attach($subject->id, ['teacher_id' => $teacherUser->id]);

        $response = $this->actingAs($teacherUser)->post('/teacher/live-classes', [
            'title' => 'Algebra Review',
            'school_class_id' => $schoolClass->id,
            'subject_id' => $subject->id,
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'duration_minutes' => 50,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('live_classes', ['title' => 'Algebra Review', 'teacher_id' => $teacherUser->id]);
    }

    public function test_authorized_teacher_can_go_live_from_scheduled_class(): void
    {
        $this->seedRoles();

        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', 'teacher')->first()->id]);
        $schoolClass = SchoolClass::create(['name' => 'Grade 10', 'grade_level' => '10', 'is_active' => true]);
        $subject = Subject::create(['name' => 'Physics', 'code' => 'PHY-101', 'is_active' => true]);
        $schoolClass->subjects()->attach($subject->id, ['teacher_id' => $teacherUser->id]);
        $liveClass = \App\Models\LiveClass::create([
            'teacher_id' => $teacherUser->id,
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'title' => 'Physics Lab',
            'scheduled_at' => now()->addHour(),
            'duration_minutes' => 45,
            'status' => 'scheduled',
        ]);

        $this->actingAs($teacherUser)
            ->post("/live-classes/{$liveClass->id}/start")
            ->assertRedirect(route('live-classes.show', $liveClass));

        $this->assertDatabaseHas('live_classes', [
            'id' => $liveClass->id,
            'status' => 'live',
        ]);
    }

    public function test_create_and_schedule_are_separate_actions(): void
    {
        $this->seedRoles();

        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', 'teacher')->first()->id]);
        $schoolClass = SchoolClass::create(['name' => 'Grade 12', 'grade_level' => '12', 'is_active' => true]);
        $subject = Subject::create(['name' => 'History', 'code' => 'HIS-101', 'is_active' => true]);
        $schoolClass->subjects()->attach($subject->id, ['teacher_id' => $teacherUser->id]);

        $this->actingAs($teacherUser)
            ->post('/teacher/live-classes/create', [
                'title' => 'History Classroom',
                'school_class_id' => $schoolClass->id,
                'subject_id' => $subject->id,
            ])
            ->assertRedirect();

        $liveClass = \App\Models\LiveClass::where('title', 'History Classroom')->firstOrFail();
        $this->assertSame('created', $liveClass->status);

        $this->actingAs($teacherUser)
            ->patch("/live-classes/{$liveClass->id}/schedule", [
                'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
                'duration_minutes' => 60,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('live_classes', [
            'id' => $liveClass->id,
            'status' => 'scheduled',
            'duration_minutes' => 60,
        ]);
    }

    public function test_ended_class_cannot_be_started_again(): void
    {
        $this->seedRoles();

        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', 'teacher')->first()->id]);
        $schoolClass = SchoolClass::create(['name' => 'Grade 11', 'grade_level' => '11', 'is_active' => true]);
        $subject = Subject::create(['name' => 'Biology', 'code' => 'BIO-101', 'is_active' => true]);
        $schoolClass->subjects()->attach($subject->id, ['teacher_id' => $teacherUser->id]);
        $liveClass = \App\Models\LiveClass::create([
            'teacher_id' => $teacherUser->id,
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'title' => 'Biology Review',
            'scheduled_at' => now()->subHour(),
            'duration_minutes' => 45,
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        $this->actingAs($teacherUser)
            ->post("/live-classes/{$liveClass->id}/start")
            ->assertStatus(422);
    }

    public function test_student_cannot_create_live_classes(): void
    {
        $this->seedRoles();

        $studentUser = User::factory()->create(['role_id' => Role::where('slug', 'student')->first()->id]);
        $schoolClass = SchoolClass::create(['name' => 'Grade 11', 'grade_level' => '11', 'is_active' => true]);
        $section = \App\Models\Section::create(['school_class_id' => $schoolClass->id, 'name' => 'B', 'capacity' => 40]);
        $subject = Subject::create(['name' => 'Biology', 'code' => 'BIO-201', 'is_active' => true]);
        $schoolClass->subjects()->attach($subject->id, ['teacher_id' => $studentUser->id]);
        Student::create([
            'user_id' => $studentUser->id,
            'school_class_id' => $schoolClass->id,
            'section_id' => $section->id,
            'roll_number' => 'STU-001',
            'admission_number' => 'ADM-2024-001',
            'admission_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($studentUser)->post('/teacher/live-classes', [
            'title' => 'Illegal Class',
            'school_class_id' => $schoolClass->id,
            'subject_id' => $subject->id,
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'duration_minutes' => 50,
        ]);

        $response->assertForbidden();
    }

    public function test_enrolled_student_can_see_live_class_and_non_enrolled_user_cannot_join(): void
    {
        $this->seedRoles();

        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', 'teacher')->first()->id]);
        $studentUser = User::factory()->create(['role_id' => Role::where('slug', 'student')->first()->id]);
        $otherStudentUser = User::factory()->create(['role_id' => Role::where('slug', 'student')->first()->id]);

        $schoolClass = SchoolClass::create(['name' => 'Grade 12', 'grade_level' => '12', 'is_active' => true]);
        $otherClass = SchoolClass::create(['name' => 'Grade 9', 'grade_level' => '9', 'is_active' => true]);
        $section = \App\Models\Section::create(['school_class_id' => $schoolClass->id, 'name' => 'C', 'capacity' => 40]);
        $otherSection = \App\Models\Section::create(['school_class_id' => $otherClass->id, 'name' => 'A', 'capacity' => 40]);
        $subject = Subject::create(['name' => 'Physics', 'code' => 'PHY-301', 'is_active' => true]);
        $schoolClass->subjects()->attach($subject->id, ['teacher_id' => $teacherUser->id]);

        Student::create([
            'user_id' => $studentUser->id,
            'school_class_id' => $schoolClass->id,
            'section_id' => $section->id,
            'roll_number' => 'STU-002',
            'admission_number' => 'ADM-2024-002',
            'admission_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        Student::create([
            'user_id' => $otherStudentUser->id,
            'school_class_id' => $otherClass->id,
            'section_id' => $otherSection->id,
            'roll_number' => 'STU-003',
            'admission_number' => 'ADM-2024-003',
            'admission_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $liveClass = \App\Models\LiveClass::create([
            'teacher_id' => $teacherUser->id,
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'title' => 'Exam Prep',
            'scheduled_at' => now()->addHour(),
            'duration_minutes' => 45,
            'status' => 'live',
            'started_at' => now(),
        ]);

        $scheduledClass = \App\Models\LiveClass::create([
            'teacher_id' => $teacherUser->id,
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'title' => 'Upcoming Physics Workshop',
            'scheduled_at' => now()->addDay(),
            'duration_minutes' => 60,
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($studentUser)->get('/student/dashboard');
        $response->assertSee('Exam Prep');
        $response->assertSee('Upcoming Physics Workshop');
        $response->assertSee('UPCOMING');
        $response->assertSee('Join Live Class');
        $response->assertDontSee('Create Live Class');
        $response->assertDontSee('Go Live');

        $this->actingAs($studentUser)
            ->getJson('/student/live-classes/status')
            ->assertOk()
            ->assertJsonPath((string) $liveClass->id, 'live')
            ->assertJsonPath((string) $scheduledClass->id, 'scheduled');

        $this->actingAs($studentUser)
            ->get('/student/live-classes')
            ->assertOk()
            ->assertSee('Live Classes')
            ->assertSee('Exam Prep')
            ->assertSee('Upcoming Physics Workshop')
            ->assertDontSee('Create Live Class')
            ->assertDontSee('Go Live');

        $this->actingAs($otherStudentUser)
            ->get('/student/live-classes')
            ->assertOk()
            ->assertDontSee('Exam Prep')
            ->assertDontSee('Upcoming Physics Workshop');

        $joinResponse = $this->actingAs($otherStudentUser)->post('/live-classes/' . $liveClass->id . '/join');
        $joinResponse->assertForbidden();
    }

    public function test_teacher_can_end_live_class_and_student_cannot_end_it(): void
    {
        $this->seedRoles();

        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', 'teacher')->first()->id]);
        $studentUser = User::factory()->create(['role_id' => Role::where('slug', 'student')->first()->id]);

        $schoolClass = SchoolClass::create(['name' => 'Grade 8', 'grade_level' => '8', 'is_active' => true]);
        $section = \App\Models\Section::create(['school_class_id' => $schoolClass->id, 'name' => 'E', 'capacity' => 30]);
        $subject = Subject::create(['name' => 'Chemistry', 'code' => 'CHEM-201', 'is_active' => true]);
        $schoolClass->subjects()->attach($subject->id, ['teacher_id' => $teacherUser->id]);

        Student::create([
            'user_id' => $studentUser->id,
            'school_class_id' => $schoolClass->id,
            'section_id' => $section->id,
            'roll_number' => 'STU-004',
            'admission_number' => 'ADM-2024-004',
            'admission_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $liveClass = \App\Models\LiveClass::create([
            'teacher_id' => $teacherUser->id,
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'title' => 'Lab Safety',
            'scheduled_at' => now()->subHour(),
            'duration_minutes' => 60,
            'status' => 'live',
            'started_at' => now()->subMinutes(20),
        ]);

        $this->actingAs($teacherUser)->post('/live-classes/' . $liveClass->id . '/end')
            ->assertRedirect();

        $this->assertDatabaseHas('live_classes', ['id' => $liveClass->id, 'status' => 'ended']);

        $this->actingAs($studentUser)->post('/live-classes/' . $liveClass->id . '/end')
            ->assertForbidden();
    }

    public function test_teacher_can_moderate_participants_and_student_cannot(): void
    {
        $this->seedRoles();

        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', 'teacher')->first()->id]);
        $studentUser = User::factory()->create(['role_id' => Role::where('slug', 'student')->first()->id]);

        $schoolClass = SchoolClass::create(['name' => 'Grade 7', 'grade_level' => '7', 'is_active' => true]);
        $section = \App\Models\Section::create(['school_class_id' => $schoolClass->id, 'name' => 'F', 'capacity' => 25]);
        $subject = Subject::create(['name' => 'English', 'code' => 'ENG-102', 'is_active' => true]);
        $schoolClass->subjects()->attach($subject->id, ['teacher_id' => $teacherUser->id]);

        Student::create([
            'user_id' => $studentUser->id,
            'school_class_id' => $schoolClass->id,
            'section_id' => $section->id,
            'roll_number' => 'STU-005',
            'admission_number' => 'ADM-2024-005',
            'admission_date' => now()->toDateString(),
            'status' => 'active',
        ]);

        $liveClass = \App\Models\LiveClass::create([
            'teacher_id' => $teacherUser->id,
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'title' => 'Reading Circle',
            'scheduled_at' => now()->addMinutes(10),
            'duration_minutes' => 40,
            'status' => 'live',
            'started_at' => now(),
        ]);

        $participant = \App\Models\LiveClassParticipant::create([
            'live_class_id' => $liveClass->id,
            'user_id' => $studentUser->id,
            'role' => 'student',
            'is_active' => true,
            'is_muted' => false,
            'camera_on' => true,
            'joined_at' => now(),
        ]);

        $this->actingAs($teacherUser)->post('/live-classes/' . $liveClass->id . '/participants/' . $participant->id . '/mute')
            ->assertRedirect();

        $this->actingAs($studentUser)->post('/live-classes/' . $liveClass->id . '/participants/' . $participant->id . '/mute')
            ->assertForbidden();
    }

    public function test_student_can_leave_without_ending_class_and_cannot_rejoin_after_end(): void
    {
        $this->seedRoles();
        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', 'teacher')->first()->id]);
        $studentUser = User::factory()->create(['role_id' => Role::where('slug', 'student')->first()->id]);
        $schoolClass = SchoolClass::create(['name' => 'Grade 6', 'grade_level' => '6', 'is_active' => true]);
        $section = \App\Models\Section::create(['school_class_id' => $schoolClass->id, 'name' => 'G', 'capacity' => 25]);
        $subject = Subject::create(['name' => 'History', 'code' => 'HIS-101', 'is_active' => true]);
        $schoolClass->subjects()->attach($subject->id, ['teacher_id' => $teacherUser->id]);
        Student::create([
            'user_id' => $studentUser->id,
            'school_class_id' => $schoolClass->id,
            'section_id' => $section->id,
            'roll_number' => 'STU-006',
            'admission_number' => 'ADM-2024-006',
            'admission_date' => now()->toDateString(),
            'status' => 'active',
        ]);
        $liveClass = \App\Models\LiveClass::create([
            'teacher_id' => $teacherUser->id,
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'title' => 'World History',
            'scheduled_at' => now(),
            'duration_minutes' => 45,
            'status' => 'live',
            'started_at' => now(),
        ]);

        $this->actingAs($studentUser)->post("/live-classes/{$liveClass->id}/join")->assertRedirect();
        $this->actingAs($studentUser)->post("/live-classes/{$liveClass->id}/leave")->assertRedirect();
        $this->assertDatabaseHas('live_classes', ['id' => $liveClass->id, 'status' => 'live']);
        $this->actingAs($teacherUser)->post("/live-classes/{$liveClass->id}/end")->assertRedirect();
        $this->actingAs($studentUser)->post("/live-classes/{$liveClass->id}/join")->assertForbidden();
    }

    public function test_teacher_can_update_permissions_and_staff_cannot_monitor(): void
    {
        $this->seedRoles();
        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', 'teacher')->first()->id]);
        $studentUser = User::factory()->create(['role_id' => Role::where('slug', 'student')->first()->id]);
        $staffUser = User::factory()->create(['role_id' => Role::where('slug', 'staff')->first()->id]);
        $adminUser = User::factory()->create(['role_id' => Role::where('slug', 'admin')->first()->id]);
        $superAdminUser = User::factory()->create(['role_id' => Role::where('slug', 'super_admin')->first()->id]);
        $schoolClass = SchoolClass::create(['name' => 'Grade 5', 'grade_level' => '5', 'is_active' => true]);
        $section = \App\Models\Section::create(['school_class_id' => $schoolClass->id, 'name' => 'H', 'capacity' => 25]);
        $subject = Subject::create(['name' => 'Art', 'code' => 'ART-101', 'is_active' => true]);
        $schoolClass->subjects()->attach($subject->id, ['teacher_id' => $teacherUser->id]);
        $liveClass = \App\Models\LiveClass::create([
            'teacher_id' => $teacherUser->id,
            'subject_id' => $subject->id,
            'school_class_id' => $schoolClass->id,
            'title' => 'Studio Session',
            'scheduled_at' => now(),
            'duration_minutes' => 45,
            'status' => 'live',
            'started_at' => now(),
        ]);
        $participant = \App\Models\LiveClassParticipant::create([
            'live_class_id' => $liveClass->id,
            'user_id' => $studentUser->id,
            'role' => 'student',
            'is_active' => true,
        ]);

        $this->actingAs($teacherUser)->patch("/live-classes/{$liveClass->id}/participants/{$participant->id}/permissions", [
            'can_share_screen' => false,
            'can_send_messages' => true,
        ])->assertRedirect();
        $this->assertDatabaseHas('live_class_participants', ['id' => $participant->id]);
        $this->actingAs($adminUser)->get('/admin/live-classes')->assertOk()->assertSee('Studio Session');
        $this->actingAs($superAdminUser)->get('/admin/live-classes')->assertOk()->assertSee('Studio Session');
        $this->actingAs($staffUser)->get('/admin/live-classes')->assertForbidden();
    }

    protected function seedRoles(): void
    {
        foreach (['super_admin', 'admin', 'staff', 'teacher', 'student', 'parent'] as $slug) {
            $name = ucfirst(str_replace('_', ' ', $slug));
            if (! \App\Models\Role::where('slug', $slug)->exists()) {
                \App\Models\Role::create(['name' => $name, 'slug' => $slug]);
            }
        }
    }
}
