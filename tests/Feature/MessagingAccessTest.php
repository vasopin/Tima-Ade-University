<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Conversation;
use App\Models\Message;
use \Tests\Concerns\ForceRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MessagingAccessTest extends TestCase
{
    use \Tests\Concerns\ForceRefreshDatabase;

    public function test_unauthenticated_users_cannot_access_messages(): void
    {
        $this->get('/messages')->assertRedirect('/login');
        $this->getJson('/api/messages/conversations')->assertUnauthorized();
    }

    public function test_staff_can_message_students_but_not_teachers(): void
    {
        $staff = $this->user('staff');
        $student = $this->user('student');
        $teacher = $this->user('teacher');

        $this->actingAs($staff)->postJson('/api/messages/conversations', ['recipient_id' => $student->id])->assertCreated();
        $this->actingAs($staff)->postJson('/api/messages/conversations', ['recipient_id' => $teacher->id])->assertForbidden();
    }

    public function test_teacher_can_message_a_student_in_an_assigned_class_only(): void
    {
        $teacher = $this->user('teacher');
        $authorizedStudent = $this->studentUser('authorized-student');
        $unrelatedStudent = $this->studentUser('unrelated-student');
        $schoolClass = DB::table('school_classes')->insertGetId(['name' => 'Year 10', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        $section = DB::table('sections')->insertGetId(['school_class_id' => $schoolClass, 'name' => 'A', 'capacity' => 40, 'created_at' => now(), 'updated_at' => now()]);
        $authorizedStudent->student->update(['school_class_id' => $schoolClass, 'section_id' => $section]);
        DB::table('class_subject')->insert(['school_class_id' => $schoolClass, 'subject_id' => DB::table('subjects')->insertGetId(['name' => 'Math', 'code' => 'MATH-' . uniqid(), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]), 'teacher_id' => $teacher->id, 'created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($teacher)->postJson('/api/messages/conversations', ['recipient_id' => $authorizedStudent->id])->assertCreated();
        $this->actingAs($teacher)->postJson('/api/messages/conversations', ['recipient_id' => $unrelatedStudent->id])->assertForbidden();
    }

    public function test_students_must_accept_friend_request_before_private_chat(): void
    {
        $first = $this->studentUser('first-student');
        $second = $this->studentUser('second-student');

        $request = $this->actingAs($first)->postJson('/api/messages/friend-requests', ['recipient_id' => $second->id])->assertCreated()->json('request');
        $this->actingAs($first)->postJson('/api/messages/conversations', ['recipient_id' => $second->id])->assertForbidden();
        $this->actingAs($second)->patchJson('/api/messages/friend-requests/' . $request['id'] . '/accepted')->assertOk();
        $this->actingAs($first)->postJson('/api/messages/conversations', ['recipient_id' => $second->id])->assertCreated();
    }

    public function test_friend_requests_reject_self_and_duplicate_pending_requests(): void
    {
        $student = $this->studentUser('self-student');
        $other = $this->studentUser('other-student');

        $this->actingAs($student)->postJson('/api/messages/friend-requests', ['recipient_id' => $student->id])->assertUnprocessable();
        $this->actingAs($student)->postJson('/api/messages/friend-requests', ['recipient_id' => $other->id])->assertCreated();
        $this->actingAs($student)->postJson('/api/messages/friend-requests', ['recipient_id' => $other->id])->assertUnprocessable();
    }

    public function test_only_conversation_participants_can_read_or_send_messages(): void
    {
        $first = $this->user('staff');
        $second = $this->user('student');
        $outsider = $this->user('teacher');
        $conversation = $this->actingAs($first)->postJson('/api/messages/conversations', ['recipient_id' => $second->id])->assertCreated()->json('conversation');
        $conversationId = $conversation['id'];

        $this->actingAs($first)->postJson('/api/messages/conversations/' . $conversationId . '/messages', ['body' => 'Private note'])->assertCreated();
        $this->actingAs($second)->getJson('/api/messages/conversations/' . $conversationId . '/messages')->assertOk()->assertJsonPath('messages.0.body', 'Private note');
        $this->actingAs($outsider)->getJson('/api/messages/conversations/' . $conversationId . '/messages')->assertForbidden();
        $this->actingAs($outsider)->postJson('/api/messages/conversations/' . $conversationId . '/messages', ['body' => 'Intrusion'])->assertForbidden();
        $this->assertDatabaseHas('messages', ['conversation_id' => $conversationId, 'sender_id' => $first->id, 'body' => 'Private note']);
    }

    public function test_message_can_be_deleted_for_self(): void
    {
        $staff = $this->user('staff');
        $student = $this->user('student');
        $conversation = $this->actingAs($staff)->postJson('/api/messages/conversations', ['recipient_id' => $student->id])->json('conversation');
        $conversationId = $conversation['id'];
        
        $message = $this->actingAs($staff)->postJson('/api/messages/conversations/' . $conversationId . '/messages', ['body' => 'Delete me'])->json('message');
        $messageId = $message['id'];
        
        // Sender deletes for self
        $this->actingAs($staff)->deleteJson('/api/messages/' . $messageId, ['scope' => 'me'])->assertOk();
        
        // Message is gone for sender but visible to recipient
        $senderMessages = $this->actingAs($staff)->getJson('/api/messages/conversations/' . $conversationId . '/messages')->json('messages');
        $recipientMessages = $this->actingAs($student)->getJson('/api/messages/conversations/' . $conversationId . '/messages')->json('messages');
        
        $this->assertEmpty(array_filter($senderMessages, fn ($m) => $m['id'] === $messageId));
        $this->assertNotEmpty(array_filter($recipientMessages, fn ($m) => $m['id'] === $messageId));
    }

    public function test_message_can_be_deleted_for_everyone_within_24_hours(): void
    {
        $staff = $this->user('staff');
        $student = $this->user('student');
        $conversation = $this->actingAs($staff)->postJson('/api/messages/conversations', ['recipient_id' => $student->id])->json('conversation');
        $conversationId = $conversation['id'];
        
        $message = $this->actingAs($staff)->postJson('/api/messages/conversations/' . $conversationId . '/messages', ['body' => 'Delete for all'])->json('message');
        $messageId = $message['id'];
        
        // Sender can delete for everyone within 24 hours
        $this->actingAs($staff)->deleteJson('/api/messages/' . $messageId, ['scope' => 'everyone'])->assertOk();
        
        // Message is gone for both
        $senderMessages = $this->actingAs($staff)->getJson('/api/messages/conversations/' . $conversationId . '/messages')->json('messages');
        $recipientMessages = $this->actingAs($student)->getJson('/api/messages/conversations/' . $conversationId . '/messages')->json('messages');
        
        $this->assertEmpty(array_filter($senderMessages, fn ($m) => $m['id'] === $messageId));
        $this->assertEmpty(array_filter($recipientMessages, fn ($m) => $m['id'] === $messageId));
    }

    public function test_non_sender_cannot_delete_message_for_everyone(): void
    {
        $staff = $this->user('staff');
        $student = $this->user('student');
        $conversation = $this->actingAs($staff)->postJson('/api/messages/conversations', ['recipient_id' => $student->id])->json('conversation');
        $conversationId = $conversation['id'];
        
        $message = $this->actingAs($staff)->postJson('/api/messages/conversations/' . $conversationId . '/messages', ['body' => 'I cannot delete this'])->json('message');
        $messageId = $message['id'];
        
        // Recipient cannot delete for everyone
        $this->actingAs($student)->deleteJson('/api/messages/' . $messageId, ['scope' => 'everyone'])->assertForbidden();
        
        // Message is still there
        $messages = $this->actingAs($student)->getJson('/api/messages/conversations/' . $conversationId . '/messages')->json('messages');
        $this->assertNotEmpty(array_filter($messages, fn ($m) => $m['id'] === $messageId));
    }

    public function test_deleted_messages_removed_from_unread_count(): void
    {
        $staff = $this->user('staff');
        $student = $this->user('student');
        $conversation = $this->actingAs($staff)->postJson('/api/messages/conversations', ['recipient_id' => $student->id])->json('conversation');
        $conversationId = $conversation['id'];
        
        $this->actingAs($staff)->postJson('/api/messages/conversations/' . $conversationId . '/messages', ['body' => 'Message 1'])->json('message');
        $message2 = $this->actingAs($staff)->postJson('/api/messages/conversations/' . $conversationId . '/messages', ['body' => 'Message 2'])->json('message');
        
        $student->fresh();
        $conversations = $this->actingAs($student)->getJson('/api/messages/conversations')->json('conversations');
        $conv = array_values(array_filter($conversations, fn ($c) => $c['id'] === $conversationId))[0] ?? null;
        $this->assertNotNull($conv);
        $this->assertEquals(2, $conv['unread_count']);
        
        // Delete one message for everyone
        $this->actingAs($staff)->deleteJson('/api/messages/' . $message2['id'], ['scope' => 'everyone'])->assertOk();
        
        $conversations = $this->actingAs($student)->getJson('/api/messages/conversations')->json('conversations');
        $conv = array_values(array_filter($conversations, fn ($c) => $c['id'] === $conversationId))[0] ?? null;
        $this->assertEquals(1, $conv['unread_count']);
    }

    public function test_recipient_can_delete_message_for_self(): void
    {
        $staff = $this->user('staff');
        $student = $this->user('student');
        $conversation = $this->actingAs($staff)->postJson('/api/messages/conversations', ['recipient_id' => $student->id])->json('conversation');
        $conversationId = $conversation['id'];
        
        $message = $this->actingAs($staff)->postJson('/api/messages/conversations/' . $conversationId . '/messages', ['body' => 'From staff'])->json('message');
        $messageId = $message['id'];
        
        // Student deletes for self
        $this->actingAs($student)->deleteJson('/api/messages/' . $messageId, ['scope' => 'me'])->assertOk();
        
        // Message is gone for student but still there for staff
        $studentMessages = $this->actingAs($student)->getJson('/api/messages/conversations/' . $conversationId . '/messages')->json('messages');
        $staffMessages = $this->actingAs($staff)->getJson('/api/messages/conversations/' . $conversationId . '/messages')->json('messages');
        
        $this->assertEmpty(array_filter($studentMessages, fn ($m) => $m['id'] === $messageId));
        $this->assertNotEmpty(array_filter($staffMessages, fn ($m) => $m['id'] === $messageId));
    }

    private function user(string $roleSlug): User
    {
        $role = Role::firstOrCreate(['slug' => $roleSlug], ['name' => ucfirst($roleSlug)]);
        return User::factory()->create(['role_id' => $role->id, 'is_active' => true]);
    }

    private function studentUser(string $name): User
    {
        $user = $this->user('student');
        $schoolClass = DB::table('school_classes')->insertGetId(['name' => $name . ' class', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        $section = DB::table('sections')->insertGetId(['school_class_id' => $schoolClass, 'name' => 'A', 'capacity' => 40, 'created_at' => now(), 'updated_at' => now()]);
        Student::create(['user_id' => $user->id, 'roll_number' => $name . '-roll', 'admission_number' => $name . '-admission', 'school_class_id' => $schoolClass, 'section_id' => $section, 'admission_date' => now()->toDateString(), 'status' => 'active']);
        return $user->fresh('student');
    }
}
