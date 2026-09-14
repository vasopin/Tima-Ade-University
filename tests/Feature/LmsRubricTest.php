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

class LmsRubricTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_assignment_owner_can_create_and_apply_rubric_scores(): void
    {
        Storage::fake('private');
        $this->seed();
        $teacherRole = Role::where('slug', Role::TEACHER)->firstOrFail();
        $studentRole = Role::where('slug', Role::STUDENT)->firstOrFail();
        $class = SchoolClass::create(['name' => 'Rubric Class']);
        $section = Section::create(['school_class_id' => $class->id, 'name' => 'A']);
        $subject = Subject::create(['name' => 'Rubric Subject', 'code' => 'RUB-1']);
        $teacherUser = User::factory()->create(['role_id' => $teacherRole->id]);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'employee_id' => 'RUB-T', 'class_teacher_of' => $class->id]);
        $studentUser = User::factory()->create(['role_id' => $studentRole->id]);
        $student = Student::create(['user_id' => $studentUser->id, 'roll_number' => 'RUB-S', 'admission_number' => 'RUB-A', 'school_class_id' => $class->id, 'section_id' => $section->id, 'admission_date' => now(), 'status' => 'active']);
        $assignment = Assignment::create(['teacher_id' => $teacher->id, 'school_class_id' => $class->id, 'subject_id' => $subject->id, 'title' => 'Rubric essay', 'status' => 'published', 'max_points' => 10]);
        $submission = AssignmentSubmission::create(['assignment_id' => $assignment->id, 'student_id' => $student->id, 'attempt_number' => 1, 'status' => 'submitted', 'submitted_at' => now()]);

        $this->actingAs($teacherUser)->post(route('teacher.learning.assignment.rubric.store', $assignment), [
            'name' => 'Essay rubric',
            'criteria' => [['name' => 'Analysis', 'max_points' => 6], ['name' => 'Clarity', 'max_points' => 4]],
        ])->assertRedirect();
        $rubric = $assignment->rubric()->with('criteria')->firstOrFail();
        $otherTeacherUser = User::factory()->create(['role_id' => $teacherRole->id]);
        Teacher::create(['user_id' => $otherTeacherUser->id, 'employee_id' => 'RUB-OTHER']);
        $this->actingAs($otherTeacherUser)->post(route('teacher.learning.assignment.rubric.store', $assignment), [
            'name' => 'Unauthorized rubric',
            'criteria' => [['name' => 'Analysis', 'max_points' => 10]],
        ])->assertForbidden();
        $this->actingAs($studentUser)->post(route('teacher.learning.submission.grade', $submission), [
            'score' => 5,
        ])->assertForbidden();
        $this->actingAs($teacherUser)->post(route('teacher.learning.submission.grade', $submission), [
            'rubric_scores' => [
                ['criterion_id' => $rubric->criteria[0]->id, 'awarded_points' => 7],
                ['criterion_id' => $rubric->criteria[1]->id, 'awarded_points' => 3],
            ],
        ])->assertStatus(422);
        $this->actingAs($teacherUser)->post(route('teacher.learning.submission.grade', $submission), [
            'rubric_scores' => [
                ['criterion_id' => $rubric->criteria[0]->id, 'awarded_points' => 5],
                ['criterion_id' => $rubric->criteria[1]->id, 'awarded_points' => 3],
            ],
        ])->assertRedirect();
        $this->assertDatabaseHas('assignment_submissions', ['id' => $submission->id, 'score' => 8, 'status' => 'graded']);
        $this->assertDatabaseCount('rubric_scores', 2);
    }
}
