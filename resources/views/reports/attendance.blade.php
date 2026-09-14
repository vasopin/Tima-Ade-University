@extends('layouts.app')

@section('title', 'Attendance Reports')

@section('breadcrumb')
    <li class="breadcrumb-item">Reports</li>
    <li class="breadcrumb-item active">Attendance Report</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title mb-1">Attendance Analytics & Reports</h3>
            <p class="text-muted small mb-0">Generate date-range attendance summaries, presence percentages, and absent logs.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.attendance.export', request()->all()) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-1"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card custom-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.attendance') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClass == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">From Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">To Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-navy w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Student Attendance Summary Table -->
    <div class="card custom-card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0"><i class="bi bi-person-lines-fill me-2 text-danger"></i>Student Attendance Summary ({{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }})</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Present Days</th>
                        <th>Absent Days</th>
                        <th>Late Days</th>
                        <th>Total Marked</th>
                        <th>Attendance Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($summary as $row)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $row['student']->user->avatar_url }}" class="rounded-circle me-2" width="32" height="32" alt="">
                                    <div>
                                        <div class="fw-semibold">{{ $row['student']->user->name }}</div>
                                        <small class="text-muted">{{ $row['student']->roll_number }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $row['student']->schoolClass->name }}</td>
                            <td><span class="badge bg-success-subtle text-success fw-bold">{{ $row['present'] }}</span></td>
                            <td><span class="badge bg-danger-subtle text-danger fw-bold">{{ $row['absent'] }}</span></td>
                            <td><span class="badge bg-warning-subtle text-warning fw-bold">{{ $row['late'] }}</span></td>
                            <td>{{ $row['total'] }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar {{ $row['pct'] >= 75 ? 'bg-success' : ($row['pct'] >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                             style="width: {{ $row['pct'] }}%"></div>
                                    </div>
                                    <span class="small fw-bold">{{ $row['pct'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No attendance records found for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
