<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\FriendRequest;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use App\Services\MessagingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessagingController extends Controller
{
    public function __construct(private MessagingService $messaging) {}

    public function index()
    {
        return view('messages.index');
    }

    public function conversations(Request $request): JsonResponse
    {
        $user = $request->user();
        $conversations = Conversation::whereHas('participants', fn ($query) => $query->whereKey($user->id))
            ->with([
                'participants:id,name,avatar',
                'messages' => fn ($query) => $query->latest(),
                'voiceMessages' => fn ($query) => $query->latest(),
            ])
            ->withCount(['messages as unread_count' => fn ($query) => $query
                ->whereNull('read_at')
                ->where('sender_id', '!=', $user->id)
                ->whereNull('deleted_for_everyone_at')
                ->whereDoesntHave('deletedForUsers', fn ($q) => $q->where('user_id', $user->id))])
            ->withCount(['voiceMessages as unread_voice_count' => fn ($query) => $query->whereNull('played_at')->where('sender_id', '!=', $user->id)])
            ->latest('updated_at')
            ->get()
            ->map(function (Conversation $conversation) use ($user) {
                $other = $conversation->participants->firstWhere('id', '!=', $user->id);
                $lastMessage = $conversation->messages
                    ->filter(fn ($msg) => $msg->deleted_for_everyone_at === null && !$msg->isDeletedForUser($user->id))
                    ->map(fn ($message) => [
                        'body' => $message->body,
                        'created_at' => $message->created_at,
                        'type' => 'text',
                    ])
                    ->concat($conversation->voiceMessages->map(fn ($message) => [
                        'body' => 'Voice note',
                        'created_at' => $message->created_at,
                        'type' => 'voice',
                    ]))
                    ->sortByDesc('created_at')
                    ->first();

                return [
                    'id' => $conversation->id,
                    'recipient' => $other ? ['id' => $other->id, 'name' => $other->name, 'avatar' => $other->avatar_url] : null,
                    'last_message' => $lastMessage['body'] ?? null,
                    'last_message_type' => $lastMessage['type'] ?? null,
                    'last_message_at' => ($lastMessage['created_at'] ?? null)?->toIso8601String(),
                    'unread_count' => $conversation->unread_count + $conversation->unread_voice_count,
                ];
            });

        return response()->json(['conversations' => $conversations]);
    }

    public function show(Conversation $conversation, Request $request): JsonResponse
    {
        $this->ensureParticipant($conversation, $request->user());
        $user = $request->user();

        $conversation->messages()->whereNull('read_at')->where('sender_id', '!=', $user->id)->update(['read_at' => now()]);
        $conversation->participants()->updateExistingPivot($user->id, ['last_read_at' => now()]);

        $textMessages = $conversation->messages()
            ->where(function ($query) {
                $query->whereNull('deleted_for_everyone_at');
            })
            ->with(['sender:id,name', 'deletedForUsers:id'])
            ->oldest()
            ->get()
            ->filter(fn ($message) => !$message->isDeletedForUser($user->id))
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'type' => 'text',
                    'conversation_id' => $message->conversation_id,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender?->name,
                    'body' => $message->body,
                    'created_at' => $message->created_at->toIso8601String(),
                ];
            });
        $voiceMessages = $conversation->voiceMessages()->with('sender:id,name')->oldest()->get()->map(function ($voiceMessage) {
            return [
                'id' => $voiceMessage->id,
                'type' => 'voice',
                'conversation_id' => $voiceMessage->conversation_id,
                'sender_id' => $voiceMessage->sender_id,
                'sender_name' => $voiceMessage->sender?->name,
                'file_path' => $voiceMessage->file_path,
                'audio_url' => $voiceMessage->audio_url,
                'duration_seconds' => (int) $voiceMessage->duration_seconds,
                'mime_type' => $voiceMessage->mime_type,
                'created_at' => $voiceMessage->created_at->toIso8601String(),
            ];
        });
        $messages = $textMessages->concat($voiceMessages)->sortBy('created_at')->values();

        return response()->json([
            'conversation' => $conversation->load('participants:id,name,avatar'),
            'messages' => $messages,
            'voice_messages' => $voiceMessages,
        ]);
    }

    public function start(Request $request): JsonResponse
    {
        $data = $request->validate(['recipient_id' => ['required', 'integer', 'exists:users,id']]);
        $recipient = User::findOrFail($data['recipient_id']);

        abort_unless($this->messaging->canMessage($request->user(), $recipient), 403, 'You are not authorized to message this user.');

        return response()->json(['conversation' => $this->messaging->conversationFor($request->user(), $recipient)], 201);
    }

    public function send(Conversation $conversation, Request $request): JsonResponse
    {
        $this->ensureParticipant($conversation, $request->user());
        $data = $request->validate(['body' => ['required', 'string', 'max:5000']]);
        $sender = $request->user();
        $recipient = $conversation->participants()->where('users.id', '!=', $sender->id)->firstOrFail();

        abort_unless($this->messaging->canMessage($sender, $recipient), 403, 'You are not authorized to message this user.');

        $message = DB::transaction(function () use ($conversation, $sender, $recipient, $data) {
            $message = $conversation->messages()->create(['sender_id' => $sender->id, 'body' => trim($data['body'])]);
            Notification::create([
                'user_id' => $recipient->id,
                'title' => 'New private message',
                'body' => $sender->name . ' sent you a private message.',
                'role' => $recipient->role?->slug,
                'read' => false,
                'meta' => ['conversation_id' => $conversation->id],
            ]);

            return $message;
        });

        return response()->json(['message' => [
            'id' => $message->id,
            'type' => 'text',
            'conversation_id' => $message->conversation_id,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender?->name,
            'body' => $message->body,
            'created_at' => $message->created_at->toIso8601String(),
        ]], 201);
    }

    public function students(Request $request): JsonResponse
    {
        abort_unless($request->user()->isStudent(), 403);
        $query = $request->validate(['q' => ['nullable', 'string', 'max:80']])['q'] ?? '';

        $students = User::whereHas('role', fn ($role) => $role->where('slug', 'student'))
            ->where('users.id', '!=', $request->user()->id)
            ->where('name', 'like', '%' . $query . '%')
            ->select('id', 'name', 'avatar')
            ->limit(20)->get();

        return response()->json(['students' => $students->map(fn ($student) => ['id' => $student->id, 'name' => $student->name, 'avatar' => $student->avatar_url])]);
    }

    public function contacts(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = $request->validate(['q' => ['nullable', 'string', 'max:80']])['q'] ?? '';
        $contacts = User::with('role')
            ->where('users.id', '!=', $user->id)
            ->where('name', 'like', '%' . $query . '%')
            ->where('is_active', true)
            ->limit(50)
            ->get()
            ->filter(fn (User $contact) => $this->messaging->canMessage($user, $contact))
            ->values();

        return response()->json(['contacts' => $contacts->map(fn (User $contact) => [
            'id' => $contact->id,
            'name' => $contact->name,
            'role' => $contact->role?->name,
            'avatar' => $contact->avatar_url,
        ])]);
    }

    public function friendRequests(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json(['requests' => FriendRequest::with(['requester:id,name,avatar', 'recipient:id,name,avatar'])
            ->where(fn ($query) => $query->where('recipient_id', $user->id)->orWhere('requester_id', $user->id))
            ->latest()->get()]);
    }

    public function sendFriendRequest(Request $request): JsonResponse
    {
        abort_unless($request->user()->isStudent(), 403);
        $data = $request->validate(['recipient_id' => ['required', 'integer', 'exists:users,id']]);
        abort_if((int) $data['recipient_id'] === $request->user()->id, 422, 'You cannot add yourself.');
        $recipient = User::findOrFail($data['recipient_id']);
        abort_unless($recipient->isStudent(), 422, 'Friend requests are for students only.');

        $existing = FriendRequest::where(function ($query) use ($request, $recipient) {
            $query->where('requester_id', $request->user()->id)->where('recipient_id', $recipient->id);
        })->orWhere(function ($query) use ($request, $recipient) {
            $query->where('requester_id', $recipient->id)->where('recipient_id', $request->user()->id);
        })->first();

        abort_if($existing?->status === 'pending', 422, 'A pending friend request already exists.');
        abort_if($existing?->status === 'accepted', 422, 'You are already friends.');

        $friendRequest = FriendRequest::updateOrCreate(
            ['requester_id' => $request->user()->id, 'recipient_id' => $recipient->id],
            ['status' => 'pending']
        );
        Notification::create(['user_id' => $recipient->id, 'title' => 'New friend request', 'body' => $request->user()->name . ' sent you a friend request.', 'role' => $recipient->role?->slug, 'read' => false, 'meta' => ['friend_request_id' => $friendRequest->id]]);

        return response()->json(['request' => $friendRequest], 201);
    }

    public function updateFriendRequest(FriendRequest $friendRequest, Request $request, string $status): JsonResponse
    {
        abort_unless($friendRequest->recipient_id === $request->user()->id, 403);
        abort_unless(in_array($status, ['accepted', 'declined'], true), 404);
        abort_if($friendRequest->status !== 'pending', 422, 'This request has already been decided.');
        $friendRequest->update(['status' => $status]);
        return response()->json(['request' => $friendRequest]);
    }

    public function removeFriend(FriendRequest $friendRequest, Request $request): JsonResponse
    {
        abort_unless(in_array($request->user()->id, [$friendRequest->requester_id, $friendRequest->recipient_id], true), 403);
        abort_unless($friendRequest->status === 'accepted', 422, 'Only accepted friendships can be removed.');
        $friendRequest->update(['status' => 'removed']);

        return response()->json(['request' => $friendRequest]);
    }

    public function deleteMessage(Message $message, Request $request): JsonResponse
    {
        $conversation = $message->conversation;
        $this->ensureParticipant($conversation, $request->user());
        
        $data = $request->validate(['scope' => ['required', 'in:me,everyone']]);
        
        if ($data['scope'] === 'everyone') {
            abort_unless($message->sender_id === $request->user()->id, 403, 'Only the sender can delete a message for everyone.');
            abort_unless($message->created_at->addHours(24)->isFuture(), 422, 'Messages can only be deleted for everyone within 24 hours of sending.');
            
            $message->update(['deleted_for_everyone_at' => now()]);
        } else {
            $message->deletedForUsers()->attach($request->user()->id);
        }
        
        return response()->json(['success' => true]);
    }

    private function ensureParticipant(Conversation $conversation, User $user): void
    {
        abort_unless($conversation->participants()->whereKey($user->id)->exists(), 403, 'You are not a participant in this conversation.');
    }
}
