@extends('layouts.app')

@section('title', 'Student Videos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Student Dashboard</a></li>
    <li class="breadcrumb-item active">Videos</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <span class="eyebrow-label">Learning Resources</span>
            <h2 class="dashboard-section-header mb-0">Videos</h2>
        </div>
        @if($student && $student->schoolClass)
            <span class="badge bg-primary-subtle text-primary px-3 py-2">{{ $student->schoolClass->name }}</span>
        @endif
    </div>

    @if($videos->isNotEmpty())
        <div class="row g-3">
            @foreach($videos as $video)
                <div class="col-lg-6">
                    <div class="card custom-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <span class="badge bg-light text-dark mb-2">{{ $video->subject->name ?? 'Course' }}</span>
                                    <h5 class="mb-1">{{ $video->title }}</h5>
                                    <small class="text-muted">{{ $video->schoolClass->name ?? 'Class' }} · {{ $video->teacher->user->name ?? 'Instructor' }}</small>
                                </div>
                                <a href="{{ $video->file_url }}" target="_blank" class="btn btn-sm btn-primary">Open</a>
                            </div>

                            <p class="text-muted mb-3">
                                {{ $video->description ?: 'No description provided for this lecture video.' }}
                            </p>

                            <div class="small text-muted d-flex flex-wrap gap-3">
                                <span><i class="bi bi-calendar3 me-1"></i>{{ $video->created_at?->format('M d, Y') }}</span>
                                <span><i class="bi bi-film me-1"></i>{{ $video->mime_type ?? 'video/mp4' }}</span>
                                <span><i class="bi bi-download me-1"></i>{{ $video->readable_size ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-lms-state">
            <i class="bi bi-play-circle me-2"></i>
            No lecture videos are available for your enrolled class yet.
        </div>
    @endif
</div>
@endsection
