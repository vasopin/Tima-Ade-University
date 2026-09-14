@extends('layouts.app')

@section('title', 'Executive Leadership Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Executive Leadership</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="welcome-banner mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <span class="badge bg-white text-dark rounded-pill px-3 py-2 mb-2"><i class="bi bi-building-check text-success me-1"></i> University-wide strategic overview</span>
                <h1 class="welcome-title mb-1">Welcome, {{ $user->name }}</h1>
                <p class="welcome-text mb-0">Institutional performance across academics, students, finance, admissions, and people.</p>
            </div>
            <div class="col-lg-4">
                <form method="GET" class="row g-2">
                    <div class="col-6">
                        <label class="visually-hidden" for="academic_year_id">Academic year</label>
                        <select class="form-select form-select-sm" id="academic_year_id" name="academic_year_id" onchange="this.form.submit()">
                            <option value="">Current academic year</option>
                            @foreach($years as $academicYear)
                                <option value="{{ $academicYear->id }}" @selected($year?->id === $academicYear->id)>{{ $academicYear->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="visually-hidden" for="term_id">Term</label>
                        <select class="form-select form-select-sm" id="term_id" name="term_id" onchange="this.form.submit()">
                            <option value="">All terms</option>
                            @foreach($terms as $academicTerm)
                                <option value="{{ $academicTerm->id }}" @selected($term?->id === $academicTerm->id)>{{ $academicTerm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6"><input class="form-control form-control-sm" type="date" name="from" value="{{ $from->toDateString() }}" aria-label="From date"></div>
                    <div class="col-6"><input class="form-control form-control-sm" type="date" name="to" value="{{ $to->toDateString() }}" aria-label="To date"></div>
                    <div class="col-12"><button class="btn btn-sm btn-light w-100" type="submit"><i class="bi bi-funnel me-1"></i> Apply reporting period</button></div>
                </form>
            </div>
        </div>

    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Students', $stats['total_students'], 'bi-people-fill', 'Active student records'],
            ['Active enrollments', $stats['active_enrollments'], 'bi-journal-check', 'Selected term'],
            ['Applicants', $stats['applicants'], 'bi-person-plus-fill', $stats['admissions_conversion'].'% conversion'],
            ['Faculty & staff', $stats['faculty_staff'], 'bi-person-workspace', $stats['employees'].' employees'],
            ['Active programs', $stats['active_programs'], 'bi-diagram-3-fill', $stats['active_courses'].' active courses'],
            ['Open sections', $stats['active_sections'], 'bi-collection-play-fill', 'Selected term'],
            ['Collected fees', '₦'.number_format($stats['collected'], 2), 'bi-cash-stack', 'Selected period'],
            ['Attendance rate', $stats['attendance_rate'].'%', 'bi-calendar-check-fill', 'Selected period'],
        ] as [$label, $value, $icon, $detail])
            <div class="col-6 col-xl-3"><div class="card custom-card h-100 border-0 shadow-sm"><div class="card-body d-flex justify-content-between gap-2"><div><div class="small text-muted">{{ $label }}</div><div class="h4 fw-bold mb-1">{{ $value }}</div><div class="small text-muted">{{ $detail }}</div></div><i class="bi {{ $icon }} fs-2 text-primary"></i></div></div></div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-8"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Academic footprint</h2></div><div class="card-body">
            <div class="row g-4">
                <div class="col-md-4"><h3 class="h6">By faculty</h3>@forelse($facultyEnrollment as $faculty)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $faculty->name }}</span><strong>{{ $faculty->enrollment_count }}</strong></div>@empty<p class="text-muted small">No faculty data.</p>@endforelse</div>
                <div class="col-md-4"><h3 class="h6">By department</h3>@forelse($departmentEnrollment as $department)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $department->name }}</span><strong>{{ $department->enrollment_count }}</strong></div>@empty<p class="text-muted small">No department data.</p>@endforelse</div>
                <div class="col-md-4"><h3 class="h6">By program</h3>@forelse($programEnrollment as $program)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $program->name }}</span><strong>{{ $program->enrollment_count }}</strong></div>@empty<p class="text-muted small">No program data.</p>@endforelse</div>
            </div>
        </div></div>
        <div class="col-xl-4"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Student success</h2></div><div class="card-body">
            <div class="d-flex justify-content-between mb-3"><span>Average mark</span><strong>{{ $stats['average_mark'] }}%</strong></div>
            <div class="d-flex justify-content-between mb-3"><span>Academic-risk students</span><strong class="text-danger">{{ $stats['at_risk'] }}</strong></div>
            <div class="d-flex justify-content-between mb-3"><span>Graduated</span><strong>{{ $stats['graduated'] }}</strong></div>
            <div class="d-flex justify-content-between mb-3"><span>Pending result approvals</span><strong>{{ $stats['pending_approvals'] }}</strong></div>
            <div class="d-flex justify-content-between"><span>Active advising assignments</span><strong>{{ $stats['advising_active'] }}</strong></div>
        </div></div></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-4"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Admissions</h2></div><div class="card-body"><div class="d-flex justify-content-between py-2 border-bottom"><span>Pending review</span><strong>{{ $admissions['pending'] }}</strong></div><div class="d-flex justify-content-between py-2 border-bottom"><span>Approved</span><strong class="text-success">{{ $admissions['approved'] }}</strong></div><div class="d-flex justify-content-between py-2"><span>Declined</span><strong class="text-danger">{{ $admissions['declined'] }}</strong></div></div></div></div>
        <div class="col-lg-4"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Financial position</h2></div><div class="card-body"><div class="d-flex justify-content-between py-2 border-bottom"><span>Collected</span><strong>₦{{ number_format($stats['collected'], 2) }}</strong></div><div class="d-flex justify-content-between py-2 border-bottom"><span>Outstanding</span><strong class="text-warning">₦{{ number_format($stats['outstanding'], 2) }}</strong></div><div class="d-flex justify-content-between py-2"><span>Active scholarships</span><strong>₦{{ number_format($stats['scholarships'], 2) }}</strong></div></div></div></div>
        <div class="col-lg-4"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">HR overview</h2></div><div class="card-body"><div class="d-flex justify-content-between py-2 border-bottom"><span>Employees</span><strong>{{ $stats['employees'] }}</strong></div><div class="d-flex justify-content-between py-2"><span>Pending leave requests</span><strong>{{ $stats['pending_leave'] }}</strong></div></div></div></div>
    </div>

    <div class="card custom-card"><div class="card-header bg-white"><h2 class="h5 mb-0">Recent institutional activity</h2><p class="small text-muted mb-0">Live-class activity currently available in the audit infrastructure.</p></div><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>When</th><th>Actor</th><th>Action</th><th>Context</th></tr></thead><tbody>@forelse($recentActivity as $activity)<tr><td>{{ $activity->created_at?->format('M j, Y g:i A') }}</td><td>{{ $activity->actor?->name ?? 'System' }}</td><td>{{ str_replace('_', ' ', $activity->action) }}</td><td>{{ $activity->liveClass?->title ?? 'Live class activity' }}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-4">No recent activity is available.</td></tr>@endforelse</tbody></table></div></div>
</div>
@endsection
