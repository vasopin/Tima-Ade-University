@extends('layouts.app')

@section('title', 'My Classes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">My Classes</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="text-uppercase small text-primary fw-semibold mb-1">University Portal</div>
            <h2 class="mb-0">My Classes</h2>
        </div>
        <a href="{{ route('teacher.attendance') }}" class="btn btn-primary">
            <i class="bi bi-calendar-check me-1"></i> Attendance
        </a>
    </div>

    @if($teacherClasses->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-building fs-1 text-muted mb-3 d-block"></i>
                <h5 class="mb-2">No classes have been assigned to you yet.</h5>
                <p class="text-muted mb-0">Once your class assignments are linked, they will appear here.</p>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($teacherClasses as $class)
                <div class="col-xl-4 col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary mb-2">{{ $class->grade_level ?? 'Class' }}</span>
                                    <h4 class="mb-1">{{ $class->name }}</h4>
                                </div>
                                <span class="badge bg-light text-dark">{{ $class->students()->count() }} students</span>
                            </div>

                            <p class="text-muted mb-3">{{ $class->description ?: 'Core classroom schedule and learning activities.' }}</p>

                            <div class="row g-2 text-sm small">
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="text-muted">Students</div>
                                        <strong>{{ $class->students()->count() }}</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="text-muted">Courses</div>
                                        <strong>{{ $class->subjects()->count() }}</strong>
                                    </div>
                                </div>
                            </div>

                            @if($class->subjects->isNotEmpty())
                                <div class="mt-3">
                                    <div class="text-uppercase small text-muted fw-semibold mb-2">Courses</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($class->subjects as $subject)
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">{{ $subject->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="mt-4 d-flex gap-2">
                                <a href="{{ route('teacher.courses') }}" class="btn btn-sm btn-outline-primary">Manage courses</a>
                                <a href="{{ route('teacher.attendance', ['class_id' => $class->id]) }}" class="btn btn-sm btn-outline-dark">Attendance</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
