@extends('layouts.app')

@section('title', 'Live Class Monitoring')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <span class="text-uppercase small text-muted fw-semibold">Administration</span>
            <h2 class="mb-1">Live Class Monitoring</h2>
            <p class="text-muted mb-0">Review active classrooms, participants, and recorded activity.</p>
        </div>
        <span class="badge bg-danger-subtle text-danger px-3 py-2">{{ $liveClasses->where('status', 'live')->count() }} active</span>
    </div>

    <div class="row g-3">
        @forelse($liveClasses as $liveClass)
            <div class="col-xl-6">
                <article class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between gap-3 flex-wrap mb-3">
                            <div>
                                <span class="badge {{ $liveClass->status === 'live' ? 'bg-danger' : 'bg-secondary' }} mb-2">{{ strtoupper($liveClass->status) }}</span>
                                <h5 class="mb-1">{{ $liveClass->title }}</h5>
                                <p class="text-muted mb-0">{{ $liveClass->subject->name ?? 'Course' }} · {{ $liveClass->schoolClass->name ?? 'Class' }}</p>
                            </div>
                            <a href="{{ route('live-classes.show', $liveClass) }}" class="btn btn-outline-primary btn-sm">Open classroom</a>
                        </div>
                        <dl class="row small mb-3">
                            <dt class="col-sm-4 text-muted">Teacher</dt>
                            <dd class="col-sm-8">{{ $liveClass->teacher->name }}</dd>
                            <dt class="col-sm-4 text-muted">Started</dt>
                            <dd class="col-sm-8">{{ $liveClass->started_at?->format('M d, Y g:i A') ?? 'Not started' }}</dd>
                            <dt class="col-sm-4 text-muted">Participants</dt>
                            <dd class="col-sm-8">{{ $liveClass->participants->where('is_active', true)->count() }}</dd>
                        </dl>
                        <h6 class="small text-uppercase text-muted">Recent activity</h6>
                        <ul class="list-group list-group-flush small">
                            @forelse($liveClass->activities->take(5) as $activity)
                                <li class="list-group-item px-0 d-flex justify-content-between gap-2">
                                    <span>{{ str_replace('_', ' ', ucfirst($activity->action)) }}</span>
                                    <time class="text-muted">{{ $activity->created_at->format('g:i A') }}</time>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted">No activity recorded.</li>
                            @endforelse
                        </ul>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-light border">No live-class activity is available.</div></div>
        @endforelse
    </div>
</div>
@endsection
