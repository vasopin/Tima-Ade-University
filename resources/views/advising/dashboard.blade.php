@extends('layouts.app')

@section('title', 'Advisor Dashboard')

@section('content')
<div class="container-fluid px-0">
    <div class="welcome-banner mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-7">
                <span class="badge bg-white text-dark rounded-pill px-3 py-2 mb-2"><i class="bi bi-person-check-fill text-primary me-1"></i> Academic advising workspace</span>
                <h1 class="welcome-title mb-1">Advisor Dashboard</h1>
                <p class="welcome-text mb-0">Manage your assigned advisees, appointments, follow-ups, and curriculum progress.</p>
            </div>
            <div class="col-lg-5">
                <form method="GET" class="row g-2">
                    <div class="col-6"><label class="visually-hidden" for="academic_year_id">Academic year</label><select class="form-select form-select-sm" id="academic_year_id" name="academic_year_id"><option value="">Current academic year</option>@foreach($years as $item)<option value="{{ $item->id }}" @selected($year?->id === $item->id)>{{ $item->name }}</option>@endforeach</select></div>
                    <div class="col-6"><label class="visually-hidden" for="term_id">Term</label><select class="form-select form-select-sm" id="term_id" name="term_id"><option value="">All terms</option>@foreach($terms as $item)<option value="{{ $item->id }}" @selected($term?->id === $item->id)>{{ $item->name }}</option>@endforeach</select></div>
                    <div class="col-8"><label class="visually-hidden" for="program_id">Program</label><select class="form-select form-select-sm" id="program_id" name="program_id"><option value="">All programs</option>@foreach($programs as $item)<option value="{{ $item->id }}" @selected($program?->id === $item->id)>{{ $item->name }}</option>@endforeach</select></div>
                    <div class="col-4"><button class="btn btn-sm btn-light w-100" type="submit"><i class="bi bi-funnel me-1"></i> Filter</button></div>
                    <div class="col-12"><input class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Search student name or ID"></div>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([['Assigned students', $stats['assigned'], 'bi-people-fill'], ['Upcoming appointments', $stats['upcoming'], 'bi-calendar-event'], ['Pending follow-ups', $stats['pending_followups'], 'bi-flag-fill'], ['Needs attention', $stats['attention'], 'bi-exclamation-triangle'], ['Recent notes', $stats['recent_activity'], 'bi-journal-text']] as [$label, $value, $icon])
            <div class="col-6 col-xl"><div class="card custom-card h-100 border-0 shadow-sm"><div class="card-body d-flex justify-content-between"><div><div class="small text-muted">{{ $label }}</div><div class="h4 fw-bold mb-0">{{ $value }}</div></div><i class="bi {{ $icon }} fs-2 text-primary"></i></div></div></div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-8"><div class="card custom-card"><div class="card-header bg-white"><h2 class="h5 mb-0">My advisees</h2></div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Student</th><th>Program</th><th>Standing</th><th>Progress</th><th>Attendance</th><th>Courses</th></tr></thead><tbody>
            @forelse($students as $student)<tr><td><strong>{{ $student->user?->name }}</strong><small class="d-block text-muted">{{ $student->student_id }}</small></td><td>{{ $student->program?->name ?? 'Unassigned' }}</td><td><span class="badge text-bg-{{ in_array($student->academicStanding?->status, ['probation','suspended','dismissed'], true) ? 'danger' : 'success' }}">{{ ucfirst($student->academicStanding?->status ?? 'Not assessed') }}</span></td><td>{{ $progress[$student->id]['completed'] }}/{{ $progress[$student->id]['required'] }}<small class="d-block text-muted">{{ $progress[$student->id]['percent'] }}%</small></td><td>{{ $attendanceRates[$student->id] ?? 'N/A' }}{{ isset($attendanceRates[$student->id]) ? '%' : '' }}</td><td>{{ $student->enrollments->whereIn('status', ['enrolled','completed'])->count() }}</td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-4">No assigned advisees match this filter.</td></tr>@endforelse
        </tbody></table></div></div></div>
        <div class="col-xl-4"><div class="card custom-card h-100"><div class="card-header bg-white"><h2 class="h5 mb-0">Follow-up queue</h2></div><div class="card-body">@forelse($followUps as $note)<div class="border-bottom pb-2 mb-2"><strong>{{ $note->student?->user?->name }}</strong><small class="d-block text-{{ $note->follow_up_at->isPast() ? 'danger' : 'muted' }}">{{ $note->follow_up_at->format('M j, Y') }}</small><span class="small">{{ \Illuminate\Support\Str::limit($note->body, 90) }}</span></div>@empty<p class="text-muted small mb-0">No follow-ups due in the next 14 days.</p>@endforelse</div></div></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-6"><div class="card custom-card"><div class="card-header bg-white"><h2 class="h5 mb-0">Appointments</h2></div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Student</th><th>Date</th><th>Status</th><th></th></tr></thead><tbody>@forelse($appointments->take(10) as $appointment)<tr><td>{{ $appointment->student?->user?->name }}</td><td>{{ $appointment->scheduled_at->format('M j, g:i A') }}</td><td><span class="badge text-bg-{{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'cancelled' ? 'secondary' : 'warning') }}">{{ ucfirst($appointment->status) }}</span></td><td>@if(in_array($appointment->status, ['requested','confirmed'], true))<form method="POST" action="{{ route('advising.appointments.update', $appointment) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="cancelled"><button class="btn btn-sm btn-outline-secondary">Cancel</button></form>@endif</td></tr>@empty<tr><td colspan="4" class="text-muted text-center py-4">No appointments in scope.</td></tr>@endforelse</tbody></table></div></div></div>
        <div class="col-xl-6"><div class="card custom-card"><div class="card-header bg-white"><h2 class="h5 mb-0">Prerequisite-aware recommendations</h2></div><div class="card-body">@forelse($students as $student) @if($recommendations[$student->id]->isNotEmpty())<div class="border-bottom pb-2 mb-2"><strong>{{ $student->user?->name }}</strong><small class="d-block text-muted">{{ $recommendations[$student->id]->pluck('name')->join(', ') }}</small></div>@endif @empty<p class="text-muted small mb-0">No recommendations available.</p>@endforelse</div></div></div>
    </div>

    <div class="card custom-card mb-4"><div class="card-header bg-white"><h2 class="h5 mb-0">Recent advising history</h2></div><div class="card-body">@forelse($recentNotes as $note)<article class="border-bottom pb-2 mb-2"><div class="d-flex justify-content-between"><strong>{{ $note->student?->user?->name }}</strong><small class="text-muted">{{ $note->created_at->format('M j, Y') }}{{ $note->is_private ? ' · Private' : ' · Shared' }}</small></div><p class="small mb-0 mt-1">{{ $note->body }}</p></article>@empty<p class="text-muted small mb-0">No recent advising notes.</p>@endforelse</div></div>

    <div class="card custom-card"><div class="card-header bg-white d-flex justify-content-between align-items-center"><h2 class="h5 mb-0">Advisor actions</h2><a class="btn btn-sm btn-outline-primary" href="{{ route('advising.index') }}">Open advising management</a></div><div class="card-body"><p class="text-muted small mb-0">Use the management workspace for shared advising assignments. This dashboard only exposes students actively assigned to you; private notes remain visible only to their author.</p></div></div>
</div>
@endsection
