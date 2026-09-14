@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid px-0 admin-dashboard-page">

    <!-- ===== WELCOME BANNER ===== -->
    <div class="welcome-banner mb-4">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <span class="badge bg-white text-dark px-3 py-1 fw-bold rounded-pill">
                        <i class="bi bi-shield-check text-success me-1"></i> Tima-Ade University Management Suite
                    </span>
                    <span class="text-white-50 small"><i class="bi bi-clock me-1"></i>{{ now()->format('l, F j, Y') }}</span>
                </div>
                <h2 class="welcome-title mb-1">Welcome back, {{ auth()->user()->name }}! 👋</h2>
                <p class="welcome-text mb-0">Here is the real-time operational overview across all academic and administrative departments.</p>
            </div>
            <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    <a href="{{ route('students.create') }}" class="btn btn-crimson shadow-sm">
                        <i class="bi bi-person-plus me-1"></i> Add Student
                    </a>
                    <a href="{{ route('attendance.create') }}" class="btn btn-navy shadow-sm">
                        <i class="bi bi-calendar-check me-1"></i> Roll Call
                    </a>
                    <a href="{{ route('fees.create') }}" class="btn btn-light shadow-sm text-dark">
                        <i class="bi bi-cash me-1"></i> Record Fee
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== PRIMARY STAT CARDS ===== -->
    <div class="row g-3 mb-3">
        <!-- Students -->
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card stat-card-students">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Total Enrolled</span>
                        <h3 class="stat-number">{{ number_format($stats['total_students']) }}</h3>
                        <span class="stat-subtext text-success">
                            <i class="bi bi-check-circle-fill me-1"></i>{{ $stats['active_students'] }} Active Scholars
                        </span>
                    </div>
                    <div class="stat-icon-wrapper icon-students">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Faculty -->
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card stat-card-teachers">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Teaching Staff</span>
                        <h3 class="stat-number">{{ number_format($stats['total_teachers']) }}</h3>
                        <span class="stat-subtext text-info">
                            <i class="bi bi-mortarboard-fill me-1"></i>Active Faculty
                        </span>
                    </div>
                    <div class="stat-icon-wrapper icon-teachers">
                        <i class="bi bi-person-workspace"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Classes -->
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card stat-card-classes">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Classes & Grades</span>
                        <h3 class="stat-number">{{ number_format($stats['total_classes']) }}</h3>
                        <span class="stat-subtext text-warning">
                            <i class="bi bi-layers-fill me-1"></i>Academic Batches
                        </span>
                    </div>
                    <div class="stat-icon-wrapper icon-classes">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fees Collected -->
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card stat-card-fees">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Total Revenue</span>
                        <h3 class="stat-number">${{ number_format($stats['fees_collected'], 2) }}</h3>
                        <span class="stat-subtext text-danger">
                            <i class="bi bi-hourglass-split me-1"></i>{{ $stats['fees_pending'] }} Pending Dues
                        </span>
                    </div>
                    <div class="stat-icon-wrapper icon-fees">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SECONDARY MINI KPI ROW ===== -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card custom-card p-3 border-0 bg-white shadow-sm d-flex flex-row align-items-center gap-3">
                <div class="p-2 rounded bg-primary-subtle text-primary">
                    <i class="bi bi-journal-check fs-4"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">{{ $stats['scheduled_exams'] }} Exams</h6>
                    <small class="text-muted">Scheduled</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card custom-card p-3 border-0 bg-white shadow-sm d-flex flex-row align-items-center gap-3">
                <div class="p-2 rounded bg-success-subtle text-success">
                    <i class="bi bi-person-check fs-4"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">{{ $stats['attendance_rate'] }}% Rate</h6>
                    <small class="text-muted">Today's Presence</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card custom-card p-3 border-0 bg-white shadow-sm d-flex flex-row align-items-center gap-3">
                <div class="p-2 rounded bg-warning-subtle text-warning">
                    <i class="bi bi-megaphone fs-4"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">{{ $stats['active_notices'] }} Notices</h6>
                    <small class="text-muted">Published</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card custom-card p-3 border-0 bg-white shadow-sm d-flex flex-row align-items-center gap-3">
                <div class="p-2 rounded bg-info-subtle text-info">
                    <i class="bi bi-journal-check fs-4"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">{{ $stats['total_tests'] }} Tests</h6>
                    <small class="text-muted">{{ $stats['published_tests'] }} published</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card custom-card p-3 border-0 bg-white shadow-sm d-flex flex-row align-items-center gap-3">
                <div class="p-2 rounded bg-danger-subtle text-danger">
                    <i class="bi bi-envelope-open fs-4"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">{{ $stats['new_inquiries'] }} Inquiries</h6>
                    <small class="text-muted">Admissions</small>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== CHARTS & VISUAL ANALYTICS ===== -->
    <div class="row g-3 mb-4">
        <!-- Attendance Overview -->
        <div class="col-lg-8">
            <div class="card custom-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="card-title mb-0"><i class="bi bi-bar-chart-line-fill me-2 text-danger"></i>Weekly Attendance Trend</h5>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge bg-light text-dark">Last 7 Days</span>
                        <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-outline-secondary">Details</a>
                    </div>
                </div>
                <div class="card-body">
                    <div style="height: 280px; position: relative;">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Attendance Snapshot -->
        <div class="col-lg-4">
            <div class="card custom-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Today's Attendance</h5>
                    <a href="{{ route('attendance.create') }}" class="btn btn-sm btn-outline-primary">Mark</a>
                </div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <div style="height: 180px; width: 180px; position: relative;">
                        <canvas id="todayAttendancePie"></canvas>
                    </div>
                    <div class="row w-100 text-center mt-3 g-2">
                        <div class="col-6">
                            <div class="p-2 border rounded bg-success-subtle text-success">
                                <span class="d-block small fw-semibold">Present Today</span>
                                <span class="fs-5 fw-bold">{{ $stats['present_today'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded bg-danger-subtle text-danger">
                                <span class="d-block small fw-semibold">Absent Today</span>
                                <span class="fs-5 fw-bold">{{ $stats['absent_today'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== RECENT STUDENTS & FEE TRANSACTIONS ===== -->
    <div class="row g-3 mb-4">
        <!-- Recent Students -->
        <div class="col-lg-6">
            <div class="card custom-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="card-title mb-0"><i class="bi bi-person-vcard me-2 text-danger"></i>Recently Enrolled Students</h5>
                    <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Class & Section</th>
                                <th>Roll No</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentStudents as $student)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $student->user->avatar_url }}" class="rounded-circle me-2" width="34" height="34" alt="">
                                            <div>
                                                <a href="{{ route('students.show', $student) }}" class="fw-semibold text-dark text-decoration-none">
                                                    {{ $student->user->name }}
                                                </a>
                                                <div class="small text-muted">{{ $student->admission_number }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $student->schoolClass->name ?? 'N/A' }} ({{ $student->section->name ?? 'A' }})</td>
                                    <td><span class="badge bg-light text-dark">{{ $student->roll_number }}</span></td>
                                    <td>{!! $student->status_badge !!}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No students enrolled yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Fee Transactions -->
        <div class="col-lg-6">
            <div class="card custom-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="card-title mb-0"><i class="bi bi-receipt me-2 text-primary"></i>Recent Fee Payments</h5>
                    <a href="{{ route('fees.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Receipt</th>
                                <th>Student</th>
                                <th>Fee Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                                <tr>
                                    <td><code>{{ $payment->receipt_number }}</code></td>
                                    <td>{{ $payment->student->user->name ?? 'N/A' }}</td>
                                    <td>{{ $payment->feeStructure->fee_type ?? 'Tuition Fee' }}</td>
                                    <td class="fw-bold text-success">${{ number_format($payment->amount_paid, 2) }}</td>
                                    <td>{!! $payment->status_badge !!}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No fee payments recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== EXAMS & NOTICES WIDGETS ===== -->
    <div class="row g-3 mb-4">
        <!-- Upcoming Examinations -->
        <div class="col-lg-6">
            <div class="card custom-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="card-title mb-0"><i class="bi bi-journal-text me-2 text-warning"></i>Examinations & Tests</h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark">{{ $stats['total_tests'] }} tests</span>
                        <a href="{{ route('exams.index') }}" class="btn btn-sm btn-outline-secondary">All Exams</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($upcomingExams as $exam)
                            <li class="list-group-item p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-dark">{{ $exam->name }}</div>
                                    <small class="text-muted">
                                        <i class="bi bi-building me-1"></i>{{ $exam->schoolClass->name }} | 
                                        <i class="bi bi-book me-1"></i>{{ $exam->subject->name }} | 
                                        <i class="bi bi-calendar me-1"></i>{{ $exam->exam_date->format('M d, Y') }}
                                    </small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    {!! $exam->status_badge !!}
                                    <a href="{{ route('exams.marks', $exam) }}" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item p-4 text-center text-muted">No examinations scheduled.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Latest Campus Announcements -->
        <div class="col-lg-6">
            <div class="card custom-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="card-title mb-0"><i class="bi bi-megaphone me-2 text-danger"></i>Latest Campus Notices</h5>
                    <a href="{{ route('admin.notices.index') }}" class="btn btn-sm btn-outline-secondary">Manage</a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($recentNotices as $notice)
                            <li class="list-group-item p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-dark">{{ $notice->title }}</div>
                                    <small class="text-muted">
                                        <span class="badge bg-light text-dark text-uppercase me-1">{{ $notice->type }}</span>
                                        <i class="bi bi-clock me-1"></i>{{ $notice->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <a href="{{ route('public.notices.single', $notice->slug ?? $notice->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item p-4 text-center text-muted">No notices posted.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card custom-card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-white"><h5 class="card-title mb-0"><i class="bi bi-camera-video me-2 text-primary"></i>Notice Board Videos</h5><a href="{{ route('admin.notices.videos') }}" class="btn btn-sm btn-outline-primary">Manage Videos</a></div>
        <div class="card-body"><div class="row g-3">
            @forelse($recentVideoNotices as $video)
                <div class="col-md-4"><a href="{{ route('admin.notices.edit', $video) }}" class="text-decoration-none"><div class="border rounded p-3 h-100"><div class="small text-muted mb-1">{{ ucfirst($video->status ?: 'draft') }} · {{ $video->category }}</div><div class="fw-bold text-dark">{{ $video->title }}</div><div class="small text-muted mt-2">{{ $video->is_pinned ? 'Featured' : 'Standard media' }}</div></div></a></div>
            @empty
                <div class="col-12 text-muted">No video notices yet. <a href="{{ route('admin.notices.create') }}">Upload the first video</a>.</div>
            @endforelse
        </div></div>
    </div>

    <!-- ===== QUICK SHORTCUTS HUB ===== -->
    <div class="card custom-card bg-light border">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3 text-muted text-uppercase small"><i class="bi bi-grid-fill me-2"></i>Quick Administrative Hub</h6>
            <div class="row g-2">
                <div class="col-lg-2 col-md-4 col-6">
                    <a href="{{ route('students.index') }}" class="btn btn-white w-100 p-3 text-start border shadow-sm h-100 d-flex flex-column justify-content-between">
                        <i class="bi bi-person-vcard text-primary fs-4 mb-2"></i>
                        <span class="fw-bold text-dark small">Student Directory</span>
                    </a>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <a href="{{ route('teachers.index') }}" class="btn btn-white w-100 p-3 text-start border shadow-sm h-100 d-flex flex-column justify-content-between">
                        <i class="bi bi-person-workspace text-success fs-4 mb-2"></i>
                        <span class="fw-bold text-dark small">Faculty Roster</span>
                    </a>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <a href="{{ route('exams.results') }}" class="btn btn-white w-100 p-3 text-start border shadow-sm h-100 d-flex flex-column justify-content-between">
                        <i class="bi bi-file-earmark-bar-graph text-warning fs-4 mb-2"></i>
                        <span class="fw-bold text-dark small">Academic Results</span>
                    </a>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <a href="{{ route('reports.attendance') }}" class="btn btn-white w-100 p-3 text-start border shadow-sm h-100 d-flex flex-column justify-content-between">
                        <i class="bi bi-calendar-range text-info fs-4 mb-2"></i>
                        <span class="fw-bold text-dark small">Attendance Reports</span>
                    </a>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <a href="{{ route('reports.fees') }}" class="btn btn-white w-100 p-3 text-start border shadow-sm h-100 d-flex flex-column justify-content-between">
                        <i class="bi bi-pie-chart text-danger fs-4 mb-2"></i>
                        <span class="fw-bold text-dark small">Fee Analytics</span>
                    </a>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <a href="{{ route('settings.index') }}" class="btn btn-white w-100 p-3 text-start border shadow-sm h-100 d-flex flex-column justify-content-between">
                        <i class="bi bi-gear text-secondary fs-4 mb-2"></i>
                        <span class="fw-bold text-dark small">School Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== MONTHLY CALENDAR ===== -->
    <div class="mt-8 mb-6">
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Weekly Attendance Bar Chart
    const attendanceCtx = document.getElementById('attendanceChart');
    if (attendanceCtx) {
        const attendanceRaw = @json($attendanceData);
        new Chart(attendanceCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: attendanceRaw.map(d => d.date),
                datasets: [
                    {
                        label: 'Present',
                        data: attendanceRaw.map(d => d.present),
                        backgroundColor: '#10b981',
                        borderRadius: 4,
                        maxBarThickness: 32
                    },
                    {
                        label: 'Absent',
                        data: attendanceRaw.map(d => d.absent),
                        backgroundColor: '#ef4444',
                        borderRadius: 4,
                        maxBarThickness: 32
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    // 2. Today's Attendance Doughnut
    const pieCtx = document.getElementById('todayAttendancePie');
    if (pieCtx) {
        const presentCount = {{ $stats['present_today'] }};
        const absentCount = {{ $stats['absent_today'] }};
        const totalStudents = {{ $stats['total_students'] }};
        const unrecorded = Math.max(0, totalStudents - presentCount - absentCount);

        new Chart(pieCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Absent', 'Unrecorded'],
                datasets: [{
                    data: [presentCount || 0, absentCount || 0, unrecorded || 0],
                    backgroundColor: ['#10b981', '#ef4444', '#e2e8f0'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '72%'
            }
        });
    }
});
</script>
@endpush
