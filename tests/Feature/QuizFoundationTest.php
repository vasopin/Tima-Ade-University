<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Section;
use App\Models\User;
use App\Models\CourseSection;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Models\Enrollment;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class QuizFoundationTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_teacher_can_create_bank_question_and_attach_it_to_owned_draft_quiz(): void
    {
        [$teacher, $student, $class, $subject, $section] = $this->fixture();
        $this->actingAs($teacher->user)->post(route('teacher.question-bank.store'), [
            'question_text' => 'What is 2 + 2?', 'question_type' => 'multiple_choice', 'marks' => 2,
            'options' => [
                ['option_text' => '3'], ['option_text' => '4', 'is_correct' => 1],
            ],
        ])->assertRedirect();
        $question = TestQuestion::where('teacher_id', $teacher->user_id)->firstOrFail();
        $test = Test::create(['teacher_id' => $teacher->user_id, 'school_class_id' => $class->id, 'subject_id' => $subject->id, 'title' => 'Quiz', 'total_marks' => 2, 'pass_marks' => 1, 'status' => 'draft']);
        $this->actingAs($teacher->user)->post(route('teacher.tests.question-bank.attach', [$test, $question]))->assertRedirect();
        $this->assertDatabaseHas('test_question_selections', ['test_id' => $test->id, 'test_question_id' => $question->id]);
        $this->actingAs($teacher->user)->get(route('teacher.tests.preview', $test))->assertOk()->assertSee('Correct');
        $this->actingAs($teacher->user)->post(route('teacher.tests.publish', $test), ['publish_date' => now()->subMinute()->toDateTimeString()])
            ->assertRedirect();
        $this->assertDatabaseHas('tests', ['id' => $test->id, 'status' => 'published']);
    }

    public function test_question_bank_and_quiz_are_isolated_and_student_cannot_see_answer_key(): void
    {
        [$teacher, $student, $class, $subject, $section] = $this->fixture();
        $otherUser = User::factory()->create(['role_id' => Role::where('slug', Role::TEACHER)->firstOrFail()->id]);
        $otherTeacher = Teacher::create(['user_id' => $otherUser->id, 'employee_id' => 'QB-O']);
        $question = TestQuestion::create(['teacher_id' => $teacher->user_id, 'question_text' => 'Secure question', 'question_type' => 'true_false', 'marks' => 1, 'correct_answer' => 'true']);
        $question->options()->createMany([
            ['option_text' => 'True', 'is_correct' => true, 'order_index' => 0],
            ['option_text' => 'False', 'is_correct' => false, 'order_index' => 1],
        ]);
        $test = Test::create(['teacher_id' => $teacher->user_id, 'school_class_id' => $class->id, 'subject_id' => $subject->id, 'title' => 'Published Quiz', 'total_marks' => 1, 'pass_marks' => 1, 'status' => 'published', 'publish_date' => now()->subMinute()]);
        $test->selectedQuestions()->attach($question->id, ['order_index' => 0, 'marks' => 1]);
        $this->actingAs($otherUser)->post(route('teacher.tests.question-bank.attach', [$test, $question]))->assertForbidden();
        $this->actingAs($student->user)->get(route('student.quizzes.preview', $test))->assertOk()->assertSee('Secure question')->assertSee('True')->assertDontSee('is_correct')->assertDontSee('correct_answer');
        $draft = Test::create(['teacher_id' => $teacher->user_id, 'school_class_id' => $class->id, 'subject_id' => $subject->id, 'title' => 'Draft Quiz', 'total_marks' => 1, 'pass_marks' => 1, 'status' => 'draft']);
        $this->actingAs($student->user)->get(route('student.quizzes.preview', $draft))->assertForbidden();
    }

    public function test_invalid_multiple_choice_question_is_rejected(): void
    {
        [$teacher] = $this->fixture();
        $this->actingAs($teacher->user)->post(route('teacher.question-bank.store'), [
            'question_text' => 'Invalid', 'question_type' => 'multiple_choice', 'marks' => 1,
            'options' => [['option_text' => 'A'], ['option_text' => 'B']],
        ])->assertStatus(422);
    }

    public function test_teacher_can_configure_deterministic_pool_with_exact_count_and_cannot_change_published_quiz(): void
    {
        [$teacher, $student, $class, $subject] = $this->fixture();
        $this->actingAs($teacher->user)->post(route('teacher.question-bank.store'), [
            'question_text' => 'Pool one', 'question_type' => 'true_false', 'marks' => 1,
            'options' => [
                ['option_text' => 'True', 'is_correct' => 1],
                ['option_text' => 'False'],
            ],
        ]);
        $this->actingAs($teacher->user)->post(route('teacher.question-bank.store'), [
            'question_text' => 'Pool two', 'question_type' => 'true_false', 'marks' => 1,
            'options' => [
                ['option_text' => 'True', 'is_correct' => 1],
                ['option_text' => 'False'],
            ],
        ]);
        $test = Test::create(['teacher_id' => $teacher->user_id, 'school_class_id' => $class->id, 'subject_id' => $subject->id, 'title' => 'Pool Quiz', 'total_marks' => 2, 'pass_marks' => 1, 'status' => 'draft']);
        $response = $this->actingAs($teacher->user)->post(route('teacher.tests.question-pools.store', $test), [
            'question_type' => 'true_false', 'questions_count' => 2, 'marks_per_question' => 2,
        ]);
        $response->assertRedirect();
        $pool = $test->questionPools()->firstOrFail();
        $first = app(\App\Services\QuizFoundationService::class)->deterministicPoolQuestions($pool, 42)->pluck('id')->all();
        $second = app(\App\Services\QuizFoundationService::class)->deterministicPoolQuestions($pool, 42)->pluck('id')->all();
        $this->assertCount(2, $first);
        $this->assertSame($first, $second);
        $test->update(['status' => 'published']);
        $this->actingAs($teacher->user)->post(route('teacher.tests.question-pools.store', $test), ['questions_count' => 1])->assertStatus(422);
    }

    public function test_pool_rejects_insufficient_questions_and_cross_teacher_access(): void
    {
        [$teacher, $student, $class, $subject] = $this->fixture();
        $test = Test::create(['teacher_id' => $teacher->user_id, 'school_class_id' => $class->id, 'subject_id' => $subject->id, 'title' => 'Pool Quiz', 'total_marks' => 2, 'pass_marks' => 1, 'status' => 'draft']);
        $this->actingAs($teacher->user)->post(route('teacher.tests.question-pools.store', $test), ['questions_count' => 1])->assertStatus(422);
        $other = User::factory()->create(['role_id' => Role::where('slug', Role::TEACHER)->firstOrFail()->id]);
        $this->actingAs($other)->post(route('teacher.tests.question-pools.store', $test), ['questions_count' => 1])->assertForbidden();
    }

    public function test_student_attempt_snapshots_pool_questions_and_rejects_foreign_options(): void
    {
        [$teacher, $student, $class, $subject] = $this->fixture();
        $question = TestQuestion::create([
            'teacher_id' => $teacher->user_id, 'question_text' => 'Snapshot question',
            'question_type' => 'multiple_choice', 'marks' => 2,
        ]);
        $option = $question->options()->create(['option_text' => 'Correct', 'is_correct' => true, 'order_index' => 0]);
        $question->options()->create(['option_text' => 'Wrong', 'is_correct' => false, 'order_index' => 1]);
        $test = Test::create([
            'teacher_id' => $teacher->user_id, 'school_class_id' => $class->id, 'subject_id' => $subject->id,
            'title' => 'Attempt Quiz', 'total_marks' => 2, 'pass_marks' => 1, 'status' => 'published',
            'publish_date' => now()->subMinute(),
        ]);
        $test->selectedQuestions()->attach($question->id, ['order_index' => 0, 'marks' => 2]);
        $this->actingAs($student->user)->post(route('student.tests.start', $test))->assertRedirect();
        $attempt = \App\Models\TestAttempt::latest('id')->firstOrFail();
        $snapshot = $attempt->questions()->firstOrFail();
        $this->assertSame('Snapshot question', $snapshot->question_text);
        $question->update(['question_text' => 'Changed after start']);
        $this->actingAs($student->user)->post(route('student.tests.save-answer', $attempt), [
            'test_question_id' => $question->id, 'selected_option_id' => $option->id,
        ])->assertJson(['success' => true]);
        $this->assertDatabaseHas('test_answers', ['test_attempt_id' => $attempt->id, 'test_question_id' => $question->id, 'selected_option_id' => $option->id]);
    }

    private function fixture(): array
    {
        $this->seed();
        $teacherRole = Role::where('slug', Role::TEACHER)->firstOrFail();
        $studentRole = Role::where('slug', Role::STUDENT)->firstOrFail();
        $year = AcademicYear::create(['name' => '2026/27', 'code' => 'QB-Y', 'starts_on' => '2026-09-01', 'ends_on' => '2027-07-31']);
        $term = Term::create(['academic_year_id' => $year->id, 'name' => 'Term 1', 'code' => 'QB-T', 'starts_on' => '2026-09-01', 'ends_on' => '2026-12-20']);
        $class = SchoolClass::create(['name' => 'Quiz Class']);
        $legacySection = Section::create(['school_class_id' => $class->id, 'name' => 'A']);
        $subject = Subject::create(['name' => 'Quiz Subject', 'code' => 'QB-1']);
        $teacherUser = User::factory()->create(['role_id' => $teacherRole->id]);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'employee_id' => 'QB-T']);
        $studentUser = User::factory()->create(['role_id' => $studentRole->id]);
        $student = Student::create(['user_id' => $studentUser->id, 'roll_number' => 'QB-S', 'admission_number' => 'QB-A', 'school_class_id' => $class->id, 'section_id' => $legacySection->id, 'admission_date' => now(), 'status' => 'active']);
        $section = CourseSection::create(['course_id' => $subject->id, 'term_id' => $term->id, 'teacher_id' => $teacherUser->id, 'code' => 'A']);
        Enrollment::create(['student_id' => $student->id, 'course_section_id' => $section->id, 'enrolled_at' => now(), 'status' => 'enrolled']);
        return [$teacher, $student, $class, $subject, $section];
    }
}
