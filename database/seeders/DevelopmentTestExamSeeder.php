<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Test;
use App\Models\TestAnswer;
use App\Models\TestAttempt;
use App\Models\TestOption;
use App\Models\TestQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Seeds a local-only Test/Exam scenario without creating institutional accounts
 * or changing the normal application seed set.
 *
 * Run explicitly with:
 * php artisan db:seed --class=DevelopmentTestExamSeeder
 */
class DevelopmentTestExamSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment() !== 'local') {
            throw new RuntimeException('DevelopmentTestExamSeeder may only run when APP_ENV=local.');
        }

        DB::transaction(function (): void {
            $teacherUser = User::where('email', 'teacher1@school.com')->first();
            $studentUser = User::where('email', 'student1@school.com')->first();

            if (!$teacherUser || !$studentUser) {
                throw new RuntimeException('The existing local teacher and student accounts are required.');
            }

            $teacher = Teacher::where('user_id', $teacherUser->id)->first();
            $student = Student::where('user_id', $studentUser->id)->first();

            if (!$teacher || !$student) {
                throw new RuntimeException('Existing teacher and student profiles are required.');
            }

            $assignment = DB::table('class_subject')
                ->where('teacher_id', $teacherUser->id)
                ->where('school_class_id', $student->school_class_id)
                ->first();

            if (!$assignment) {
                throw new RuntimeException('The teacher must already be authorized for the student class.');
            }

            $subject = Subject::find($assignment->subject_id);

            if (!$subject) {
                throw new RuntimeException('The authorized class subject must already exist.');
            }

            $browserTest = $this->firstOrCreateTest(
                $teacherUser->id,
                $student->school_class_id,
                $subject->id,
                'Development Browser Test'
            );
            $this->seedQuestions($browserTest);

            $gradingTest = $this->firstOrCreateTest(
                $teacherUser->id,
                $student->school_class_id,
                $subject->id,
                'Development Grading Test'
            );
            $questions = $this->seedQuestions($gradingTest);
            $attempt = TestAttempt::firstOrCreate(
                [
                    'test_id' => $gradingTest->id,
                    'student_id' => $student->id,
                    'attempt_number' => 1,
                ],
                [
                    'started_at' => now()->subMinutes(12),
                    'submitted_at' => now()->subMinutes(2),
                    'time_spent_minutes' => 10,
                    'status' => 'submitted',
                ]
            );

            $firstQuestion = $questions[0];
            $correctOption = $firstQuestion->options()->where('is_correct', true)->first();

            TestAnswer::updateOrCreate(
                [
                    'test_attempt_id' => $attempt->id,
                    'test_question_id' => $firstQuestion->id,
                ],
                [
                    'selected_option_id' => $correctOption?->id,
                    'is_correct' => true,
                    'marks_obtained' => $firstQuestion->marks,
                ]
            );

            TestAnswer::firstOrCreate(
                [
                    'test_attempt_id' => $attempt->id,
                    'test_question_id' => $questions[1]->id,
                ],
                [
                    'answer_text' => 'A development answer for teacher review.',
                    'is_correct' => null,
                    'marks_obtained' => null,
                ]
            );
        });
    }

    private function firstOrCreateTest(
        int $teacherId,
        int $schoolClassId,
        int $subjectId,
        string $title
    ): Test {
        return Test::firstOrCreate(
            [
                'teacher_id' => $teacherId,
                'school_class_id' => $schoolClassId,
                'subject_id' => $subjectId,
                'title' => $title,
            ],
            [
                'instructions' => 'Local development data for browser verification.',
                'total_marks' => 10,
                'pass_marks' => 5,
                'duration_minutes' => 30,
                'attempt_limit' => 1,
                'status' => 'published',
                'publish_date' => now()->subHour(),
                'results_release_date' => now()->addDay(),
            ]
        );
    }

    /**
     * @return array<int, TestQuestion>
     */
    private function seedQuestions(Test $test): array
    {
        $multipleChoice = TestQuestion::firstOrCreate(
            [
                'test_id' => $test->id,
                'order_index' => 1,
            ],
            [
                'question_text' => 'Which answer is correct for this development test?',
                'question_type' => 'multiple_choice',
                'marks' => 5,
            ]
        );

        TestOption::firstOrCreate(
            ['test_question_id' => $multipleChoice->id, 'option_text' => 'Correct development answer'],
            ['is_correct' => true, 'order_index' => 1]
        );
        TestOption::firstOrCreate(
            ['test_question_id' => $multipleChoice->id, 'option_text' => 'Distractor answer'],
            ['is_correct' => false, 'order_index' => 2]
        );

        $shortAnswer = TestQuestion::firstOrCreate(
            [
                'test_id' => $test->id,
                'order_index' => 2,
            ],
            [
                'question_text' => 'Describe how this local scenario is used.',
                'question_type' => 'short_answer',
                'marks' => 5,
                'correct_answer' => 'It is used for local browser verification.',
            ]
        );

        return [$multipleChoice, $shortAnswer];
    }
}
