<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class AssignmentSubmissionTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_student_can_submit_assignment_and_teacher_can_grade_owned_submission(): void
    {
        Storage::fake('private');
        $this->seed();
        $studentRole = Role::where('slug', Role::STUDENT)->firstOrFail();
        $teacherRole = Role::where('slug', Role::TEACHER)->firstOrFail();
        $class = SchoolClass::create(['name' => 'Submission Class']);
        $section = Section::create(['school_class_id' => $class->id, 'name' => 'A']);
        $subject = Subject::create(['name' => 'Submission Subject', 'code' => 'SUB-1']);
        $teacherUser = User::factory()->create(['role_id' => $teacherRole->id]);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'employee_id' => 'SUB-T', 'class_teacher_of' => $class->id]);
        $studentUser = User::factory()->create(['role_id' => $studentRole->id]);
        $student = Student::create(['user_id' => $studentUser->id, 'roll_number' => 'SUB-S', 'admission_number' => 'SUB-A', 'school_class_id' => $class->id, 'section_id' => $section->id, 'admission_date' => now(), 'status' => 'active']);
        $assignment = Assignment::create(['teacher_id' => $teacher->id, 'school_class_id' => $class->id, 'subject_id' => $subject->id, 'title' => 'Essay', 'status' => 'published', 'due_date' => now()->addDay(), 'max_attempts' => 2, 'max_points' => 20]);

        $this->actingAs($studentUser)->post(route('student.assignments.submit', $assignment), ['submission_file' => UploadedFile::fake()->create('essay.pdf', 10, 'application/pdf')])->assertRedirect();
        $this->assertDatabaseHas('assignment_submissions', ['assignment_id' => $assignment->id, 'student_id' => $student->id, 'status' => 'submitted']);
        $submission = $assignment->submissions()->firstOrFail();
        $this->actingAs($teacherUser)->post(route('teacher.learning.submission.grade', $submission), ['score' => 18, 'feedback' => 'Good work'])->assertRedirect();
        $this->assertDatabaseHas('assignment_submissions', ['id' => $submission->id, 'status' => 'graded', 'score' => 18]);
    }

    public function test_late_submission_is_preserved_and_marked_late(): void
    {
        Storage::fake('private');
        $this->seed();
        $studentRole = Role::where('slug', Role::STUDENT)->firstOrFail();
        $class = SchoolClass::create(['name' => 'Late Class']);
        $section = Section::create(['school_class_id' => $class->id, 'name' => 'A']);
        $subject = Subject::create(['name' => 'Late Subject', 'code' => 'LATE-1']);
        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', Role::TEACHER)->value('id')]);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'employee_id' => 'LATE-T', 'class_teacher_of' => $class->id]);
        $studentUser = User::factory()->create(['role_id' => $studentRole->id]);
        $student = Student::create(['user_id' => $studentUser->id, 'roll_number' => 'LATE-S', 'admission_number' => 'LATE-A', 'school_class_id' => $class->id, 'section_id' => $section->id, 'admission_date' => now(), 'status' => 'active']);
        $assignment = Assignment::create(['teacher_id' => $teacher->id, 'school_class_id' => $class->id, 'subject_id' => $subject->id, 'title' => 'Late Essay', 'status' => 'published', 'due_date' => now()->subDay(), 'max_attempts' => 1]);

        $this->actingAs($studentUser)->post(route('student.assignments.submit', $assignment), ['submission_file' => UploadedFile::fake()->create('late.pdf', 10, 'application/pdf')])->assertRedirect();
        $this->assertDatabaseHas('assignment_submissions', ['assignment_id' => $assignment->id, 'student_id' => $student->id, 'is_late' => 1]);
    }

    public function test_student_can_update_only_a_draft_and_submitted_attempts_remain_immutable(): void
    {
        Storage::fake('private');
        $this->seed();
        $studentRole = Role::where('slug', Role::STUDENT)->firstOrFail();
        $class = SchoolClass::create(['name' => 'Draft Class']);
        $section = Section::create(['school_class_id' => $class->id, 'name' => 'A']);
        $subject = Subject::create(['name' => 'Draft Subject', 'code' => 'DRAFT-1']);
        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', Role::TEACHER)->value('id')]);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'employee_id' => 'DRAFT-T', 'class_teacher_of' => $class->id]);
        $studentUser = User::factory()->create(['role_id' => $studentRole->id]);
        $student = Student::create(['user_id' => $studentUser->id, 'roll_number' => 'DRAFT-S', 'admission_number' => 'DRAFT-A', 'school_class_id' => $class->id, 'section_id' => $section->id, 'admission_date' => now(), 'status' => 'active']);
        $assignment = Assignment::create(['teacher_id' => $teacher->id, 'school_class_id' => $class->id, 'subject_id' => $subject->id, 'title' => 'Draft essay', 'status' => 'published', 'max_attempts' => 2]);
        $draft = AssignmentSubmission::create(['assignment_id' => $assignment->id, 'student_id' => $student->id, 'attempt_number' => 1, 'status' => 'draft']);

        $this->actingAs($studentUser)->put(route('student.assignments.submissions.update', $draft), [
            'status' => 'submitted',
            'submission_file' => UploadedFile::fake()->create('draft-final.pdf', 10, 'application/pdf'),
        ])->assertRedirect();
        $this->assertDatabaseHas('assignment_submissions', ['id' => $draft->id, 'status' => 'submitted']);

        $this->actingAs($studentUser)->put(route('student.assignments.submissions.update', $draft), [
            'status' => 'draft',
        ])->assertStatus(422);
        $this->assertDatabaseHas('assignment_submissions', ['id' => $draft->id, 'status' => 'submitted']);
    }

    public function test_repeated_draft_saves_reuse_one_attempt_and_resubmission_uses_next_attempt(): void
    {
        Storage::fake('private');
        $this->seed();
        $studentRole = Role::where('slug', Role::STUDENT)->firstOrFail();
        $class = SchoolClass::create(['name' => 'Repeat Class']);
        $section = Section::create(['school_class_id' => $class->id, 'name' => 'A']);
        $subject = Subject::create(['name' => 'Repeat Subject', 'code' => 'REPEAT-1']);
        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', Role::TEACHER)->value('id')]);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'employee_id' => 'REPEAT-T', 'class_teacher_of' => $class->id]);
        $studentUser = User::factory()->create(['role_id' => $studentRole->id]);
        $student = Student::create(['user_id' => $studentUser->id, 'roll_number' => 'REPEAT-S', 'admission_number' => 'REPEAT-A', 'school_class_id' => $class->id, 'section_id' => $section->id, 'admission_date' => now(), 'status' => 'active']);
        $assignment = Assignment::create(['teacher_id' => $teacher->id, 'school_class_id' => $class->id, 'subject_id' => $subject->id, 'title' => 'Repeat essay', 'status' => 'published', 'max_attempts' => 2]);

        $this->actingAs($studentUser)->post(route('student.assignments.submit', $assignment), ['status' => 'draft', 'submission_file' => UploadedFile::fake()->create('draft-1.pdf', 10, 'application/pdf')])->assertRedirect();
        $this->actingAs($studentUser)->post(route('student.assignments.submit', $assignment), ['status' => 'draft', 'submission_file' => UploadedFile::fake()->create('draft-2.pdf', 10, 'application/pdf')])->assertRedirect();
        $this->assertDatabaseCount('assignment_submissions', 1);
        $this->assertDatabaseHas('assignment_submissions', ['assignment_id' => $assignment->id, 'attempt_number' => 1, 'status' => 'draft']);

        $this->actingAs($studentUser)->post(route('student.assignments.submit', $assignment), ['status' => 'submitted', 'submission_file' => UploadedFile::fake()->create('final-1.pdf', 10, 'application/pdf')])->assertRedirect();
        $this->actingAs($studentUser)->post(route('student.assignments.submit', $assignment), ['status' => 'submitted', 'submission_file' => UploadedFile::fake()->create('final-2.pdf', 10, 'application/pdf')])->assertRedirect();
        $this->assertDatabaseHas('assignment_submissions', ['assignment_id' => $assignment->id, 'attempt_number' => 1, 'status' => 'submitted']);
        $this->assertDatabaseHas('assignment_submissions', ['assignment_id' => $assignment->id, 'attempt_number' => 2, 'status' => 'submitted']);
        $this->actingAs($studentUser)->post(route('student.assignments.submit', $assignment), ['status' => 'submitted', 'submission_file' => UploadedFile::fake()->create('final-3.pdf', 10, 'application/pdf')])->assertStatus(422);
        $this->assertDatabaseCount('assignment_submissions', 2);
    }
}
