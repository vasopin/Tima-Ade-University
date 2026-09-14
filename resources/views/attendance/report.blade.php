@extends('layouts.app')

@section('title', 'Attendance Reports')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}">Attendance</a></li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="page-header-title mb-1">Attendance Analytics & Reports</h3>
            <p class="text-muted small mb-0">Generate periodic classroom attendance breakdowns and rate summaries.</p>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->isTeacher())
                <a href="{{ route('teacher.attendance') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-calendar-plus me-1"></i> Take Attendance
                </a>
            @elseif(auth()->user()->isAdmin())
                <a href="{{ route('attendance.create') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-calendar-plus me-1"></i> Take Attendance
                </a>
            @endif
            <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Daily Register
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card custom-card mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('attendance.report') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label required fw-semibold">Class</label>
                    <select name="class_id" class="form-select" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">From Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">To Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-navy w-100 fw-semibold">
                        <i class="bi bi-filter me-1"></i> Generate
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($report !== null)
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-table me-2 text-danger"></i>Class Attendance Summary</h5>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print Report
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Roll #</th>
                            <th>Student</th>
                            <th class="text-center text-success">Present</th>
                            <th class="text-center text-danger">Absent</th>
                            <th class="text-center text-warning">Late</th>
                            <th class="text-center text-info">Excused</th>
                            <th class="text-center">Total Sessions</th>
                            <th class="text-end">Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report as $student)
                            @php
                                $present = $student->attendances->where('status', 'present')->count();
                                $absent  = $student->attendances->where('status', 'absent')->count();
                                $late    = $student->attendances->where('status', 'late')->count();
                                $excused = $student->attendances->where('status', 'excused')->count();
                                $total   = $student->attendances->count();
                                $pct     = $total > 0 ? round(($present / $total) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td><span class="badge bg-light text-dark border">{{ $student->roll_number }}</span></td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $student->user->name }}</div>
                                    <small class="text-muted">{{ $student->admission_number }}</small>
                                </td>
                                <td class="text-center fw-bold text-success">{{ $present }}</td>
                                <td class="text-center fw-bold text-danger">{{ $absent }}</td>
                                <td class="text-center fw-bold text-warning">{{ $late }}</td>
                                <td class="text-center fw-bold text-info">{{ $excused }}</td>
                                <td class="text-center fw-bold">{{ $total }}</td>
                                <td class="text-end">
                                    <span class="badge {{ $pct >= 75 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $pct }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No students found in this class.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
