@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h1 class="h3 mb-1">{{ $student->user->name }}</h1><div class="text-muted">{{ $student->student_id }} · {{ $student->program?->name ?? 'No programme' }}</div></div>
        <a href="{{ route('students.student-360.index') }}" class="btn btn-outline-secondary">Back to Student 360</a>
    </div>
    <div class="row g-3 mb-4">
        @foreach ([['Status', ucfirst($student->lifecycle_status ?: $student->status)], ['Standing', is_array($standing) ? ($standing['standing'] ?? 'Not calculated') : ($standing?->standing ?? 'Not calculated')], ['Attendance', $attendanceRate.'%'], ['Active holds', $student->holds->where('status', 'active')->count()]] as $metric)
            <div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body"><div class="text-muted small">{{ $metric[0] }}</div><div class="h4 mb-0">{{ $metric[1] }}</div></div></div></div>
        @endforeach
    </div>
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card mb-4"><div class="card-header">Academic record</div><div class="card-body">
                <p class="mb-1"><strong>Faculty:</strong> {{ $student->program?->department?->faculty?->name ?? '—' }}</p>
                <p class="mb-1"><strong>Department:</strong> {{ $student->program?->department?->name ?? '—' }}</p>
                <p class="mb-3"><strong>Programme:</strong> {{ $student->program?->name ?? '—' }}</p>
                <h6>Current and historical courses</h6><ul class="mb-0">@forelse($student->enrollments as $enrollment)<li>{{ $enrollment->courseSection?->course?->name ?? $enrollment->courseSection?->course?->code ?? 'Course' }} — {{ ucfirst($enrollment->status) }}</li>@empty<li>No enrollment records.</li>@endforelse</ul>
            </div></div>
            <div class="card"><div class="card-header">Lifecycle and programme history</div><div class="card-body">
                @foreach($student->statusHistories->sortByDesc('effective_date') as $history)<div class="border-bottom py-2">{{ ucfirst($history->to_status) }} <span class="text-muted">effective {{ $history->effective_date?->format('Y-m-d') }}</span><div class="small">{{ $history->reason }}</div></div>@endforeach
                @foreach($student->programHistories->sortByDesc('effective_date') as $history)<div class="border-bottom py-2">Programme changed to {{ $history->toProgram?->name }} <span class="text-muted">effective {{ $history->effective_date?->format('Y-m-d') }}</span></div>@endforeach
            </div></div>
        </div>
        <div class="col-lg-5">
            <div class="card mb-4"><div class="card-header">Active holds</div><div class="card-body">@forelse($student->holds->where('status', 'active') as $hold)<div class="d-flex justify-content-between border-bottom py-2"><span><strong>{{ ucfirst($hold->type) }}</strong><br><small>{{ $hold->reason }}</small></span>@if(auth()->user()->isAdmin() || auth()->user()->isRegistrar())<form method="POST" action="{{ route('students.holds.release', $hold) }}">@csrf<button class="btn btn-sm btn-outline-danger">Release</button></form>@endif</div>@empty<p class="mb-0 text-muted">No active holds.</p>@endforelse</div></div>
            <div class="card"><div class="card-header">Advising</div><div class="card-body"><p class="mb-1">Assignments: {{ $student->advisorAssignments->where('is_active', true)->count() }}</p><p class="mb-0">Appointments: {{ $student->advisingAppointments->count() }}</p></div></div>
        </div>
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isRegistrar())
    <div class="row g-4 mt-1">
        <div class="col-lg-4"><div class="card"><div class="card-header">Update lifecycle status</div><div class="card-body">
            <form method="POST" action="{{ route('students.lifecycle-status', $student) }}">@csrf
                <select name="status" class="form-select mb-2" required><option value="">Select status</option>@foreach(['active','leave','suspended','withdrawn','graduated','dismissed'] as $status)<option value="{{ $status }}">{{ ucfirst($status) }}</option>@endforeach</select>
                <input type="date" name="effective_date" class="form-control mb-2" value="{{ now()->toDateString() }}" required>
                <textarea name="reason" class="form-control mb-2" placeholder="Reason" required></textarea>
                <button class="btn btn-primary w-100">Save status</button>
            </form>
        </div></div></div>
        <div class="col-lg-4"><div class="card"><div class="card-header">Place hold</div><div class="card-body">
            <form method="POST" action="{{ route('students.holds.store', $student) }}">@csrf
                <select name="type" class="form-select mb-2" required>@foreach(['registration','academic','finance','disciplinary','library','administrative'] as $type)<option value="{{ $type }}">{{ ucfirst($type) }}</option>@endforeach</select>
                <textarea name="reason" class="form-control mb-2" placeholder="Reason" required></textarea>
                <button class="btn btn-warning w-100">Place hold</button>
            </form>
        </div></div></div>
        <div class="col-lg-4"><div class="card"><div class="card-header">Request programme transfer</div><div class="card-body">
            <form method="POST" action="{{ route('students.transfers.store', $student) }}">@csrf
                <select name="to_program_id" class="form-select mb-2" required><option value="">Select programme</option>@foreach($programs as $program)<option value="{{ $program->id }}" @selected($program->id === $student->program_id)>{{ $program->name }}</option>@endforeach</select>
                <textarea name="reason" class="form-control mb-2" placeholder="Reason" required></textarea>
                <button class="btn btn-outline-primary w-100">Submit transfer</button>
            </form>
        </div></div></div>
    </div>
    @endif
</div>
@endsection
