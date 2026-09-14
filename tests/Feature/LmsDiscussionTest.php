<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\LmsDiscussion;
use App\Models\LmsDiscussionPost;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Term;
use App\Models\User;
use Tests\Concerns\ForceRefreshDatabase;
use Tests\TestCase;

class LmsDiscussionTest extends TestCase
{
    use ForceRefreshDatabase;

    public function test_owned_teacher_can_publish_discussion_and_enrolled_student_can_post(): void
    {
        [$teacher, $otherTeacher, $student, $otherStudent, $courseSection] = $this->fixture();
        $this->actingAs($teacher->user)->post(route('teacher.discussions.store'), [
            'title' => 'Welcome forum', 'description' => 'Discuss the first topic.', 'course_section_id' => $courseSection->id, 'status' => 'published',
        ])->assertRedirect();
        $discussion = LmsDiscussion::firstOrFail();
        $this->assertSame('published', $discussion->status);
        $this->actingAs($student->user)->get(route('student.discussions.show', $discussion))->assertOk()->assertSee('Welcome forum');
        $this->actingAs($student->user)->post(route('lms.discussions.posts.store', $discussion), ['body' => 'My contribution'])->assertRedirect();
        $this->assertDatabaseHas('lms_discussion_posts', ['discussion_id' => $discussion->id, 'author_id' => $student->user_id, 'body' => 'My contribution']);
        $post = LmsDiscussionPost::where('discussion_id', $discussion->id)->firstOrFail();
        $this->actingAs($student->user)->put(route('lms.discussion-posts.update', $post), ['body' => 'Edited contribution'])->assertRedirect();
        $this->assertDatabaseHas('lms_discussion_posts', ['id' => $post->id, 'body' => 'Edited contribution']);
        $this->actingAs($otherStudent->user)->put(route('lms.discussion-posts.update', $post), ['body' => 'Hijacked'])->assertForbidden();
        $this->actingAs($otherStudent->user)->get(route('student.discussions.show', $discussion))->assertForbidden();
        $this->actingAs($otherTeacher->user)->get(route('teacher.discussions.show', $discussion))->assertForbidden();
    }

    public function test_drafts_unavailable_discussions_and_closed_posts_are_protected(): void
    {
        [$teacher, $otherTeacher, $student, $otherStudent, $courseSection] = $this->fixture();
        $discussion = LmsDiscussion::create(['teacher_id' => $teacher->id, 'course_section_id' => $courseSection->id, 'title' => 'Draft topic', 'status' => 'draft']);
        $this->actingAs($student->user)->get(route('student.discussions.show', $discussion))->assertForbidden();
        $discussion->update(['status' => 'published', 'available_from' => now()->addHour()]);
        $this->actingAs($student->user)->get(route('student.discussions.show', $discussion))->assertForbidden();
        $discussion->update(['available_from' => now()->subMinute()]);
        $discussion->update(['status' => 'closed']);
        $this->actingAs($student->user)->post(route('lms.discussions.posts.store', $discussion), ['body' => 'Too late'])->assertStatus(422);
    }

    public function test_teacher_cannot_create_or_moderate_another_teachers_discussion(): void
    {
        [$teacher, $otherTeacher, $student, $otherStudent, $courseSection] = $this->fixture();
        $discussion = LmsDiscussion::create(['teacher_id' => $teacher->id, 'course_section_id' => $courseSection->id, 'title' => 'Owned topic', 'status' => 'published']);
        $post = LmsDiscussionPost::create(['discussion_id' => $discussion->id, 'author_id' => $student->user_id, 'body' => 'Post']);
        $this->actingAs($otherTeacher->user)->post(route('teacher.discussions.store'), [
            'title' => 'Invalid', 'course_section_id' => $courseSection->id, 'status' => 'published',
        ])->assertForbidden();
        $this->actingAs($otherTeacher->user)->post(route('lms.discussion-posts.moderate', $post), ['status' => 'hidden'])->assertForbidden();
    }

    private function fixture(): array
    {
        $this->seed();
        $teacherRole = Role::where('slug', Role::TEACHER)->firstOrFail();
        $studentRole = Role::where('slug', Role::STUDENT)->firstOrFail();
        $year = AcademicYear::create(['name' => '2026/27', 'code' => 'DS-26', 'starts_on' => '2026-09-01', 'ends_on' => '2027-07-31']);
        $term = Term::create(['academic_year_id' => $year->id, 'name' => 'Term 1', 'code' => 'DS-T1', 'starts_on' => '2026-09-01', 'ends_on' => '2026-12-20']);
        $class = SchoolClass::create(['name' => 'Discussion Class']);
        $section = Section::create(['school_class_id' => $class->id, 'name' => 'A']);
        $subject = Subject::create(['name' => 'Discussion Course', 'code' => 'DS-1']);
        $teacherUser = User::factory()->create(['role_id' => $teacherRole->id]);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'employee_id' => 'DS-T']);
        $otherTeacherUser = User::factory()->create(['role_id' => $teacherRole->id]);
        $otherTeacher = Teacher::create(['user_id' => $otherTeacherUser->id, 'employee_id' => 'DS-O']);
        $studentUser = User::factory()->create(['role_id' => $studentRole->id]);
        $student = Student::create(['user_id' => $studentUser->id, 'roll_number' => 'DS-S', 'admission_number' => 'DS-A', 'school_class_id' => $class->id, 'section_id' => $section->id, 'admission_date' => now(), 'status' => 'active']);
        $otherStudentUser = User::factory()->create(['role_id' => $studentRole->id]);
        $otherStudent = Student::create(['user_id' => $otherStudentUser->id, 'roll_number' => 'DS-OS', 'admission_number' => 'DS-OA', 'school_class_id' => $class->id, 'section_id' => $section->id, 'admission_date' => now(), 'status' => 'active']);
        $courseSection = CourseSection::create(['course_id' => $subject->id, 'term_id' => $term->id, 'teacher_id' => $teacherUser->id, 'code' => 'A']);
        Enrollment::create(['student_id' => $student->id, 'course_section_id' => $courseSection->id, 'enrolled_at' => now(), 'status' => 'enrolled']);
        return [$teacher, $otherTeacher, $student, $otherStudent, $courseSection];
    }
}
