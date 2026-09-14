@extends('layouts.app')

@section('title', 'Student Attendance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Student Dashboard</a></li>
    <li class="breadcrumb-item active">Attendance</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <span class="eyebrow-label">Learning Progress</span>
            <h2 class="dashboard-section-header mb-0">My Attendance</h2>
        </div>
        <span class="badge bg-success-subtle text-success px-3 py-2">{{ $presentDays ?? 0 }} present · {{ $attendanceRate ?? 0 }}%</span>
    </div>

    <div class="card custom-card mb-4">
        <div class="card-body">
            <div class="row g-3 text-center">
                <div class="col-md-4">
                    <div class="small text-muted">Present</div>
                    <h4 class="mb-0 text-success">{{ $presentDays ?? 0 }}</h4>
                </div>
                <div class="col-md-4">
                    <div class="small text-muted">Absent</div>
                    <h4 class="mb-0 text-danger">{{ $absentDays ?? 0 }}</h4>
                </div>
                <div class="col-md-4">
                    <div class="small text-muted">Attendance Rate</div>
                    <h4 class="mb-0 text-primary">{{ $attendanceRate ?? 0 }}%</h4>
                </div>
            </div>
        </div>
    </div>

    @if($attendances->isNotEmpty())
        <div class="card custom-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->attendance_date?->format('M d, Y') ?? 'N/A' }}</td>
                                    <td>{{ $attendance->schoolClass->name ?? 'Class' }}</td>
                                    <td>{{ $attendance->section->name ?? 'Section' }}</td>
                                    <td>
                                        @php
                                            $statusClass = match($attendance->status ?? '') {
                                                'present' => 'success',
                                                'absent' => 'danger',
                                                'late' => 'warning',
                                                'excused' => 'info',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }} text-uppercase">{{ $attendance->status ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $attendance->remarks ?: 'No remarks' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="empty-lms-state">
            <i class="bi bi-calendar2-x me-2"></i>
            No attendance records are available for you yet.
        </div>
    @endif
</div>
@endsection
