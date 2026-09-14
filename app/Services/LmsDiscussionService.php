<?php

namespace App\Services;

use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\LmsDiscussion;
use App\Models\LmsDiscussionPost;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LmsDiscussionService
{
    public function canTeacherManage(LmsDiscussion $discussion, User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->isTeacher()
            && (int) $discussion->teacher?->user_id === (int) $user->id
            && $this->teacherOwnsScope($discussion->course_section_id, $discussion->school_class_id, $discussion->subject_id, $user);
    }

    public function canStudentAccess(LmsDiscussion $discussion, User $user): bool
    {
        $student = $user->student;
        if (!$user->isStudent() || !$student || !in_array($discussion->status, ['published', 'closed'], true)
            || ($discussion->available_from && now()->lt($discussion->available_from))
            || ($discussion->available_until && now()->gt($discussion->available_until))) {
            return false;
        }
        if ($discussion->course_section_id) {
            return Enrollment::where('student_id', $student->id)
                ->where('course_section_id', $discussion->course_section_id)
                ->whereIn('status', Enrollment::ACTIVE_STATUSES)->exists();
        }
        return (int) $student->school_class_id === (int) $discussion->school_class_id;
    }

    public function teacherOwnsScope(?int $sectionId, ?int $classId, ?int $subjectId, User $user): bool
    {
        if (!$user->isTeacher()) {
            return false;
        }
        if ($sectionId) {
            return CourseSection::whereKey($sectionId)->where('teacher_id', $user->id)->exists();
        }
        return DB::table('class_subject')
            ->where('teacher_id', $user->id)
            ->where('school_class_id', $classId)
            ->when($subjectId, fn ($query) => $query->where('subject_id', $subjectId))
            ->exists();
    }

    public function create(User $user, array $data): LmsDiscussion
    {
        abort_unless($user->isTeacher(), 403, 'Teacher access required.');
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
        abort_unless($this->teacherOwnsScope($data['course_section_id'] ?? null, $data['school_class_id'] ?? null, $data['subject_id'] ?? null, $user), 403);
        return LmsDiscussion::create([...$data, 'teacher_id' => $teacher->id]);
    }

    public function post(LmsDiscussion $discussion, User $user, string $body, ?int $parentId = null): LmsDiscussionPost
    {
        abort_unless($this->canStudentAccess($discussion, $user) || $this->canTeacherManage($discussion, $user), 403);
        abort_unless($discussion->isOpen(), 422, 'This discussion is closed.');
        if ($parentId) {
            abort_unless($discussion->posts()->whereKey($parentId)->exists(), 422, 'The reply target is invalid.');
        }
        return $discussion->posts()->create(['author_id' => $user->id, 'parent_id' => $parentId, 'body' => trim($body)]);
    }
}
