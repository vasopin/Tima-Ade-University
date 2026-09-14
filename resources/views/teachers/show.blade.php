@extends('layouts.app')

@section('title', $teacher->user->name . ' — Faculty Profile')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('teachers.index') }}">Teachers</a></li>
    <li class="breadcrumb-item active">{{ $teacher->user->name }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <img src="{{ $teacher->user->avatar_url }}" class="rounded-circle border shadow-sm me-3" width="64" height="64" alt="">
            <div>
                <h3 class="page-header-title mb-0">{{ $teacher->user->name }}</h3>
                <div class="text-muted small mt-1">
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle me-2">{{ $teacher->employee_id }}</span>
                    <span><i class="bi bi-envelope me-1"></i>{{ $teacher->user->email }}</span> • 
                    <span><i class="bi bi-award me-1"></i>{{ $teacher->qualification ?? 'Faculty' }}</span>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-crimson btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit Profile
            </a>
            <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-person-lines-fill me-2 text-danger"></i>Faculty Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Employee ID</span>
                            <span class="fw-bold">{{ $teacher->employee_id }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Qualification</span>
                            <span class="fw-semibold">{{ $teacher->qualification ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Specialization</span>
                            <span class="fw-semibold">{{ $teacher->specialization ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Class Teacher of</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                {{ $teacher->classTeacherOf ? $teacher->classTeacherOf->name : 'None' }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Joining Date</span>
                            <span class="fw-semibold">{{ $teacher->joining_date ? $teacher->joining_date->format('M d, Y') : 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Phone</span>
                            <span class="fw-semibold">{{ $teacher->user->phone ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Emergency Contact</span>
                            <span class="fw-semibold">{{ $teacher->emergency_contact ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item px-0">
                            <span class="text-muted d-block mb-1">Address</span>
                            <span class="fw-semibold">{{ $teacher->address ?? 'N/A' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-calendar-check-fill me-2 text-primary"></i>Recent Attendance Marked by Faculty</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Class</th>
                                <th>Student</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teacher->attendancesMarked->take(10) as $att)
                                <tr>
                                    <td>{{ $att->attendance_date ? $att->attendance_date->format('M d, Y') : '—' }}</td>
                                    <td>{{ $att->schoolClass->name ?? 'N/A' }}</td>
                                    <td>{{ $att->student->user->name ?? 'N/A' }}</td>
                                    <td>{!! $att->status_badge !!}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No recent attendance records marked.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
