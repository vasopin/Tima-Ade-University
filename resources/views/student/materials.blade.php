@extends('layouts.app')

@section('title', 'Student Materials')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Student Dashboard</a></li>
    <li class="breadcrumb-item active">Materials</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <span class="eyebrow-label">Learning Resources</span>
            <h2 class="dashboard-section-header mb-0">Course Materials</h2>
        </div>
        @if($student && $student->schoolClass)
            <span class="badge bg-primary-subtle text-primary px-3 py-2">{{ $student->schoolClass->name }}</span>
        @endif
    </div>

    @if($materials->isNotEmpty())
        <div class="row g-3">
            @foreach($materials as $material)
                <div class="col-lg-6">
                    <div class="card custom-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <span class="badge bg-light text-dark mb-2">{{ $material->subject->name ?? 'Course' }}</span>
                                    <h5 class="mb-1">{{ $material->title }}</h5>
                                    <small class="text-muted">{{ $material->schoolClass->name ?? 'Class' }} · {{ $material->teacher->user->name ?? 'Instructor' }}</small>
                                </div>
                                <i class="bi bi-file-earmark-pdf fs-3 text-danger"></i>
                            </div>

                            <p class="text-muted mb-3">
                                {{ $material->description ?: 'No description provided for this learning resource.' }}
                            </p>

                            <div class="small text-muted d-flex flex-wrap gap-3 mb-3">
                                <span><i class="bi bi-calendar3 me-1"></i>{{ $material->created_at?->format('M d, Y') }}</span>
                                <span><i class="bi bi-file-earmark-text me-1"></i>{{ $material->mime_type ?? 'application/pdf' }}</span>
                                <span><i class="bi bi-download me-1"></i>{{ $material->readable_size ?? '—' }}</span>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ $material->file_url }}" class="btn btn-sm btn-primary" target="_blank">Open</a>
                                <a href="{{ $material->file_url }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener">Download</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-lms-state">
            <i class="bi bi-file-earmark-pdf me-2"></i>
            No course materials are available for your enrolled class yet.
        </div>
    @endif
</div>
@endsection
