@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid px-4 py-4 bg-light min-vh-100">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <p class="text-uppercase text-muted small fw-semibold mb-1">Account</p>
            <h1 class="h2 mb-1">Notifications</h1>
            <p class="text-muted mb-0">System and admissions updates for {{ auth()->user()->name }}.</p>
        </div>
        @if($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.readAll') }}" class="flex-shrink-0">
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-check2-all me-1" aria-hidden="true"></i>Mark all my notifications read
                </button>
            </form>
        @endif
    </div>

    <section class="card border-0 shadow-sm" aria-labelledby="notification-list-heading">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center gap-2">
            <h2 id="notification-list-heading" class="h5 mb-0">Notification center</h2>
            <span class="text-muted small">{{ $notifications->total() }} {{ Str::plural('notification', $notifications->total()) }}</span>
        </div>

        @forelse($notifications as $notification)
            <article class="border-bottom p-3 p-md-4 {{ $notification->read ? 'bg-white' : 'bg-primary bg-opacity-10' }}">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
                    <div class="d-flex gap-3">
                        <span class="text-primary fs-4" aria-hidden="true"><i class="bi bi-{{ $notification->read ? 'bell' : 'bell-fill' }}"></i></span>
                        <div>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <h3 class="h6 mb-0">{{ $notification->title }}</h3>
                                @if(!$notification->read)
                                    <span class="badge text-bg-primary">Unread</span>
                                @else
                                    <span class="badge text-bg-light border text-secondary">Read</span>
                                @endif
                                @if($notification->role && $notification->user_id === null)
                                    <span class="badge text-bg-secondary">{{ $notification->role === 'all' ? 'All users' : Str::title(str_replace('_', ' ', $notification->role)) }}</span>
                                @endif
                            </div>
                            @if($notification->body)
                                <p class="mb-2 text-secondary">{{ $notification->body }}</p>
                            @endif
                            <time class="small text-muted" datetime="{{ $notification->created_at?->toIso8601String() }}">
                                {{ $notification->created_at?->format('M j, Y, g:i A') }}
                            </time>
                        </div>
                    </div>
                    @if($notification->user_id === auth()->id() && !$notification->read)
                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="flex-shrink-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Mark as read</button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="text-center p-5">
                <i class="bi bi-bell-slash fs-1 text-muted" aria-hidden="true"></i>
                <h2 class="h5 mt-3">No notifications yet</h2>
                <p class="text-muted mb-0">New system and admissions updates will appear here.</p>
            </div>
        @endforelse

        @if($notifications->hasPages())
            <div class="p-3">{{ $notifications->links() }}</div>
        @endif
    </section>
</div>
@endsection
