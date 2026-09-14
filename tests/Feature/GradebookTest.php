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
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class GradebookTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_teacher_gradebook_is_scoped_and_uses_latest_graded_attempt(): void
    {
        $this->seed();
        $teacherRole = Role::where('slug', Role::TEACHER)->firstOrFail();
        $studentRole = Role::where('slug', Role::STUDENT)->firstOrFail();
        $class = SchoolClass::create(['name' => 'Gradebook Class']);
        $section = Section::create(['school_class_id' => $class->id, 'name' => 'A']);
        $subject = Subject::create(['name' => 'Gradebook Subject', 'code' => 'GB-1']);
        $teacherUser = User::factory()->create(['role_id' => $teacherRole->id]);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'employee_id' => 'GB-T']);
        $otherTeacherUser = User::factory()->create(['role_id' => $teacherRole->id]);
        $otherTeacher = Teacher::create(['user_id' => $otherTeacherUser->id, 'employee_id' => 'GB-O']);
        $studentUser = User::factory()->create(['role_id' => $studentRole->id, 'name' => 'Gradebook Student']);
        $student = Student::create([
            'user_id' => $studentUser->id, 'roll_number' => 'GB-S', 'admission_number' => 'GB-A',
            'school_class_id' => $class->id, 'section_id' => $section->id, 'admission_date' => now(), 'status' => 'active',
        ]);
        $assignment = Assignment::create([
            'teacher_id' => $teacher->id, 'school_class_id' => $class->id, 'subject_id' => $subject->id,
            'title' => 'Gradebook activity', 'status' => 'published', 'max_points' => 10,
        ]);
        $otherAssignment = Assignment::create([
            'teacher_id' => $otherTeacher->id, 'school_class_id' => $class->id, 'subject_id' => $subject->id,
            'title' => 'Other teacher activity', 'status' => 'published', 'max_points' => 10,
        ]);
        AssignmentSubmission::create([
            'assignment_id' => $assignment->id, 'student_id' => $student->id, 'attempt_number' => 1,
            'status' => 'graded', 'submitted_at' => now()->subDay(), 'score' => 4, 'graded_at' => now()->subDay(),
        ]);
        AssignmentSubmission::create([
            'assignment_id' => $assignment->id, 'student_id' => $student->id, 'attempt_number' => 2,
            'status' => 'graded', 'submitted_at' => now(), 'score' => 8, 'graded_at' => now(),
        ]);

        $this->actingAs($teacherUser)->get(route('teacher.gradebook'))
            ->assertOk()
            ->assertSee('Gradebook activity')
            ->assertSee('8')
            ->assertDontSee('Other teacher activity');
        $this->actingAs($otherTeacherUser)->get(route('teacher.gradebook'))
            ->assertOk()
            ->assertSee('Other teacher activity')
            ->assertDontSee('Gradebook activity');
    }

    public function test_student_gradebook_only_shows_the_authenticated_students_published_work(): void
    {
        $this->seed();
        $studentRole = Role::where('slug', Role::STUDENT)->firstOrFail();
        $class = SchoolClass::create(['name' => 'Student Gradebook Class']);
        $section = Section::create(['school_class_id' => $class->id, 'name' => 'A']);
        $subject = Subject::create(['name' => 'Student Gradebook Subject', 'code' => 'SGB-1']);
        $user = User::factory()->create(['role_id' => $studentRole->id]);
        $student = Student::create([
            'user_id' => $user->id, 'roll_number' => 'SGB-S', 'admission_number' => 'SGB-A',
            'school_class_id' => $class->id, 'section_id' => $section->id, 'admission_date' => now(), 'status' => 'active',
        ]);
        $teacherUser = User::factory()->create(['role_id' => Role::where('slug', Role::TEACHER)->firstOrFail()->id]);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'employee_id' => 'SGB-T']);
        $published = Assignment::create([
            'teacher_id' => $teacher->id, 'school_class_id' => $class->id, 'subject_id' => $subject->id,
            'title' => 'Published result', 'status' => 'published', 'max_points' => 20,
        ]);
        Assignment::create([
            'teacher_id' => $teacher->id, 'school_class_id' => $class->id, 'subject_id' => $subject->id,
            'title' => 'Draft activity', 'status' => 'draft', 'max_points' => 20,
        ]);
        AssignmentSubmission::create([
            'assignment_id' => $published->id, 'student_id' => $student->id, 'attempt_number' => 1,
            'status' => 'graded', 'submitted_at' => now(), 'score' => 15, 'graded_at' => now(), 'feedback' => 'Good work',
        ]);

        $this->actingAs($user)->get(route('student.gradebook'))
            ->assertOk()
            ->assertSee('Published result')
            ->assertSee('15')
            ->assertSee('Good work')
            ->assertDontSee('Draft activity');
    }
}
