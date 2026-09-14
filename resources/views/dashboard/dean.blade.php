@extends('layouts.app')

@section('title', 'Dean Faculty Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">{{ $faculty->name }} Faculty</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="welcome-banner mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-7">
                <span class="badge bg-white text-dark rounded-pill px-3 py-2 mb-2"><i class="bi bi-mortarboard-fill text-primary me-1"></i> Faculty leadership</span>
                <h1 class="welcome-title mb-1">{{ $faculty->name }} Faculty</h1>
                <p class="welcome-text mb-0">Academic performance, student success, and teaching activity within your assigned faculty.</p>
            </div>
            <div class="col-lg-5">
                <form method="GET" class="row g-2">
                    <div class="col-6"><label class="visually-hidden" for="academic_year_id">Academic year</label><select class="form-select form-select-sm" id="academic_year_id" name="academic_year_id" onchange="this.form.submit()"><option value="">Current academic year</option>@foreach($years as $academicYear)<option value="{{ $academicYear->id }}" @selected($year?->id === $academicYear->id)>{{ $academicYear->name }}</option>@endforeach</select></div>
                    <div class="col-6"><label class="visually-hidden" for="term_id">Term</label><select class="form-select form-select-sm" id="term_id" name="term_id" onchange="this.form.submit()"><option value="">All terms</option>@foreach($terms as $academicTerm)<option value="{{ $academicTerm->id }}" @selected($term?->id === $academicTerm->id)>{{ $academicTerm->name }}</option>@endforeach</select></div>
                    <div class="col-6"><select class="form-select form-select-sm" name="department_id" aria-label="Department"><option value="">All departments</option>@foreach($departments as $item)<option value="{{ $item->id }}" @selected($department?->id === $item->id)>{{ $item->name }}</option>@endforeach</select></div>
                    <div class="col-6"><select class="form-select form-select-sm" name="program_id" aria-label="Program"><option value="">All programs</option>@foreach($programs as $item)<option value="{{ $item->id }}" @selected($program?->id === $item->id)>{{ $item->name }}</option>@endforeach</select></div>
                    <div class="col-12"><button class="btn btn-sm btn-light w-100" type="submit"><i class="bi bi-funnel me-1"></i> Apply faculty filters</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Students', $stats['students'], 'bi-people-fill', 'Active faculty students'],
            ['Active enrollments', $stats['active_enrollments'], 'bi-journal-check', 'Selected term'],
            ['Departments', $stats['departments'], 'bi-diagram-3', 'Active departments'],
            ['Programs', $stats['programs'], 'bi-mortarboard', 'Active programs'],
            ['Courses', $stats['courses'], 'bi-book', 'Active catalogue courses'],
            ['Open sections', $stats['sections'], 'bi-collection-play', 'Selected term'],
            ['Teaching staff', $stats['teaching_staff'], 'bi-person-workspace', 'Assigned sections'],
            ['Attendance', $stats['attendance_rate'].'%', 'bi-calendar-check', 'Present or late']
        ] as [$label, $value, $icon, $detail])
            <div class="col-6 col-xl-3"><div class="card custom-card h-100 border-0 shadow-sm"><div class="card-body d-flex justify-content-between gap-2"><div><div class="small text-muted">{{ $label }}</div><div class="h4 fw-bold mb-1">{{ $value }}</div><div class="small text-muted">{{ $detail }}</div></div><i class="bi {{ $icon }} fs-2 text-primary"></i></div></div></div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-8"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Enrollment footprint</h2></div><div class="card-body"><div class="row g-4"><div class="col-md-6"><h3 class="h6">By department</h3>@forelse($departmentEnrollment as $item)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $item->name }}</span><strong>{{ $item->student_count }}</strong></div>@empty<p class="text-muted small">No department enrollment data.</p>@endforelse</div><div class="col-md-6"><h3 class="h6">By program</h3>@forelse($programEnrollment as $item)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $item->name }}</span><strong>{{ $item->student_count }}</strong></div>@empty<p class="text-muted small">No program enrollment data.</p>@endforelse</div></div></div></div>
        <div class="col-xl-4"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Academic standing</h2></div><div class="card-body">@forelse($standing as $status => $total)<div class="d-flex justify-content-between py-2 border-bottom"><span>{{ str_replace('_', ' ', ucfirst($status)) }}</span><strong>{{ $total }}</strong></div>@empty<p class="text-muted small">No standing records available.</p>@endforelse<div class="d-flex justify-content-between py-2"><span>Risk indicators</span><strong class="text-danger">{{ $stats['at_risk'] + $stats['probation'] }}</strong></div></div></div></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-4"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Performance</h2></div><div class="card-body"><div class="d-flex justify-content-between py-2 border-bottom"><span>Average mark</span><strong>{{ $stats['average_mark'] }}%</strong></div><div class="d-flex justify-content-between py-2 border-bottom"><span>Pass rate</span><strong class="text-success">{{ $stats['pass_rate'] }}%</strong></div><div class="d-flex justify-content-between py-2 border-bottom"><span>Failed-student indicators</span><strong class="text-danger">{{ $stats['at_risk'] }}</strong></div><div class="d-flex justify-content-between py-2"><span>Graduated</span><strong>{{ $stats['graduated'] }}</strong></div></div></div></div>
        <div class="col-lg-4"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Advising & progression</h2></div><div class="card-body"><div class="d-flex justify-content-between py-2 border-bottom"><span>Active assignments</span><strong>{{ $stats['advising_active'] }}</strong></div><div class="d-flex justify-content-between py-2 border-bottom"><span>Open appointments</span><strong>{{ $stats['advising_appointments'] }}</strong></div><div class="d-flex justify-content-between py-2"><span>Students on probation</span><strong class="text-warning">{{ $stats['probation'] }}</strong></div></div></div></div>
        <div class="col-lg-4"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Admissions & finance</h2></div><div class="card-body"><p class="text-muted small mb-0">Faculty-linked admissions and fee authorization are not present in the current data model. No institution-wide admissions or financial figures are shown in this faculty-scoped view.</p></div></div></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-7"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Teaching staff and section activity</h2></div><div class="card-body"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Teacher</th><th>Sections</th><th>Enrollments</th></tr></thead><tbody>@forelse($teacherOverview as $item)<tr><td>{{ $item['teacher']?->name ?? 'Unassigned' }}</td><td>{{ $item['sections'] }}</td><td>{{ $item['enrollments'] }}</td></tr>@empty<tr><td colspan="3" class="text-muted text-center py-4">No teaching assignments for this filter.</td></tr>@endforelse</tbody></table></div></div></div></div>
        <div class="col-xl-5"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Timetable overview</h2></div><div class="card-body">@forelse($timetable as $entry)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $entry->subject?->name ?? 'Course' }}<small class="d-block text-muted">{{ $entry->day_of_week }} · {{ $entry->room_number ?? 'Room TBA' }}</small></span><strong>{{ $entry->teacher?->user?->name ?? 'Unassigned' }}</strong></div>@empty<p class="text-muted small">No timetable entries available.</p>@endforelse</div></div></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-5"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Department oversight</h2></div><div class="card-body"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Department</th><th>Head</th><th>Programs</th><th>Courses</th></tr></thead><tbody>@forelse($departmentOverview as $item)<tr><td><strong>{{ $item->name }}</strong><small class="d-block text-muted">{{ $item->code }}</small></td><td>{{ $item->head?->name ?? 'Not assigned' }}</td><td>{{ $item->programs_count }}</td><td>{{ $item->subjects_count }}</td></tr>@empty<tr><td colspan="4" class="text-muted text-center py-4">No departments in this faculty.</td></tr>@endforelse</tbody></table></div></div></div></div>
        <div class="col-xl-7"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Course and section oversight</h2></div><div class="card-body"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Course</th><th>Department</th><th>Sections</th><th>Recent section activity</th></tr></thead><tbody>@forelse($courseOverview as $item)<tr><td><strong>{{ $item->name }}</strong><small class="d-block text-muted">{{ $item->code }}</small></td><td>{{ $item->department?->name }}</td><td>{{ $item->course_sections_count }}</td><td>{{ $sectionOverview->where('course_id', $item->id)->count() }}</td></tr>@empty<tr><td colspan="4" class="text-muted text-center py-4">No courses in this faculty.</td></tr>@endforelse</tbody></table></div></div></div></div>
    </div>

    <div class="card custom-card mb-4"><div class="card-header bg-white"><h2 class="h5 mb-0">Faculty students</h2><p class="small text-muted mb-0">Recent active students matching the selected faculty filters.</p></div><div class="card-body"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Student</th><th>Student ID</th><th>Program</th><th>Department</th><th>Status</th></tr></thead><tbody>@forelse($studentOverview as $item)<tr><td>{{ $item->user?->name ?? 'Unknown student' }}</td><td>{{ $item->student_id ?? $item->admission_number }}</td><td>{{ $item->program?->name ?? 'Not assigned' }}</td><td>{{ $item->program?->department?->name ?? 'Not assigned' }}</td><td><span class="badge text-bg-success">{{ ucfirst($item->status) }}</span></td></tr>@empty<tr><td colspan="5" class="text-muted text-center py-4">No active students match the selected filters.</td></tr>@endforelse</tbody></table></div></div></div>
</div>
@endsection
