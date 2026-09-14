@extends('layouts.app')

@section('title', 'My Courses')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">My Courses</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="text-uppercase small text-primary fw-semibold mb-1">University Portal</div>
            <h2 class="mb-0">My Courses</h2>
        </div>
        <a href="{{ route('teacher.videos') }}" class="btn btn-primary">
            <i class="bi bi-play-circle me-1"></i> View videos
        </a>
    </div>

    @if($teacherCourseRows->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-journal-bookmark fs-1 text-muted mb-3 d-block"></i>
                <h5 class="mb-2">No courses have been assigned to you yet.</h5>
                <p class="text-muted mb-0">Your assigned courses will appear here once they are linked to your teaching profile.</p>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($teacherCourseRows as $row)
                @php
                    $class = $row['class'];
                    $subject = $row['subject'];
                    $studentCount = \App\Models\Student::where('school_class_id', $class->id)->count();
                    $videoCount = \App\Models\LectureVideo::where('teacher_id', $teacher->id)->where('subject_id', $subject->id)->where('school_class_id', $class->id)->count();
                    $materialCount = \App\Models\CourseMaterial::where('teacher_id', $teacher->id)->where('subject_id', $subject->id)->where('school_class_id', $class->id)->count();
                    $assignmentCount = \App\Models\Assignment::where('teacher_id', $teacher->id)->where('subject_id', $subject->id)->where('school_class_id', $class->id)->count();
                @endphp

                <div class="col-xl-4 col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge bg-success-subtle text-success mb-2">{{ $class->name }}</span>
                                    <h4 class="mb-1">{{ $subject->name }}</h4>
                                </div>
                                <span class="badge bg-light text-dark">{{ $subject->code ?? 'Course' }}</span>
                            </div>

                            <p class="text-muted mb-3">{{ $subject->description ?: 'Course content and learning materials for this class.' }}</p>

                            <div class="row g-2 text-sm small">
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="text-muted">Students</div>
                                        <strong>{{ $studentCount }}</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="text-muted">Status</div>
                                        <strong>Active</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 d-flex flex-wrap gap-2">
                                <span class="badge bg-primary-subtle text-primary">{{ $videoCount }} videos</span>
                                <span class="badge bg-warning-subtle text-warning">{{ $materialCount }} materials</span>
                                <span class="badge bg-danger-subtle text-danger">{{ $assignmentCount }} assignments</span>
                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <a href="{{ route('teacher.videos') }}" class="btn btn-sm btn-outline-primary">Videos</a>
                                <a href="{{ route('teacher.materials') }}" class="btn btn-sm btn-outline-secondary">Materials</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
