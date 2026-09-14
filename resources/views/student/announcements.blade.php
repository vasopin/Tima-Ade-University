@extends('layouts.app')

@section('title', 'Student Announcements')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Student Dashboard</a></li>
    <li class="breadcrumb-item active">Announcements</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <span class="eyebrow-label">School Communications</span>
            <h2 class="dashboard-section-header mb-0">Announcements</h2>
        </div>
        <span class="badge bg-info-subtle text-info px-3 py-2">{{ $announcements->count() }} active</span>
    </div>

    @if($announcements->isNotEmpty())
        <div class="row g-3">
            @foreach($announcements as $announcement)
                <div class="col-12">
                    <div class="card custom-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <span class="badge bg-light text-dark mb-2">{{ strtoupper($announcement->category ?? 'Notice') }}</span>
                                    <h5 class="mb-1">{{ $announcement->title }}</h5>
                                </div>
                                @if($announcement->is_pinned)
                                    <span class="badge bg-warning-subtle text-warning">Pinned</span>
                                @endif
                            </div>

                            <p class="text-muted mb-3">{{ $announcement->content }}</p>

                            <div class="small text-muted d-flex flex-wrap gap-3">
                                <span><i class="bi bi-calendar3 me-1"></i>{{ $announcement->published_date?->format('M d, Y') ?? 'Date not set' }}</span>
                                <span><i class="bi bi-clock-history me-1"></i>{{ $announcement->created_at?->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-lms-state">
            <i class="bi bi-megaphone me-2"></i>
            No announcements at this time.
        </div>
    @endif
</div>
@endsection
