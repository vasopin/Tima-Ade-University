<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $role = $user->role?->slug ?? 'guest';

        $notifications = Notification::where(function ($q) use ($user, $role) {
            $q->where('user_id', $user->id)
              ->orWhere(function ($q2) use ($role) {
                  $q2->whereNull('user_id')
                     ->whereIn('role', ['all', $role]);
              });
        })
            ->latest('created_at')
            ->take(12)
            ->get();

        return response()->json($notifications);
    }

    public function markRead(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $notification = Notification::whereKey($id)->first();

        if (!$notification || $notification->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $notification->update(['read' => true]);

        return response()->json(['status' => 'ok']);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $user = $request->user();

        Notification::where('user_id', $user->id)
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json(['status' => 'ok']);
    }

    public function store(Request $request): JsonResponse
    {
        // Only admin may create notifications
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'body'  => 'nullable|string',
            'role'  => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'meta'  => 'nullable|array',
        ]);

        $notification = Notification::create([
            'title' => $request->input('title'),
            'body'  => $request->input('body'),
            'role'  => $request->input('role', 'all'),
            'user_id' => $request->input('user_id'),
            'meta'  => $request->input('meta', []),
            'read'  => false,
        ]);

        return response()->json($notification, 201);
    }
}
