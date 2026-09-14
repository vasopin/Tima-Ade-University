<?php

namespace Tests\Feature;

use App\Models\CourseMaterial;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use \Tests\Concerns\ForceRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LmsSecurityTest extends TestCase
{
    use \Tests\Concerns\ForceRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_authorized_teacher_can_upload_to_their_course(): void
    {
        Storage::fake('public');

        $teacher = User::where('email', 'teacher1@school.com')->firstOrFail();
        $class = SchoolClass::where('grade_level', '10')->firstOrFail();
        $subjectRow = DB::table('class_subject')->where('school_class_id', $class->id)->where('teacher_id', $teacher->id)->first();
        $subject = Subject::findOrFail($subjectRow->subject_id);

        $response = $this->actingAs($teacher)->post('/teacher/learning/videos', [
            'title' => 'Security test lesson',
            'description' => 'Authorized teacher upload test',
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
            'video' => UploadedFile::fake()->create('security-lesson.mp4', 1024, 'video/mp4'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lecture_videos', [
            'title' => 'Security test lesson',
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->teacher->id,
        ]);
    }

    public function test_unauthorized_teacher_cannot_upload_to_another_teachers_course(): void
    {
        Storage::fake('public');

        $teacherOne = User::where('email', 'teacher1@school.com')->firstOrFail();
        $teacherTwo = User::where('email', 'teacher2@school.com')->firstOrFail();
        $class = SchoolClass::create([
            'name' => 'Science Lab 12',
            'grade_level' => '12',
            'description' => 'Restricted access test class',
            'is_active' => true,
        ]);
        $subject = Subject::create([
            'name' => 'Security Methods',
            'code' => 'SEC-TEST-001',
            'description' => 'Security test course',
            'is_active' => true,
        ]);

        DB::table('class_subject')->insert([
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacherOne->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($teacherTwo)->post('/teacher/learning/videos', [
            'title' => 'Unauthorized upload',
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
            'video' => UploadedFile::fake()->create('unauthorized-lesson.mp4', 1024, 'video/mp4'),
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('lecture_videos', ['title' => 'Unauthorized upload']);
    }

    public function test_enrolled_student_can_view_course_material(): void
    {
        Storage::fake('public');

        $teacher = User::where('email', 'teacher1@school.com')->firstOrFail();
        $student = User::where('email', 'student1@school.com')->firstOrFail();
        $class = $student->student->schoolClass;
        $subjectRow = DB::table('class_subject')->where('school_class_id', $class->id)->where('teacher_id', $teacher->id)->first();
        $subject = Subject::findOrFail($subjectRow->subject_id);

        $material = CourseMaterial::create([
            'teacher_id' => $teacher->teacher->id,
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
            'title' => 'Student access test material',
            'description' => 'This should be visible to enrolled students',
            'file_path' => 'uploads/materials/student-access-test.pdf',
            'original_name' => 'student-access-test.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 2048,
            'status' => 'published',
        ]);

        Storage::disk('public')->put($material->file_path, '%PDF-1.4');

        $response = $this->actingAs($student)->get('/learning/materials/' . $material->id . '/file');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_unenrolled_student_cannot_view_course_material(): void
    {
        Storage::fake('public');

        $teacher = User::where('email', 'teacher1@school.com')->firstOrFail();
        $student = User::where('email', 'student4@school.com')->firstOrFail();
        $class = SchoolClass::where('grade_level', '10')->firstOrFail();
        $subjectRow = DB::table('class_subject')->where('school_class_id', $class->id)->where('teacher_id', $teacher->id)->first();
        $subject = Subject::findOrFail($subjectRow->subject_id);

        $material = CourseMaterial::create([
            'teacher_id' => $teacher->teacher->id,
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
            'title' => 'Hidden material',
            'description' => 'Not for this student',
            'file_path' => 'uploads/materials/hidden-material.pdf',
            'original_name' => 'hidden-material.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 2048,
            'status' => 'published',
        ]);

        Storage::disk('public')->put($material->file_path, '%PDF-1.4');

        $response = $this->actingAs($student)->get('/learning/materials/' . $material->id . '/file');

        $response->assertStatus(403);
    }

    public function test_student_cannot_modify_attendance(): void
    {
        $studentUser = User::where('email', 'student1@school.com')->firstOrFail();
        $student = $studentUser->student;

        $response = $this->actingAs($studentUser)->post('/attendance', [
            'attendance_date' => today()->toDateString(),
            'school_class_id' => $student->school_class_id,
            'section_id' => $student->section_id,
            'attendance' => [$student->id => 'present'],
        ]);

        $response->assertStatus(403);
    }

    public function test_teacher_cannot_manage_another_teachers_attendance(): void
    {
        $teacherOne = User::where('email', 'teacher1@school.com')->firstOrFail();
        $teacherTwo = User::where('email', 'teacher2@school.com')->firstOrFail();
        $class = SchoolClass::create([
            'name' => 'Attendance Guard Class',
            'grade_level' => '9',
            'description' => 'Unauthorized teacher must not access this attendance roster',
            'is_active' => true,
        ]);
        $section = DB::table('sections')->where('school_class_id', $class->id)->first();
        if (! $section) {
            DB::table('sections')->insert([
                'school_class_id' => $class->id,
                'name' => 'Section A',
                'capacity' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $section = DB::table('sections')->where('school_class_id', $class->id)->first();
        }

        $subject = Subject::create([
            'name' => 'Attendance Security',
            'code' => 'ATT-SEC-001',
            'description' => 'Restricted attendance subject',
            'is_active' => true,
        ]);

        DB::table('class_subject')->insert([
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacherOne->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $studentUser = User::where('email', 'student5@school.com')->firstOrFail();
        $student = $studentUser->student;
        $student->update([
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($teacherTwo)->post('/attendance', [
            'attendance_date' => today()->toDateString(),
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'attendance' => [$student->id => 'present'],
        ]);

        $response->assertStatus(403);
    }

    public function test_authorized_teacher_can_save_attendance_from_teacher_page(): void
    {
        $teacher = User::where('email', 'teacher1@school.com')->firstOrFail();
        $student = Student::whereHas('schoolClass', function ($query) use ($teacher) {
            $query->whereIn('id', DB::table('class_subject')->where('teacher_id', $teacher->id)->pluck('school_class_id'));
        })->where('status', 'active')->firstOrFail();

        $response = $this->actingAs($teacher)->followingRedirects()->post('/attendance/mark', [
            'attendance_date' => today()->toDateString(),
            'school_class_id' => $student->school_class_id,
            'section_id' => $student->section_id,
            'attendance' => [$student->id => 'present'],
        ]);

        $response->assertOk()
            ->assertSee('Attendance saved successfully')
            ->assertSee($student->user->name)
            ->assertSee('Present');
        $this->assertTrue(Attendance::where('student_id', $student->id)
            ->whereDate('attendance_date', today())
            ->where('status', 'present')
            ->exists());

        $redirectResponse = $this->actingAs($teacher)->post('/attendance/mark', [
            'attendance_date' => today()->toDateString(),
            'school_class_id' => $student->school_class_id,
            'section_id' => $student->section_id,
            'attendance' => [$student->id => 'late'],
        ]);

        $redirectResponse->assertRedirect(route('attendance.index', [
            'class_id' => $student->school_class_id,
            'date' => today()->toDateString(),
        ]));

        $this->assertSame(1, Attendance::where('student_id', $student->id)
            ->whereDate('attendance_date', today())
            ->count());
        $this->assertDatabaseHas('attendances', [
            'student_id' => $student->id,
            'status' => 'late',
        ]);
    }

    public function test_student_can_view_fee_ledger_but_cannot_record_payment(): void
    {
        $studentUser = User::where('email', 'student1@school.com')->firstOrFail();
        $student = $studentUser->student;

        $this->actingAs($studentUser)
            ->get(route('fees.student', $student))
            ->assertOk()
            ->assertDontSee('Record Payment');

        $this->actingAs($studentUser)
            ->get(route('fees.create'))
            ->assertForbidden();
    }

    public function test_parent_dashboard_uses_linked_children_and_rejects_other_children(): void
    {
        $parent = User::where('email', 'parent1@school.com')->firstOrFail();
        $children = $parent->guardian->students;
        $child = $children->firstOrFail();
        $otherStudent = Student::whereNotIn('id', $children->pluck('id'))->firstOrFail();

        $this->actingAs($parent)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('University Portal')
            ->assertSee('My Children')
            ->assertSee($child->user->name);

        $this->actingAs($parent)
            ->get(route('parent.children'))
            ->assertOk()
            ->assertSee('My Children')
            ->assertSee($child->user->name);

        $this->actingAs($parent)
            ->get(route('parent.children', ['student_id' => $child->id]))
            ->assertOk();

        $this->actingAs($parent)
            ->get(route('parent.children', ['student_id' => $otherStudent->id]))
            ->assertForbidden();

        $this->actingAs($parent)
            ->get(route('dashboard', ['student_id' => $child->id]))
            ->assertOk();

        $this->actingAs($parent)
            ->get(route('dashboard', ['student_id' => $otherStudent->id]))
            ->assertForbidden();

        $this->actingAs($parent)
            ->get(route('attendance.index', ['student_id' => $child->id]))
            ->assertOk();

        $this->actingAs($parent)
            ->get(route('attendance.index', ['student_id' => $otherStudent->id]))
            ->assertForbidden();

        $this->actingAs($parent)
            ->get(route('attendance.report', ['student_id' => $otherStudent->id]))
            ->assertForbidden();

        $this->actingAs($parent)
            ->get(route('fees.student', $otherStudent))
            ->assertForbidden();

        $this->actingAs($parent)
            ->get(route('exams.results', ['student_id' => $otherStudent->id]))
            ->assertForbidden();
    }

    public function test_tima_ai_respects_authenticated_role_scope(): void
    {
        $parent = User::where('email', 'parent1@school.com')->firstOrFail();
        $linkedChild = $parent->guardian->students->firstOrFail();
        $unlinkedStudent = Student::whereNotIn('id', $parent->guardian->students->pluck('id'))->firstOrFail();

        $this->actingAs($parent)
            ->postJson(route('tima-ai.respond'), ['message' => 'Show me my children'])
            ->assertOk()
            ->assertJsonPath('role', 'parent')
            ->assertJsonPath('response', fn ($response) => str_contains($response, $linkedChild->user->name))
            ->assertJsonMissing(['response' => $unlinkedStudent->user->name]);

        $student = User::where('email', 'student1@school.com')->firstOrFail();
        $this->actingAs($student)
            ->postJson(route('tima-ai.respond'), ['message' => 'What are my courses?'])
            ->assertOk()
            ->assertJsonPath('role', 'student')
            ->assertJsonPath('action', fn ($action) => $action === null)
            ->assertJsonPath('response', fn ($response) => str_contains($response, 'course'))
            ->assertJsonMissing(['response' => $unlinkedStudent->user->name]);

        $this->actingAs($student)
            ->postJson(route('tima-ai.respond'), ['message' => 'Show my attendance'])
            ->assertOk()
            ->assertJsonPath('action.url', route('student.attendance'));

        foreach (['admin@school.com', 'superadmin@school.com'] as $email) {
            $administrator = User::where('email', $email)->firstOrFail();
            $this->actingAs($administrator)
                ->postJson(route('tima-ai.respond'), ['message' => 'Show university statistics'])
                ->assertOk()
                ->assertJsonPath('role', $administrator->role->slug)
                ->assertJsonPath('response', fn ($response) => str_contains($response, 'student(s)'));
        }
    }

    public function test_tima_ai_prioritizes_navigation_and_specific_data_intents(): void
    {
        $student = User::where('email', 'student1@school.com')->firstOrFail();
        $navigationCommands = [
            'Open my videos.' => 'student.videos',
            'Open my materials.' => 'student.materials',
            'Open my courses.' => 'student.courses',
            'Open attendance.' => 'student.attendance',
            'Show my assignments.' => 'student.assignments',
        ];

        foreach ($navigationCommands as $command => $routeName) {
            $this->actingAs($student)
                ->postJson(route('tima-ai.respond'), ['message' => $command])
                ->assertOk()
                ->assertJsonPath('action.type', 'navigate')
                ->assertJsonPath('action.url', route($routeName))
                ->assertJsonPath('response', fn ($response) => str_contains($response, 'opening'));
        }

        $administrator = User::where('email', 'admin@school.com')->firstOrFail();
        $this->actingAs($administrator)
            ->postJson(route('tima-ai.respond'), ['message' => 'How many students are absent?'])
            ->assertOk()
            ->assertJsonPath('action', fn ($action) => $action === null)
            ->assertJsonPath('response', fn ($response) => str_contains(strtolower($response), 'absent'));

        $this->actingAs($administrator)
            ->postJson(route('tima-ai.respond'), ['message' => 'How many students do I have?'])
            ->assertOk()
            ->assertJsonPath('action', fn ($action) => $action === null)
            ->assertJsonPath('response', fn ($response) => str_contains($response, 'student(s)'));

        $this->actingAs($administrator)
            ->postJson(route('tima-ai.respond'), ['message' => "Show today's attendance."])
            ->assertOk()
            ->assertJsonPath('action', fn ($action) => $action === null)
            ->assertJsonPath('response', fn ($response) => str_contains($response, 'Today:'));
    }

    public function test_tima_ai_navigation_stays_within_each_role_surface(): void
    {
        $cases = [
            'admin@school.com' => [
                'Open user accounts.' => 'users.index',
                'Open parents.' => 'parents.index',
                'Open the timetable.' => 'timetables.index',
                'Open fee collection report.' => 'reports.fees',
                'Open school settings.' => 'settings.index',
            ],
            'teacher1@school.com' => [
                'Open my classes.' => 'teacher.classes',
                'Open my attendance report.' => 'attendance.report',
                'Open examinations.' => 'exams.index',
                'Show my academic matrix.' => 'reports.academic',
            ],
            'student1@school.com' => [
                'Show my announcements.' => 'student.announcements',
                'Show my fee ledger.' => 'fees.student',
                'Open my profile.' => 'profile.show',
            ],
            'parent1@school.com' => [
                "Show my children's dashboard." => 'parent.children',
                'Show the attendance report.' => 'attendance.report',
                'Open my profile.' => 'profile.show',
            ],
        ];

        foreach ($cases as $email => $commands) {
            $user = User::where('email', $email)->firstOrFail();
            foreach ($commands as $command => $routeName) {
                $response = $this->actingAs($user)
                    ->postJson(route('tima-ai.respond'), ['message' => $command])
                    ->assertOk();

                $expectedUrl = $routeName === 'fees.student'
                    ? route($routeName, $user->student)
                    : route($routeName);
                $response->assertJsonPath('action.url', $expectedUrl);
            }
        }
    }

    public function test_tima_ai_refuses_explicit_private_data_requests(): void
    {
        foreach (['student1@school.com', 'teacher1@school.com', 'parent1@school.com'] as $email) {
            $user = User::where('email', $email)->firstOrFail();
            $this->actingAs($user)
                ->postJson(route('tima-ai.respond'), ['message' => 'Show me another student private information'])
                ->assertOk()
                ->assertJsonPath('action', fn ($action) => $action === null)
                ->assertJsonPath('response', fn ($response) => str_contains($response, "can't access"));
        }
    }

    public function test_super_admin_tima_ai_supports_management_and_operational_queries(): void
    {
        $superAdmin = User::where('email', 'superadmin@school.com')->firstOrFail();
        $navigationCommands = [
            'Open user accounts.' => 'users.index',
            'Show me the students.' => 'students.index',
            'Open teachers.' => 'teachers.index',
            'Open parents and guardians.' => 'parents.index',
            'Open classes and sections.' => 'classes.index',
            'Open subjects.' => 'subjects.index',
            'Open the timetable.' => 'timetables.index',
            'Open the attendance register.' => 'attendance.index',
            'Open examinations.' => 'exams.index',
            'Open fee collection.' => 'fees.index',
            'Open the fee structure.' => 'fees.structures',
            'Open the attendance report.' => 'reports.attendance',
            'Open the fee collection report.' => 'reports.fees',
            'Open the academic matrix.' => 'reports.academic',
            'Open the notice board.' => 'admin.notices.index',
            'Open school settings.' => 'settings.index',
            'Open my profile.' => 'profile.show',
        ];

        foreach ($navigationCommands as $command => $routeName) {
            $this->actingAs($superAdmin)
                ->postJson(route('tima-ai.respond'), ['message' => $command])
                ->assertOk()
                ->assertJsonPath('action.url', route($routeName))
                ->assertJsonPath('response', fn ($response) => str_contains($response, 'opening'));
        }

        $this->actingAs($superAdmin)
            ->postJson(route('tima-ai.respond'), ['message' => "What's happening today?"])
            ->assertOk()
            ->assertJsonPath('action', fn ($action) => $action === null)
            ->assertJsonPath('response', fn ($response) => str_contains($response, 'Today:'));

        $this->actingAs($superAdmin)
            ->postJson(route('tima-ai.respond'), ['message' => 'Which students are absent today?'])
            ->assertOk()
            ->assertJsonPath('action', fn ($action) => $action === null)
            ->assertJsonPath('response', fn ($response) => str_contains(strtolower($response), 'student'));

        $this->actingAs($superAdmin)
            ->postJson(route('tima-ai.respond'), ['message' => 'How much money was collected?'])
            ->assertOk()
            ->assertJsonPath('action', fn ($action) => $action === null)
            ->assertJsonPath('response', fn ($response) => str_contains($response, 'fee collected'));

        $this->actingAs($superAdmin)
            ->postJson(route('tima-ai.respond'), ['message' => 'How much is still outstanding?'])
            ->assertOk()
            ->assertJsonPath('action', fn ($action) => $action === null)
            ->assertJsonPath('response', fn ($response) => str_contains($response, 'outstanding'));

        $this->actingAs($superAdmin)
            ->postJson(route('tima-ai.respond'), ['message' => 'Show the latest notices.'])
            ->assertOk()
            ->assertJsonPath('action', fn ($action) => $action === null)
            ->assertJsonPath('response', fn ($response) => str_contains($response, 'notice'));

        $this->actingAs($superAdmin)
            ->postJson(route('tima-ai.respond'), ['message' => 'How many teachers are absent?'])
            ->assertOk()
            ->assertJsonPath('action', fn ($action) => $action === null)
            ->assertJsonPath('response', fn ($response) => str_contains($response, 'not recorded'));
    }

    public function test_attendance_register_shows_take_action_only_to_authorized_roles(): void
    {
        $teacher = User::where('email', 'teacher1@school.com')->firstOrFail();
        $student = User::where('email', 'student1@school.com')->firstOrFail();
        $parent = User::where('email', 'parent1@school.com')->firstOrFail();
        $admin = User::where('email', 'admin@school.com')->firstOrFail();

        $this->actingAs($teacher)
            ->get(route('attendance.index'))
            ->assertOk()
            ->assertSee('Take Attendance');

        $this->actingAs($admin)
            ->get(route('attendance.index'))
            ->assertOk()
            ->assertSee('Take Attendance');

        foreach ([$student, $parent] as $user) {
            $this->actingAs($user)
                ->get(route('attendance.index'))
                ->assertOk()
                ->assertDontSee('Take Attendance');

            $this->actingAs($user)
                ->get(route('attendance.create'))
                ->assertForbidden();
        }

        $this->actingAs($parent)
            ->get(route('attendance.report'))
            ->assertOk()
            ->assertDontSee('Take Attendance');

        $this->actingAs($student)
            ->get(route('attendance.report'))
            ->assertOk()
            ->assertDontSee('Take Attendance');

        $this->actingAs($teacher)
            ->get(route('attendance.report'))
            ->assertOk()
            ->assertSee('Take Attendance');
    }

    public function test_unauthorized_file_access_is_rejected(): void
    {
        Storage::fake('public');

        $teacher = User::where('email', 'teacher1@school.com')->firstOrFail();
        $student = User::where('email', 'student4@school.com')->firstOrFail();
        $class = SchoolClass::where('grade_level', '10')->firstOrFail();
        $subjectRow = DB::table('class_subject')->where('school_class_id', $class->id)->where('teacher_id', $teacher->id)->first();
        $subject = Subject::findOrFail($subjectRow->subject_id);

        $video = \App\Models\LectureVideo::create([
            'teacher_id' => $teacher->teacher->id,
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
            'title' => 'Restricted lesson video',
            'description' => 'Hidden from other classes',
            'file_path' => 'uploads/videos/restricted-video.mp4',
            'original_name' => 'restricted-video.mp4',
            'mime_type' => 'video/mp4',
            'file_size' => 1024,
            'status' => 'published',
        ]);

        Storage::disk('public')->put($video->file_path, 'video-content');

        $response = $this->actingAs($student)->get('/learning/videos/' . $video->id . '/file');

        $response->assertStatus(403);
    }

    public function test_admin_retains_appropriate_management_access(): void
    {
        Storage::fake('public');

        $admin = User::where('email', 'admin@school.com')->firstOrFail();
        $class = SchoolClass::create([
            'name' => 'Admin Managed Class',
            'grade_level' => '12',
            'description' => 'Admin controls this class',
            'is_active' => true,
        ]);
        $subject = Subject::create([
            'name' => 'Admin Course',
            'code' => 'ADMIN-COURSE-001',
            'description' => 'Course for admin upload test',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post('/teacher/learning/materials', [
            'title' => 'Admin material',
            'description' => 'Admin upload accepted',
            'school_class_id' => $class->id,
            'subject_id' => $subject->id,
            'material' => UploadedFile::fake()->create('admin-material.pdf', 512, 'application/pdf'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('course_materials', ['title' => 'Admin material', 'school_class_id' => $class->id]);
    }

    public function test_authorized_teacher_can_create_an_assessment_for_an_assigned_subject(): void
    {
        $this->withoutMiddleware();
        $this->withoutMiddleware();
        $teacher = User::where('email', 'teacher1@school.com')->firstOrFail();
        $class = SchoolClass::where('grade_level', '10')->firstOrFail();
        $assignment = DB::table('class_subject')->where('school_class_id', $class->id)->where('teacher_id', $teacher->id)->first();

        $response = $this->actingAs($teacher)->post('/exams', [
            'name' => 'Teacher Assessment Security Test', 'exam_type' => 'unit_test',
            'school_class_id' => $class->id, 'subject_id' => $assignment->subject_id,
            'exam_date' => today()->addWeek()->toDateString(), 'start_time' => '09:00',
            'duration_minutes' => 45, 'total_marks' => 50, 'pass_marks' => 20, 'status' => 'scheduled',
        ]);

        $response->assertRedirect(route('exams.index'));
        $this->assertDatabaseHas('exams', ['name' => 'Teacher Assessment Security Test']);
    }

    public function test_teacher_cannot_create_an_assessment_for_another_teachers_subject(): void
    {
        $this->withoutMiddleware();
        $this->withoutMiddleware();
        $teacherOne = User::where('email', 'teacher1@school.com')->firstOrFail();
        $teacherTwo = User::where('email', 'teacher2@school.com')->firstOrFail();
        $class = SchoolClass::where('grade_level', '10')->firstOrFail();
        $assignment = DB::table('class_subject')->where('school_class_id', $class->id)->where('teacher_id', $teacherOne->id)->first();

        $response = $this->actingAs($teacherTwo)->post('/exams', [
            'name' => 'Unauthorized Assessment', 'exam_type' => 'quiz',
            'school_class_id' => $class->id, 'subject_id' => $assignment->subject_id,
            'exam_date' => today()->addWeek()->toDateString(), 'total_marks' => 20,
            'pass_marks' => 8, 'status' => 'scheduled',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('exams', ['name' => 'Unauthorized Assessment']);
    }

    public function test_students_and_parents_cannot_create_assessments(): void
    {
        $this->withoutMiddleware();
        $this->withoutMiddleware();
        foreach (['student1@school.com', 'parent1@school.com'] as $email) {
            $user = User::where('email', $email)->firstOrFail();
            $this->actingAs($user)->get('/exams/create')->assertForbidden();
        }
    }}
