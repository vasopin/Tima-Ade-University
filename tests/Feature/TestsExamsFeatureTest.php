<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\TestOption;
use App\Models\TestAttempt;
use App\Models\TestAnswer;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Section;
use App\Models\Role;
use \Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class TestsExamsFeatureTest extends TestCase
{
    use \Tests\Concerns\ForceRefreshDatabase;

    private $superAdmin;
    private $admin;
    private $teacher;
    private $student;
    private $schoolClass;
    private $subject;
    private $section;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $superAdminRole = Role::create(['name' => 'Super Admin', 'slug' => 'super_admin']);
        $adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $teacherRole = Role::create(['name' => 'Teacher', 'slug' => 'teacher']);
        $studentRole = Role::create(['name' => 'Student', 'slug' => 'student']);

        // Create users
        $this->superAdmin = User::create([
            'role_id' => $superAdminRole->id,
            'name' => 'Super Admin',
            'email' => 'super@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->admin = User::create([
            'role_id' => $adminRole->id,
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $teacherUser = User::create([
            'role_id' => $teacherRole->id,
            'name' => 'Teacher',
            'email' => 'teacher@test.com',
            'password' => bcrypt('password'),
        ]);

        $studentUser = User::create([
            'role_id' => $studentRole->id,
            'name' => 'Student',
            'email' => 'student@test.com',
            'password' => bcrypt('password'),
        ]);

        // Create school class
        $this->schoolClass = SchoolClass::create([
            'name' => 'Class 10-A',
            'level' => 10,
            'is_active' => true,
        ]);

        // Create section
        $this->section = Section::create([
            'school_class_id' => $this->schoolClass->id,
            'name' => 'Section A',
        ]);

        // Create subject
        $this->subject = Subject::create([
            'name' => 'Mathematics',
            'code' => 'MATH101',
            'is_active' => true,
        ]);

        // Link subject to class
        DB::table('class_subject')->insert([
            'school_class_id' => $this->schoolClass->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $teacherUser->id,
        ]);

        // Create teacher profile
        $this->teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'employee_id' => 'EMP001',
            'joining_date' => now(),
        ]);

        // Create student profile
        $this->student = Student::create([
            'user_id' => $studentUser->id,
            'roll_number' => 'ROLL001',
            'admission_number' => 'ADM001',
            'school_class_id' => $this->schoolClass->id,
            'section_id' => $this->section->id,
            'admission_date' => now(),
            'status' => 'active',
        ]);
    }

    /**
     * Test: Teacher can create a test for authorized course
     */
    public function test_teacher_and_student_test_index_pages_render(): void
    {
        $test = Test::create([
            'teacher_id' => $this->teacher->user_id,
            'school_class_id' => $this->schoolClass->id,
            'subject_id' => $this->subject->id,
            'title' => 'Index Page Test',
            'status' => 'published',
            'publish_date' => now()->subHour(),
        ]);

        TestAttempt::create([
            'test_id' => $test->id,
            'student_id' => $this->student->id,
            'attempt_number' => 1,
            'started_at' => now(),
            'submitted_at' => now(),
            'status' => 'results_released',
            'score' => 90,
            'is_passed' => true,
        ]);

        $this->actingAs($this->teacher->user);
        $this->get('/teacher/tests')->assertStatus(200);

        $this->actingAs($this->student->user);
        $this->get('/student/tests')->assertStatus(200);
    }

    public function test_teacher_can_create_test_for_authorized_course(): void
    {
        $this->actingAs($this->teacher->user);

        $response = $this->post('/teacher/tests', [
            'school_class_id' => $this->schoolClass->id,
            'subject_id' => $this->subject->id,
            'title' => 'Mathematics Test',
            'instructions' => 'Answer all questions',
            'total_marks' => 100,
            'pass_marks' => 40,
            'duration_minutes' => 60,
            'attempt_limit' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tests', [
            'teacher_id' => $this->teacher->user_id,
            'school_class_id' => $this->schoolClass->id,
            'title' => 'Mathematics Test',
            'status' => 'draft',
        ]);
    }

    /**
     * Test: Teacher cannot create test for unauthorized course
     */
    public function test_teacher_cannot_create_test_for_unauthorized_course(): void
    {
        // Create another class teacher is not authorized for
        $otherClass = SchoolClass::create(['name' => 'Class 11-B', 'level' => 11, 'is_active' => true]);
        Section::create(['school_class_id' => $otherClass->id, 'name' => 'Section A']);

        $this->actingAs($this->teacher->user);

        $response = $this->post('/teacher/tests', [
            'school_class_id' => $otherClass->id,
            'subject_id' => $this->subject->id,
            'title' => 'Unauthorized Test',
            'instructions' => 'Test',
            'total_marks' => 100,
            'pass_marks' => 40,
            'duration_minutes' => 60,
            'attempt_limit' => 1,
        ]);

        // Should fail validation  
        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('tests', ['title' => 'Unauthorized Test']);
    }

    /**
     * Test: Student can only access test for enrolled course
     */
    public function test_student_can_only_access_enrolled_course_test(): void
    {
        $test = Test::create([
            'teacher_id' => $this->teacher->user_id,
            'school_class_id' => $this->schoolClass->id,
            'subject_id' => $this->subject->id,
            'title' => 'Enrolled Course Test',
            'status' => 'published',
            'publish_date' => now()->subHour(),
        ]);

        $this->actingAs($this->student->user);
        $response = $this->get("/student/tests/{$test->id}");

        $response->assertStatus(200);
    }

    /**
     * Test: Student cannot access test for non-enrolled course
     */
    public function test_student_cannot_access_non_enrolled_course_test(): void
    {
        $otherClass = SchoolClass::create(['name' => 'Class 11-B', 'level' => 11, 'is_active' => true]);
        Section::create(['school_class_id' => $otherClass->id, 'name' => 'Section A']);
        
        $test = Test::create([
            'teacher_id' => $this->teacher->user_id,
            'school_class_id' => $otherClass->id,
            'subject_id' => $this->subject->id,
            'title' => 'Other Class Test',
            'status' => 'published',
            'publish_date' => now()->subHour(),
        ]);

        $this->actingAs($this->student->user);
        $response = $this->get("/student/tests/{$test->id}");

        $response->assertStatus(403);
    }

    /**
     * Test: Test can only be taken if published and available
     */
    public function test_test_can_only_be_taken_if_published_and_available(): void
    {
        $draftTest = Test::create([
            'teacher_id' => $this->teacher->user_id,
            'school_class_id' => $this->schoolClass->id,
            'subject_id' => $this->subject->id,
            'title' => 'Draft Test',
            'status' => 'draft',
        ]);

        $this->actingAs($this->student->user);
        $response = $this->post("/student/tests/{$draftTest->id}/start");

        $response->assertStatus(403);
    }

    /**
     * Test: Auto-grading works for MCQ questions
     */
    public function test_auto_grading_for_mcq_questions(): void
    {
        $test = Test::create([
            'teacher_id' => $this->teacher->user_id,
            'school_class_id' => $this->schoolClass->id,
            'subject_id' => $this->subject->id,
            'title' => 'MCQ Test',
            'total_marks' => 1,
            'pass_marks' => 1,
            'status' => 'published',
            'publish_date' => now()->subHour(),
        ]);

        $question = TestQuestion::create([
            'test_id' => $test->id,
            'question_text' => 'What is 2+2?',
            'question_type' => 'multiple_choice',
            'marks' => 1,
        ]);

        TestOption::create([
            'test_question_id' => $question->id,
            'option_text' => '4',
            'is_correct' => true,
        ]);

        TestOption::create([
            'test_question_id' => $question->id,
            'option_text' => '5',
            'is_correct' => false,
        ]);

        // Student takes test
        $this->actingAs($this->student->user);
        $attempt = TestAttempt::create([
            'test_id' => $test->id,
            'student_id' => $this->student->id,
            'attempt_number' => 1,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        // Select correct option
        $correctOption = $question->options()->where('is_correct', true)->first();
        TestAnswer::create([
            'test_attempt_id' => $attempt->id,
            'test_question_id' => $question->id,
            'selected_option_id' => $correctOption->id,
        ]);

        // Submit (triggers auto-grading)
        $response = $this->post("/student/tests/attempt/{$attempt->id}/submit");

        // Verify auto-grading
        $attempt = $attempt->refresh();
        $this->assertEquals(1, $attempt->score);
        $this->assertTrue($attempt->is_passed);

        $answer = TestAnswer::where('test_attempt_id', $attempt->id)->first();
        $this->assertTrue($answer->is_correct);
    }

    /**
     * Test: Attempt limit is enforced
     */
    public function test_attempt_limit_is_enforced(): void
    {
        $test = Test::create([
            'teacher_id' => $this->teacher->user_id,
            'school_class_id' => $this->schoolClass->id,
            'subject_id' => $this->subject->id,
            'title' => 'Limited Attempts Test',
            'attempt_limit' => 1,
            'status' => 'published',
            'publish_date' => now()->subHour(),
        ]);

        // Create and submit first attempt
        $attempt1 = TestAttempt::create([
            'test_id' => $test->id,
            'student_id' => $this->student->id,
            'attempt_number' => 1,
            'started_at' => now(),
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        // Try to start second attempt
        $this->actingAs($this->student->user);
        $response = $this->post("/student/tests/{$test->id}/start");

        $response->assertStatus(403);
        $this->assertDatabaseMissing('test_attempts', [
            'test_id' => $test->id,
            'student_id' => $this->student->id,
            'attempt_number' => 2,
        ]);
    }

    /**
     * Test: Student cannot access another student's attempt
     */
    public function test_student_cannot_access_another_students_attempt(): void
    {
        $otherStudent = Student::create([
            'user_id' => User::create([
                'role_id' => Role::where('slug', 'student')->first()->id,
                'name' => 'Other Student',
                'email' => 'otherstudent@test.com',
                'password' => bcrypt('password'),
            ])->id,
            'roll_number' => 'ROLL002',
            'admission_number' => 'ADM002',
            'school_class_id' => $this->schoolClass->id,
            'section_id' => $this->section->id,
            'admission_date' => now(),
            'status' => 'active',
        ]);

        $test = Test::create([
            'teacher_id' => $this->teacher->user_id,
            'school_class_id' => $this->schoolClass->id,
            'subject_id' => $this->subject->id,
            'title' => 'Test',
            'status' => 'published',
            'publish_date' => now()->subHour(),
        ]);

        $otherAttempt = TestAttempt::create([
            'test_id' => $test->id,
            'student_id' => $otherStudent->id,
            'attempt_number' => 1,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        $this->actingAs($this->student->user);
        $response = $this->get("/student/tests/attempt/{$otherAttempt->id}");

        $response->assertStatus(403);
    }

    /**
     * Test: Results only visible when released
     */
    public function test_results_only_visible_when_released(): void
    {
        $test = Test::create([
            'teacher_id' => $this->teacher->user_id,
            'school_class_id' => $this->schoolClass->id,
            'subject_id' => $this->subject->id,
            'title' => 'Test Without Results Released',
            'status' => 'published',
            'publish_date' => now()->subHour(),
        ]);

        $attempt = TestAttempt::create([
            'test_id' => $test->id,
            'student_id' => $this->student->id,
            'attempt_number' => 1,
            'started_at' => now(),
            'submitted_at' => now(),
            'status' => 'graded',
            'score' => 50,
        ]);

        // Try to view results before release
        $this->actingAs($this->student->user);
        $response = $this->get("/student/tests/{$test->id}/results");

        $response->assertStatus(403);

        // Release results
        $this->actingAs($this->teacher->user);
        $this->post("/teacher/tests/{$test->id}/release-results");

        // Now results should be visible
        $this->actingAs($this->student->user);
        $response = $this->get("/student/tests/{$test->id}/results");

        $response->assertStatus(200);
    }

    /**
     * Test: Non-authenticated user cannot access tests
     */
    public function test_non_authenticated_user_cannot_access_tests(): void
    {
        $response = $this->get('/student/tests');
        $response->assertRedirect('/login');
    }

    /**
     * Test: Staff cannot create tests
     */
    public function test_staff_cannot_create_tests(): void
    {
        $staffRole = Role::create(['name' => 'Staff', 'slug' => 'staff']);
        $staffUser = User::create([
            'role_id' => $staffRole->id,
            'name' => 'Staff',
            'email' => 'staff@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($staffUser);
        $response = $this->post('/teacher/tests', [
            'school_class_id' => $this->schoolClass->id,
            'subject_id' => $this->subject->id,
            'title' => 'Staff Test',
        ]);

        $response->assertStatus(403);
    }
}
