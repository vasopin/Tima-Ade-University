@extends('layouts.app')

@section('title', 'Department Head Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ $department->name }} Department</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="welcome-banner mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-7">
                <span class="badge bg-white text-dark rounded-pill px-3 py-2 mb-2"><i class="bi bi-diagram-3-fill text-primary me-1"></i> Department leadership</span>
                <h1 class="welcome-title mb-1">{{ $department->name }} Department</h1>
                <p class="welcome-text mb-0">Student success, teaching activity, and academic performance for your department.</p>
            </div>
            <div class="col-lg-5">
                <form method="GET" class="row g-2">
                    <div class="col-6"><label class="visually-hidden" for="academic_year_id">Academic year</label><select class="form-select form-select-sm" id="academic_year_id" name="academic_year_id"><option value="">Current academic year</option>@foreach($years as $item)<option value="{{ $item->id }}" @selected($year?->id === $item->id)>{{ $item->name }}</option>@endforeach</select></div>
                    <div class="col-6"><label class="visually-hidden" for="term_id">Term</label><select class="form-select form-select-sm" id="term_id" name="term_id"><option value="">All terms</option>@foreach($terms as $item)<option value="{{ $item->id }}" @selected($term?->id === $item->id)>{{ $item->name }}</option>@endforeach</select></div>
                    <div class="col-8"><label class="visually-hidden" for="program_id">Program</label><select class="form-select form-select-sm" id="program_id" name="program_id"><option value="">All programs</option>@foreach($programEnrollment as $item)<option value="{{ $item->id }}" @selected($program?->id === $item->id)>{{ $item->name }}</option>@endforeach</select></div>
                    <div class="col-4"><button class="btn btn-sm btn-light w-100" type="submit"><i class="bi bi-funnel me-1"></i> Filter</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Students', $stats['students'], 'bi-people-fill', 'Active department students'],
            ['Active enrollments', $stats['active_enrollments'], 'bi-journal-check', 'Selected term'],
            ['Programs', $stats['programs'], 'bi-mortarboard', 'Active programs'],
            ['Courses', $stats['courses'], 'bi-book', 'Active catalogue courses'],
            ['Open sections', $stats['sections'], 'bi-collection-play', 'Selected term'],
            ['Teachers', $stats['teachers'], 'bi-person-workspace', 'Assigned sections'],
            ['Attendance', $stats['attendance_rate'].'%', 'bi-calendar-check', 'Present or late'],
            ['At risk', $stats['at_risk'] + $stats['probation'], 'bi-exclamation-triangle', 'Needs attention']
        ] as [$label, $value, $icon, $detail])
            <div class="col-6 col-xl-3"><div class="card custom-card h-100 border-0 shadow-sm"><div class="card-body d-flex justify-content-between gap-2"><div><div class="small text-muted">{{ $label }}</div><div class="h4 fw-bold mb-1">{{ $value }}</div><div class="small text-muted">{{ $detail }}</div></div><i class="bi {{ $icon }} fs-2 text-primary"></i></div></div></div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-6"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Students by program</h2></div><div class="card-body">@forelse($programEnrollment as $item)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $item->name }}</span><strong>{{ $item->student_count }}</strong></div>@empty<p class="text-muted small">No enrollment data for this filter.</p>@endforelse</div></div></div>
        <div class="col-xl-3"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Academic standing</h2></div><div class="card-body">@forelse($standing as $status => $total)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ str_replace('_', ' ', ucfirst($status)) }}</span><strong>{{ $total }}</strong></div>@empty<p class="text-muted small">No standing records.</p>@endforelse</div></div></div>
        <div class="col-xl-3"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Advising</h2></div><div class="card-body"><div class="d-flex justify-content-between border-bottom py-2"><span>Active assignments</span><strong>{{ $stats['advising_active'] }}</strong></div><div class="d-flex justify-content-between py-2"><span>Open appointments</span><strong>{{ $stats['advising_appointments'] }}</strong></div></div></div></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-5"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Course performance</h2></div><div class="card-body"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Course</th><th>Average</th><th>Pass</th></tr></thead><tbody>@forelse($coursePerformance as $item)<tr><td>{{ $item['course']->name }}</td><td>{{ $item['average'] }}%</td><td class="text-success">{{ $item['pass_rate'] }}%</td></tr>@empty<tr><td colspan="3" class="text-muted text-center py-4">No examination results for this period.</td></tr>@endforelse</tbody></table></div></div></div></div>
        <div class="col-xl-4"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Teacher workload</h2></div><div class="card-body"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Teacher</th><th>Sections</th><th>Enrollments</th></tr></thead><tbody>@forelse($teacherOverview as $item)<tr><td>{{ $item['teacher']?->name ?? 'Unassigned' }}</td><td>{{ $item['sections'] }}</td><td>{{ $item['enrollments'] }}</td></tr>@empty<tr><td colspan="3" class="text-muted text-center py-4">No teaching assignments.</td></tr>@endforelse</tbody></table></div></div></div></div>
        <div class="col-xl-3"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Results summary</h2></div><div class="card-body"><div class="d-flex justify-content-between border-bottom py-2"><span>Average mark</span><strong>{{ $stats['average_mark'] }}%</strong></div><div class="d-flex justify-content-between py-2"><span>Pass rate</span><strong class="text-success">{{ $stats['pass_rate'] }}%</strong></div></div></div></div>
    </div>

    <div class="row g-3">
        <div class="col-xl-7"><div class="card custom-card"><div class="card-header bg-white"><h2 class="h5 mb-0">Timetable and active sections</h2></div><div class="card-body">@forelse($timetable as $entry)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $entry->subject?->name ?? 'Course' }}<small class="d-block text-muted">{{ $entry->day_of_week }} · {{ $entry->room_number ?? 'Room TBA' }}</small></span><strong>{{ $entry->teacher?->user?->name ?? 'Unassigned' }}</strong></div>@empty<p class="text-muted small mb-0">No timetable entries available.</p>@endforelse</div></div></div>
        <div class="col-xl-5"><div class="card custom-card"><div class="card-header bg-white"><h2 class="h5 mb-0">Department reports</h2></div><div class="card-body d-flex flex-wrap gap-2"><a class="btn btn-outline-primary btn-sm" href="{{ route('department-head.reports') }}">Open read-only reports</a><a class="btn btn-outline-primary btn-sm" href="{{ route('department-head.analytics') }}">View analytics</a></div></div></div>
    </div>
</div>
@endsection
