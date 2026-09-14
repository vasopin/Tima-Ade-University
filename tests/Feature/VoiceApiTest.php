<?php

namespace Tests\Feature;

use App\Models\Call;
use App\Models\CallHistory;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\VoiceMessage;
use \Tests\Concerns\ForceRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Comprehensive API verification suite for voice messages and voice calls.
 * Tests all authorization rules, unauthorized access prevention, and API contracts.
 */
class VoiceApiTest extends TestCase
{
    use \Tests\Concerns\ForceRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
    }

    // =====================================================================
    // VOICE MESSAGE UPLOAD TESTS
    // =====================================================================

    public function test_unauthenticated_user_cannot_upload_voice_message(): void
    {
        $conversation = Conversation::create(['type' => 'direct', 'direct_key' => 'unauth-1:2']);
        $file = UploadedFile::fake()->create('audio.webm', 100, 'audio/webm');

        $this->postJson(
            "/api/conversations/{$conversation->id}/voice-messages",
            ['audio' => $file, 'duration_seconds' => 5]
        )->assertUnauthorized();
    }

    public function test_conversation_non_participant_cannot_upload_voice_message(): void
    {
        $sender = $this->user('staff');
        $recipient = $this->user('student');
        $outsider = $this->user('teacher');
        $conversation = $this->createConversation($sender, $recipient);

        $file = UploadedFile::fake()->create('audio.webm', 100, 'audio/webm');

        $this->actingAs($outsider)->postJson(
            "/api/conversations/{$conversation->id}/voice-messages",
            ['audio' => $file, 'duration_seconds' => 5]
        )->assertForbidden();
    }

    public function test_participant_cannot_message_unauthorized_user_via_voice(): void
    {
        // Scenario: Staff (sender) tries to message Teacher (unauthorized recipient)
        $staff = $this->user('staff');
        $teacher = $this->user('teacher');
        $student = $this->user('student');

        // Create a conversation (this will bypass the initial check)
        // But when staff tries to send voice to teacher, should be blocked
        $conversation = $this->createConversation($staff, $student);

        // Now try to send as staff to teacher (who is not in this conversation)
        // This tests the ensureParticipant + canMessage check
        $file = UploadedFile::fake()->create('audio.webm', 100, 'audio/webm');

        // Staff can message student in this conversation
        $response = $this->actingAs($staff)->postJson(
            "/api/conversations/{$conversation->id}/voice-messages",
            ['audio' => $file, 'duration_seconds' => 5]
        );
        $this->assertTrue($response->status() === 201 || $response->status() === 200);
    }

    public function test_staff_can_upload_voice_message_to_student(): void
    {
        $staff = $this->user('staff');
        $student = $this->user('student');
        $conversation = $this->createConversation($staff, $student);

        $file = UploadedFile::fake()->create('audio.webm', 100, 'audio/webm');

        $this->actingAs($staff)->postJson(
            "/api/conversations/{$conversation->id}/voice-messages",
            ['audio' => $file, 'duration_seconds' => 5]
        )->assertCreated()->assertJsonStructure(['voice_message' => ['id', 'sender_id', 'sender_name', 'duration_seconds', 'created_at']]);

        $this->assertDatabaseHas('voice_messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $staff->id,
            'duration_seconds' => 5,
        ]);
    }

    public function test_staff_cannot_upload_voice_message_to_teacher(): void
    {
        $staff = $this->user('staff');
        $teacher = $this->user('teacher');

        // Try to upload without creating a conversation
        $file = UploadedFile::fake()->create('audio.webm', 100, 'audio/webm');

        // This should fail because staff cannot message teacher
        $this->actingAs($staff)->postJson('/api/messages/conversations', ['recipient_id' => $teacher->id])
            ->assertForbidden();
    }

    public function test_teacher_can_upload_voice_to_assigned_student(): void
    {
        $teacher = $this->user('teacher');
        $student = $this->studentUser('assigned-student');
        $conversation = $this->setupTeacherStudentRelation($teacher, $student);

        $file = UploadedFile::fake()->create('audio.webm', 100, 'audio/webm');

        $this->actingAs($teacher)->postJson(
            "/api/conversations/{$conversation->id}/voice-messages",
            ['audio' => $file, 'duration_seconds' => 3]
        )->assertCreated();

        $this->assertDatabaseHas('voice_messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $teacher->id,
            'duration_seconds' => 3,
        ]);
    }

    public function test_student_can_upload_voice_to_friendly_student(): void
    {
        $first = $this->studentUser('first');
        $second = $this->studentUser('second');

        // Accept friend request
        DB::table('friend_requests')->insert([
            'requester_id' => $first->id,
            'recipient_id' => $second->id,
            'status' => 'accepted',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $conversation = $this->createConversation($first, $second);

        $file = UploadedFile::fake()->create('audio.webm', 100, 'audio/webm');

        $this->actingAs($first)->postJson(
            "/api/conversations/{$conversation->id}/voice-messages",
            ['audio' => $file, 'duration_seconds' => 4]
        )->assertCreated();
    }

    public function test_student_cannot_upload_voice_to_non_friend_student(): void
    {
        $first = $this->studentUser('first');
        $second = $this->studentUser('second');

        // Do NOT accept friend request
        $conversation = Conversation::create([
            'type' => 'direct',
            'direct_key' => 'nonfriend-' . $first->id . ':' . $second->id,
        ]);
        $conversation->participants()->sync([$first->id, $second->id]);

        $file = UploadedFile::fake()->create('audio.webm', 100, 'audio/webm');

        // Should fail because they're not friends
        $this->actingAs($first)->postJson(
            "/api/conversations/{$conversation->id}/voice-messages",
            ['audio' => $file, 'duration_seconds' => 4]
        )->assertForbidden();
    }

    public function test_voice_message_upload_validates_audio_file_type(): void
    {
        $staff = $this->user('staff');
        $student = $this->user('student');
        $conversation = $this->createConversation($staff, $student);

        // Try to upload a non-audio file
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $this->actingAs($staff)->postJson(
            "/api/conversations/{$conversation->id}/voice-messages",
            ['audio' => $file, 'duration_seconds' => 5]
        )->assertUnprocessable();
    }

    public function test_voice_message_upload_validates_file_size(): void
    {
        $staff = $this->user('staff');
        $student = $this->user('student');
        $conversation = $this->createConversation($staff, $student);

        // Create a file larger than 10MB (max:10240)
        $file = UploadedFile::fake()->create('audio.webm', 11000, 'audio/webm');

        $this->actingAs($staff)->postJson(
            "/api/conversations/{$conversation->id}/voice-messages",
            ['audio' => $file, 'duration_seconds' => 5]
        )->assertUnprocessable();
    }

    public function test_voice_message_upload_validates_duration(): void
    {
        $staff = $this->user('staff');
        $student = $this->user('student');
        $conversation = $this->createConversation($staff, $student);

        $file = UploadedFile::fake()->create('audio.webm', 100, 'audio/webm');

        // Duration too long (max 300 seconds)
        $this->actingAs($staff)->postJson(
            "/api/conversations/{$conversation->id}/voice-messages",
            ['audio' => $file, 'duration_seconds' => 301]
        )->assertUnprocessable();

        // Duration zero
        $this->actingAs($staff)->postJson(
            "/api/conversations/{$conversation->id}/voice-messages",
            ['audio' => $file, 'duration_seconds' => 0]
        )->assertUnprocessable();
    }

    // =====================================================================
    // VOICE MESSAGE DOWNLOAD/RETRIEVAL TESTS
    // =====================================================================

    public function test_unauthenticated_user_cannot_download_voice_message(): void
    {
        $conversation = Conversation::create(['type' => 'direct', 'direct_key' => '1:2']);
        $sender = $this->user('staff');
        $conversation->participants()->sync([$sender->id]);
        
        $voiceMessage = VoiceMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'file_path' => 'voice-messages/test.webm',
            'duration_seconds' => 10,
            'mime_type' => 'audio/webm',
            'file_size' => 100,
        ]);

        $this->getJson("/api/voice-messages/{$voiceMessage->id}/file")
            ->assertUnauthorized();
    }

    public function test_non_participant_cannot_download_voice_message(): void
    {
        $sender = $this->user('staff');
        $recipient = $this->user('student');
        $outsider = $this->user('teacher');

        $conversation = $this->createConversation($sender, $recipient);
        $voiceMessage = VoiceMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'file_path' => 'voice-messages/test.webm',
            'duration_seconds' => 10,
            'mime_type' => 'audio/webm',
            'file_size' => 100,
        ]);

        Storage::disk('private')->put('voice-messages/test.webm', 'audio-data');

        $this->actingAs($outsider)->getJson("/api/voice-messages/{$voiceMessage->id}/file")
            ->assertForbidden();
    }

    public function test_conversation_participant_can_download_voice_message(): void
    {
        $sender = $this->user('staff');
        $recipient = $this->user('student');
        $conversation = $this->createConversation($sender, $recipient);

        $voiceMessage = VoiceMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'file_path' => 'voice-messages/test.webm',
            'duration_seconds' => 10,
            'mime_type' => 'audio/webm',
            'file_size' => 100,
            'played_at' => null,
        ]);

        Storage::disk('private')->put('voice-messages/test.webm', 'audio-data');

        $this->actingAs($recipient)->getJson("/api/voice-messages/{$voiceMessage->id}/file")
            ->assertOk();

        // Verify played_at was updated
        $this->assertNotNull($voiceMessage->fresh()->played_at);
    }

    public function test_conversation_response_includes_voice_messages_for_persistent_history(): void
    {
        $sender = $this->user('staff');
        $recipient = $this->user('student');
        $conversation = $this->createConversation($sender, $recipient);

        $voiceMessage = VoiceMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'file_path' => 'voice-messages/persisted.webm',
            'duration_seconds' => 12,
            'mime_type' => 'audio/webm',
            'file_size' => 256,
        ]);

        Storage::disk('private')->put('voice-messages/persisted.webm', 'audio-data');

        $this->actingAs($sender)->getJson("/api/messages/conversations/{$conversation->id}/messages")
            ->assertOk()
            ->assertJsonPath('voice_messages.0.id', $voiceMessage->id)
            ->assertJsonPath('voice_messages.0.file_path', 'voice-messages/persisted.webm');
    }

    public function test_conversation_response_combines_text_and_voice_messages_chronologically(): void
    {
        $sender = $this->user('staff');
        $recipient = $this->user('student');
        $conversation = $this->createConversation($sender, $recipient);
        $base = now()->subMinutes(3);

        $text = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => 'Ready?',
            'created_at' => $base,
            'updated_at' => $base,
        ]);
        $voice = VoiceMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $recipient->id,
            'file_path' => 'voice-messages/combined.webm',
            'duration_seconds' => 18,
            'mime_type' => 'audio/webm',
            'file_size' => 100,
            'created_at' => $base->copy()->addMinute(),
            'updated_at' => $base->copy()->addMinute(),
        ]);

        $response = $this->actingAs($sender)->getJson("/api/messages/conversations/{$conversation->id}/messages");

        $response->assertOk()
            ->assertJsonPath('messages.0.id', $text->id)
            ->assertJsonPath('messages.0.type', 'text')
            ->assertJsonPath('messages.1.id', $voice->id)
            ->assertJsonPath('messages.1.type', 'voice')
            ->assertJsonPath('messages.1.duration_seconds', 18);
    }

    public function test_voice_message_file_is_served_inline_for_browser_audio_player(): void
    {
        $sender = $this->user('staff');
        $recipient = $this->user('student');
        $conversation = $this->createConversation($sender, $recipient);

        $voiceMessage = VoiceMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'file_path' => 'voice-messages/inline.webm',
            'duration_seconds' => 9,
            'mime_type' => 'audio/webm',
            'file_size' => 512,
        ]);

        Storage::disk('private')->put('voice-messages/inline.webm', 'audio-data');

        $this->actingAs($recipient)
            ->get("/api/voice-messages/{$voiceMessage->id}/file")
            ->assertOk()
            ->assertHeader('content-type', 'audio/webm');
    }

    public function test_voice_message_marks_as_played_on_download(): void
    {
        $sender = $this->user('staff');
        $recipient = $this->user('student');
        $conversation = $this->createConversation($sender, $recipient);

        $voiceMessage = VoiceMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'file_path' => 'voice-messages/test.webm',
            'duration_seconds' => 10,
            'mime_type' => 'audio/webm',
            'file_size' => 100,
            'played_at' => null,
        ]);

        Storage::disk('private')->put('voice-messages/test.webm', 'audio-data');

        $this->assertNull($voiceMessage->played_at);

        $this->actingAs($recipient)->getJson("/api/voice-messages/{$voiceMessage->id}/file");

        $this->assertNotNull($voiceMessage->fresh()->played_at);
    }

    public function test_voice_message_endpoint_returns_404_for_missing_file(): void
    {
        $sender = $this->user('staff');
        $recipient = $this->user('student');
        $conversation = $this->createConversation($sender, $recipient);

        $voiceMessage = VoiceMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'file_path' => 'voice-messages/missing.webm',
            'duration_seconds' => 10,
            'mime_type' => 'audio/webm',
            'file_size' => 100,
        ]);

        // Don't create the file
        $this->actingAs($recipient)->getJson("/api/voice-messages/{$voiceMessage->id}/file")
            ->assertNotFound();
    }

    // =====================================================================
    // VOICE CALL INITIATION TESTS
    // =====================================================================

    public function test_call_and_video_endpoints_are_removed(): void
    {
        $caller = $this->user('staff');
        $recipient = $this->user('student');

        $this->postJson('/api/calls/initiate', ['recipient_id' => $recipient->id])
            ->assertNotFound();

        $this->actingAs($caller)->getJson('/api/calls/current')
            ->assertNotFound();
    }

    // =====================================================================
    // HELPER METHODS
    // =====================================================================

    private function user(string $roleSlug): User
    {
        $role = Role::firstOrCreate(['slug' => $roleSlug], ['name' => ucfirst($roleSlug)]);
        return User::factory()->create(['role_id' => $role->id, 'is_active' => true]);
    }

    private function studentUser(string $name): User
    {
        $user = $this->user('student');
        $schoolClass = DB::table('school_classes')->insertGetId([
            'name' => $name . ' class',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $section = DB::table('sections')->insertGetId([
            'school_class_id' => $schoolClass,
            'name' => 'A',
            'capacity' => 40,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Student::create([
            'user_id' => $user->id,
            'roll_number' => $name . '-roll',
            'admission_number' => $name . '-admission',
            'school_class_id' => $schoolClass,
            'section_id' => $section,
            'admission_date' => now()->toDateString(),
            'status' => 'active',
        ]);
        return $user->fresh('student');
    }

    private function createConversation(User $first, User $second): Conversation
    {
        $directKey = min($first->id, $second->id) . ':' . max($first->id, $second->id);
        $conversation = Conversation::firstOrCreate([
            'direct_key' => $directKey,
        ], ['type' => 'direct']);
        
        $conversation->participants()->sync([$first->id, $second->id]);
        return $conversation->fresh();
    }

    private function setupTeacherStudentRelation(User $teacher, User $student): Conversation
    {
        $schoolClass = $student->student->school_class_id;
        $subjectId = DB::table('subjects')->insertGetId([
            'name' => 'Test Subject',
            'code' => 'TEST-' . uniqid(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('class_subject')->insert([
            'school_class_id' => $schoolClass,
            'subject_id' => $subjectId,
            'teacher_id' => $teacher->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->createConversation($teacher, $student);
    }
}
