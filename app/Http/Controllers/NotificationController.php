<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $roleSlug = $user->role->slug ?? 'all';

        $scopedNotifications = Notification::where(function ($query) use ($user, $roleSlug) {
            $query->where('user_id', $user->id)
                ->orWhere(function ($roleQuery) use ($roleSlug) {
                    $roleQuery->whereNull('user_id')
                        ->whereIn('role', ['all', $roleSlug]);
                });
        });

        $notifications = (clone $scopedNotifications)
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();
        $unreadCount = (clone $scopedNotifications)->where('read', false)->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a single notification as read. Only allows marking notifications that belong to the authenticated user.
     */
    public function markRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = Notification::whereKey($id)->first();

        if (!$notification || $notification->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $notification->update(['read' => true]);

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications belonging to the authenticated user as read.
     */
    public function markAllRead(Request $request)
    {
        $user = $request->user();

        Notification::where('user_id', $user->id)
            ->where('read', false)
            ->update(['read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
