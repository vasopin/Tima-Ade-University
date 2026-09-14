<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\CallHistory;
use App\Models\Conversation;
use App\Models\Notification;
use App\Models\User;
use App\Services\LiveKitClassroomProvider;
use App\Services\MessagingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CallController extends Controller
{
    public function __construct(private MessagingService $messaging) {}

    /**
     * Initiate a voice call
     */
    public function initiate(Request $request): JsonResponse
    {
        $data = $request->validate(['recipient_id' => ['required', 'integer', 'exists:users,id']]);
        $user = $request->user();
        $recipient = User::findOrFail($data['recipient_id']);

        abort_unless($this->messaging->canMessage($user, $recipient), 403, 'You are not authorized to call this user.');

        $call = DB::transaction(function () use ($user, $recipient) {
            // Get or create conversation
            $conversation = $this->messaging->conversationFor($user, $recipient);

            // Create call record
            $call = Call::create([
                'conversation_id' => $conversation->id,
                'caller_id' => $user->id,
                'recipient_id' => $recipient->id,
                'status' => 'ringing',
            ]);

            // Log the initiation
            CallHistory::create([
                'call_id' => $call->id,
                'user_id' => $user->id,
                'action' => 'initiated',
            ]);

            // Notify recipient
            Notification::create([
                'user_id' => $recipient->id,
                'title' => 'Incoming voice call',
                'body' => $user->name . ' is calling you.',
                'role' => $recipient->role?->slug,
                'read' => false,
                'meta' => ['call_id' => $call->id, 'type' => 'incoming_call'],
            ]);

            return $call->fresh(['caller', 'recipient']);
        });

        return response()->json(['call' => $this->formatCall($call)], 201);
    }

    /**
     * Get incoming calls for the user
     */
    public function incoming(Request $request): JsonResponse
    {
        $calls = Call::where('recipient_id', $request->user()->id)
            ->where('status', 'ringing')
            ->with(['caller:id,name,avatar', 'conversation:id'])
            ->latest()
            ->get();

        return response()->json(['calls' => $calls->map(fn ($call) => $this->formatCall($call))]);
    }

    /**
     * Issue a short-lived audio call token from the configured provider.
     */
    public function token(Call $call, Request $request): JsonResponse
    {
        $user = $request->user();
        $callType = $request->query('type', 'audio');

        abort_unless(in_array($user->id, [$call->caller_id, $call->recipient_id], true), 403, 'You are not part of this call.');

        $otherUserId = $user->id === $call->caller_id ? $call->recipient_id : $call->caller_id;
        $otherUser = User::findOrFail($otherUserId);

        abort_unless($this->messaging->canMessage($user, $otherUser), 403, 'You are not authorized to join this call.');

        $provider = app(LiveKitClassroomProvider::class);
        $token = $callType === 'video'
            ? $provider->issueVideoCallToken('call-' . $call->id, $user, false)
            : $provider->issueAudioCallToken('call-' . $call->id, $user, false, false);

        if (! $token) {
            return response()->json(['message' => 'LiveKit credentials are not configured. Add LIVE_VIDEO_API_URL, LIVE_VIDEO_API_KEY, and LIVE_VIDEO_API_SECRET to enable real call connectivity.'], 503);
        }

        return response()->json([
            'token' => $token,
            'provider_url' => config('live-classroom.production.api_url'),
            'room_name' => 'call-' . $call->id,
            'type' => $callType,
        ]);
    }

    /**
     * Accept an incoming call
     */
    public function accept(Call $call, Request $request): JsonResponse
    {
        abort_unless($call->recipient_id === $request->user()->id, 403);
        abort_unless($call->status === 'ringing', 422, 'This call is not available.');

        $call->update(['status' => 'connected', 'started_at' => now()]);

        CallHistory::create([
            'call_id' => $call->id,
            'user_id' => $request->user()->id,
            'action' => 'accepted',
        ]);

        return response()->json(['call' => $this->formatCall($call->fresh(['caller', 'recipient']))]);
    }

    /**
     * Decline an incoming call
     */
    public function decline(Call $call, Request $request): JsonResponse
    {
        abort_unless($call->recipient_id === $request->user()->id, 403);
        abort_unless($call->status === 'ringing', 422, 'This call cannot be declined.');

        $call->update(['status' => 'declined', 'ended_at' => now()]);

        CallHistory::create([
            'call_id' => $call->id,
            'user_id' => $request->user()->id,
            'action' => 'declined',
        ]);

        return response()->json(['call' => $this->formatCall($call)]);
    }

    /**
     * End a call
     */
    public function end(Call $call, Request $request): JsonResponse
    {
        abort_unless(in_array($request->user()->id, [$call->caller_id, $call->recipient_id], true), 403);
        abort_unless(in_array($call->status, ['ringing', 'connected'], true), 422, 'This call cannot be ended.');

        $wasConnected = $call->status === 'connected';
        $durationSeconds = 0;
        if ($wasConnected && $call->started_at) {
            // Calculate seconds between started_at and now (handle negative values)
            $durationSeconds = abs((int) now()->diffInSeconds($call->started_at, false));
        }
        
        $call->update([
            'status' => $wasConnected ? 'ended' : 'missed',
            'ended_at' => now(),
            'duration_seconds' => $durationSeconds,
        ]);

        CallHistory::create([
            'call_id' => $call->id,
            'user_id' => $request->user()->id,
            'action' => 'ended',
        ]);

        return response()->json(['call' => $this->formatCall($call)]);
    }

    /**
     * Get call history for a conversation
     */
    public function history(Conversation $conversation, Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($conversation->participants()->where('user_id', $user->id)->exists(), 403);

        $calls = $conversation->calls()
            ->with(['caller:id,name,avatar', 'recipient:id,name,avatar'])
            ->latest()
            ->limit(50)
            ->get();

        return response()->json(['calls' => $calls->map(fn ($call) => $this->formatCall($call))]);
    }

    /**
     * Get current active call for user
     */
    public function current(Request $request): JsonResponse
    {
        $call = Call::where(function ($query) use ($request) {
            $query->where('caller_id', $request->user()->id)
                ->orWhere('recipient_id', $request->user()->id);
        })
        ->whereIn('status', ['ringing', 'connected'])
        ->with(['caller:id,name,avatar', 'recipient:id,name,avatar'])
        ->latest()
        ->first();

        return response()->json(['call' => $call ? $this->formatCall($call) : null]);
    }

    private function formatCall(Call $call): array
    {
        return [
            'id' => $call->id,
            'conversation_id' => $call->conversation_id,
            'caller_id' => $call->caller_id,
            'recipient_id' => $call->recipient_id,
            'caller' => ['id' => $call->caller->id, 'name' => $call->caller->name, 'avatar' => $call->caller->avatar_url],
            'recipient' => ['id' => $call->recipient->id, 'name' => $call->recipient->name, 'avatar' => $call->recipient->avatar_url],
            'status' => $call->status,
            'duration_seconds' => $call->duration_seconds,
            'started_at' => $call->started_at?->toIso8601String(),
            'ended_at' => $call->ended_at?->toIso8601String(),
            'created_at' => $call->created_at->toIso8601String(),
        ];
    }
}
