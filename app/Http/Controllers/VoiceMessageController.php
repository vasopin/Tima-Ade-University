<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\VoiceMessage;
use App\Services\MessagingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VoiceMessageController extends Controller
{
    public function __construct(private MessagingService $messaging) {}

    public function store(Conversation $conversation, Request $request): JsonResponse
    {
        $this->ensureParticipant($conversation, $request->user());
        $user = $request->user();

        // Validate audio file - accept any file uploaded as audio from the browser
        // MediaRecorder output may vary by browser and OS
        $data = $request->validate([
            'audio' => [
                'required',
                'file',
                'mimetypes:audio/webm,audio/ogg,audio/mpeg,audio/mp4,audio/wav,video/webm',
                'max:10240', // 10MB max
            ],
            'duration_seconds' => ['required', 'integer', 'min:1', 'max:300'],
        ]);

        $recipient = $conversation->participants()->where('users.id', '!=', $user->id)->firstOrFail();

        abort_unless($this->messaging->canMessage($user, $recipient), 403, 'You are not authorized to message this user.');

        $voiceMessage = DB::transaction(function () use ($conversation, $user, $data, $recipient, $request) {
            $audioFile = $request->file('audio');
            $mimeType = $audioFile->getMimeType() ?: 'audio/webm';
            $originalExtension = strtolower(pathinfo($audioFile->getClientOriginalName(), PATHINFO_EXTENSION));
            $extensionMap = [
                'audio/wav' => 'wav',
                'audio/mpeg' => 'mp3',
                'audio/mp4' => 'm4a',
                'audio/ogg' => 'ogg',
                'audio/webm' => 'webm',
                'video/webm' => 'webm',
            ];
            $extension = $originalExtension ?: ($extensionMap[$mimeType] ?? 'webm');

            $filename = $user->id . '-' . $conversation->id . '-' . time() . '.' . $extension;
            $path = $audioFile->storeAs('voice-messages', $filename, 'private');

            $voiceMessage = $conversation->voiceMessages()->create([
                'sender_id' => $user->id,
                'file_path' => $path,
                'duration_seconds' => (int) $data['duration_seconds'],
                'mime_type' => $mimeType,
                'file_size' => $audioFile->getSize(),
            ]);

            // Update conversation timestamp
            $conversation->touch();

            // Notify recipient
            \App\Models\Notification::create([
                'user_id' => $recipient->id,
                'title' => 'New voice message',
                'body' => $user->name . ' sent you a voice message.',
                'role' => $recipient->role?->slug,
                'read' => false,
                'meta' => ['conversation_id' => $conversation->id, 'type' => 'voice_message'],
            ]);

            return $voiceMessage;
        });

        return response()->json(['voice_message' => [
            'id' => $voiceMessage->id,
            'sender_id' => $voiceMessage->sender_id,
            'sender_name' => $voiceMessage->sender->name,
            'duration_seconds' => $voiceMessage->duration_seconds,
            'created_at' => $voiceMessage->created_at->toIso8601String(),
        ]], 201);
    }

    public function file(VoiceMessage $voiceMessage, Request $request)
    {
        $conversation = $voiceMessage->conversation;
        $this->ensureParticipant($conversation, $request->user());

        if ($voiceMessage->played_at === null) {
            $voiceMessage->update(['played_at' => now()]);
        }

        if (!Storage::disk('private')->exists($voiceMessage->file_path)) {
            abort(404, 'Voice message file not found.');
        }

        $mimeType = $voiceMessage->mime_type ?: 'audio/webm';
        $path = Storage::disk('private')->path($voiceMessage->file_path);

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="voice-message.' . pathinfo($voiceMessage->file_path, PATHINFO_EXTENSION) . '"',
        ]);
    }

    public function markAsPlayed(VoiceMessage $voiceMessage, Request $request): JsonResponse
    {
        $conversation = $voiceMessage->conversation;
        $this->ensureParticipant($conversation, $request->user());

        $voiceMessage->update(['played_at' => now()]);

        return response()->json(['success' => true]);
    }

    private function ensureParticipant(Conversation $conversation, \App\Models\User $user): void
    {
        abort_unless($conversation->participants()->where('user_id', $user->id)->exists(), 403, 'You are not a participant in this conversation.');
    }
}
