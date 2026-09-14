@extends('layouts.app')

@section('title', 'My Courses')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Student Dashboard</a></li>
    <li class="breadcrumb-item active">My Courses</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <span class="eyebrow-label">Student LMS</span>
            <h2 class="dashboard-section-header mb-0">My Courses</h2>
        </div>
        @if($student && $student->schoolClass)
            <span class="badge bg-success-subtle text-success px-3 py-2">{{ $student->schoolClass->name }}</span>
        @endif
    </div>

    @if($student && $subjects->isNotEmpty())
        <div class="row g-3">
            @foreach($subjects as $subject)
                <div class="col-lg-4 col-md-6">
                    <div class="course-portfolio-card h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="course-chip">{{ $student->schoolClass->name ?? 'Class' }}</span>
                            <span class="badge bg-light text-dark">{{ $subject->code ?? 'Course' }}</span>
                        </div>
                        <h5 class="fw-bold mb-2">{{ $subject->name }}</h5>
                        <p class="small text-muted mb-3">
                            {{ $subject->description ?? 'Continue learning with course materials, videos, and assignments for this subject.' }}
                        </p>

                        <div class="mini-stat-grid mb-3">
                            <span><i class="bi bi-play-circle me-1"></i>{{ $subject->lectureVideos()->count() }} videos</span>
                            <span><i class="bi bi-file-earmark-pdf me-1"></i>{{ $subject->courseMaterials()->count() }} materials</span>
                            <span><i class="bi bi-list-task me-1"></i>{{ $subject->assignments()->count() }} assignments</span>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('student.videos') }}" class="btn btn-sm btn-outline-primary">Videos</a>
                            <a href="{{ route('student.materials') }}" class="btn btn-sm btn-outline-secondary">Materials</a>
                            <a href="{{ route('student.assignments') }}" class="btn btn-sm btn-outline-dark">Assignments</a>
                            @php($courseEnrollment = $enrollments->first(fn ($enrollment) => (int) $enrollment->courseSection?->course_id === (int) $subject->id))
                            @if($courseEnrollment)
                                <a href="{{ route('student.enrollment.completion', $courseEnrollment) }}" class="btn btn-sm btn-outline-success">Progress</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-lms-state">
            <i class="bi bi-journal-bookmark me-2"></i>
            You are not currently enrolled in any courses.
        </div>
    @endif
</div>
@endsection
