<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MessagingService
{
    public function canMessage(User $sender, User $recipient): bool
    {
        if ($sender->id === $recipient->id || !$recipient->is_active) {
            return false;
        }

        if ($sender->isAdmin()) {
            return in_array($recipient->role?->slug, ['super_admin', 'admin', 'staff', 'teacher', 'student', 'parent'], true);
        }

        if ($sender->isStaff()) {
            return $recipient->isStudent();
        }

        if ($sender->isTeacher()) {
            return $recipient->isStudent()
                ? $this->teacherTeachesStudent($sender, $recipient)
                : $recipient->isParent() && $this->teacherLinkedToParent($sender, $recipient);
        }

        if ($sender->isStudent()) {
            return $recipient->isTeacher()
                ? $this->teacherTeachesStudent($recipient, $sender)
                : $recipient->isStudent() && $this->areFriends($sender, $recipient);
        }

        if ($sender->isParent()) {
            return $recipient->isStudent()
                ? $this->parentLinkedToStudent($sender, $recipient)
                : $recipient->isTeacher() && $this->teacherLinkedToParent($recipient, $sender);
        }

        return false;
    }

    public function teacherTeachesStudent(User $teacher, User $student): bool
    {
        $studentClassId = $student->student?->school_class_id;

        return $studentClassId !== null && DB::table('class_subject')
            ->where('teacher_id', $teacher->id)
            ->where('school_class_id', $studentClassId)
            ->exists();
    }

    public function parentLinkedToStudent(User $parent, User $student): bool
    {
        return $parent->guardian && $student->student && DB::table('parent_student')
            ->where('parent_id', $parent->guardian->id)
            ->where('student_id', $student->student->id)
            ->exists();
    }

    public function teacherLinkedToParent(User $teacher, User $parent): bool
    {
        if (!$parent->guardian) {
            return false;
        }

        $studentIds = DB::table('parent_student')
            ->where('parent_id', $parent->guardian->id)
            ->pluck('student_id');

        return $studentIds->isNotEmpty() && DB::table('class_subject')
            ->where('teacher_id', $teacher->id)
            ->whereIn('school_class_id', function ($query) use ($studentIds) {
                $query->from('students')->select('school_class_id')->whereIn('id', $studentIds);
            })
            ->exists();
    }

    public function areFriends(User $first, User $second): bool
    {
        return FriendRequest::where('status', 'accepted')
            ->where(function ($query) use ($first, $second) {
                $query->where('requester_id', $first->id)->where('recipient_id', $second->id);
            })
            ->orWhere(function ($query) use ($first, $second) {
                $query->where('requester_id', $second->id)->where('recipient_id', $first->id);
            })
            ->exists();
    }

    public function conversationFor(User $first, User $second): Conversation
    {
        $directKey = min($first->id, $second->id) . ':' . max($first->id, $second->id);

        return DB::transaction(function () use ($first, $second, $directKey) {
            $conversation = Conversation::firstOrCreate([
                'direct_key' => $directKey,
            ], ['type' => 'direct']);

            $conversation->participants()->syncWithoutDetaching([$first->id, $second->id]);

            return $conversation;
        });
    }
}
