@extends('layouts.app')

@section('title', 'Attendance Management')

@section('breadcrumb')
    <li class="breadcrumb-item active">Attendance</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    @include('partials._alerts')

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="page-header-title mb-1">Daily Attendance Registry</h3>
            <p class="text-muted small mb-0">Record and inspect daily student roll call across all active classrooms.</p>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->isTeacher())
                <a href="{{ route('teacher.attendance') }}" class="btn btn-crimson shadow-sm">
                    <i class="bi bi-calendar-plus me-1"></i> Take Attendance
                </a>
            @elseif(auth()->user()->isAdmin())
                <a href="{{ route('attendance.create') }}" class="btn btn-crimson shadow-sm">
                    <i class="bi bi-calendar-plus me-1"></i> Take Attendance
                </a>
            @endif
            <a href="{{ route('attendance.report') }}" class="btn btn-navy shadow-sm">
                <i class="bi bi-file-earmark-bar-graph me-1"></i> Attendance Report
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card custom-card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('attendance.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1 fw-semibold">Attendance Date</label>
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date', today()->toDateString()) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1 fw-semibold">Filter Class</label>
                    <select name="class_id" class="form-select form-select-sm">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-sm btn-navy flex-grow-1">Filter Records</button>
                    @if(request()->hasAny(['date', 'class_id']))
                        <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="card custom-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Class & Section</th>
                        <th>Roll #</th>
                        <th>Status</th>
                        <th>Marked By</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                        <tr>
                            <td>
                                <span class="fw-semibold text-dark">{{ $att->attendance_date ? $att->attendance_date->format('M d, Y') : '—' }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $att->student->user->avatar_url }}" class="rounded-circle me-2" width="32" height="32" alt="">
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $att->student->user->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $att->student->admission_number ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $att->schoolClass->name }} ({{ $att->section->name }})</td>
                            <td><span class="badge bg-light text-dark border">{{ $att->student->roll_number }}</span></td>
                            <td>{!! $att->status_badge !!}</td>
                            <td class="small text-muted">{{ $att->markedBy->name ?? 'Admin' }}</td>
                            <td class="small text-muted">{{ $att->remarks ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 text-secondary d-block mb-2"></i>
                                No attendance recorded for this date/class. Click "Mark Daily Attendance" to record.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
