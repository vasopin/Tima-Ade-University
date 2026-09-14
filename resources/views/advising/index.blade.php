@extends('layouts.app')

@section('title', 'Academic Advising')

@section('content')
<div class="container-fluid px-4 py-4 bg-light min-vh-100">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-uppercase text-muted small fw-semibold mb-1">Student success</p>
            <h1 class="h2 mb-1">Academic Advising</h1>
            <p class="text-muted mb-0">Coordinate academic guidance, appointments, and follow-up notes.</p>
        </div>
    </div>

    @if(auth()->user()->isStudent())
        <div class="row g-4">
            <div class="col-12 col-xl-4">
                <section class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5">Your advisor</h2>
                        @if($assignment)
                            <p class="mb-1 fw-semibold">{{ $assignment->advisor->name }}</p>
                            <p class="text-muted small mb-0">{{ $assignment->advisor->email }}</p>
                        @else
                            <p class="text-muted mb-0">No academic advisor has been assigned yet.</p>
                        @endif
                    </div>
                </section>
                <section class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h5">Request an appointment</h2>
                        <form method="POST" action="{{ route('advising.appointments.store') }}" class="row g-3">
                            @csrf
                            <div class="col-12"><label class="form-label" for="scheduled_at">Preferred date and time</label><input id="scheduled_at" name="scheduled_at" type="datetime-local" class="form-control" required></div>
                            <div class="col-6"><label class="form-label" for="duration_minutes">Duration</label><select id="duration_minutes" name="duration_minutes" class="form-select"><option value="30">30 minutes</option><option value="60">60 minutes</option></select></div>
                            <div class="col-6"><label class="form-label" for="mode">Mode</label><select id="mode" name="mode" class="form-select"><option value="in_person">In person</option><option value="online">Online</option><option value="phone">Phone</option></select></div>
                            <div class="col-12"><label class="form-label" for="topic">Topic</label><input id="topic" name="topic" class="form-control" maxlength="255" required></div>
                            <div class="col-12"><label class="form-label" for="notes">Context</label><textarea id="notes" name="notes" class="form-control" rows="3" maxlength="5000"></textarea></div>
                            <div class="col-12"><button class="btn btn-primary">Request appointment</button></div>
                        </form>
                    </div>
                </section>
            </div>
            <div class="col-12 col-xl-8">
                <section class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white"><h2 class="h5 mb-0">Appointments</h2></div>
                    <div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Date</th><th>Advisor</th><th>Topic</th><th>Status</th></tr></thead><tbody>
                    @forelse($appointments as $appointment)<tr><td>{{ $appointment->scheduled_at->format('M j, Y g:i A') }}</td><td>{{ $appointment->advisor?->name ?? 'To be assigned' }}</td><td>{{ $appointment->topic }}</td><td><span class="badge text-bg-{{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'cancelled' ? 'secondary' : 'warning') }}">{{ ucfirst($appointment->status) }}</span></td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-4">No advising appointments yet.</td></tr>@endforelse
                    </tbody></table></div>
                </section>
                <section class="card border-0 shadow-sm">
                    <div class="card-header bg-white"><h2 class="h5 mb-0">Advising notes</h2></div>
                    <div class="card-body">@forelse($notes as $note)<article class="border-bottom pb-3 mb-3"><div class="d-flex justify-content-between"><strong>{{ $note->advisor->name }}</strong><small class="text-muted">{{ $note->created_at->format('M j, Y') }}</small></div><p class="mb-0 mt-2">{{ $note->body }}</p></article>@empty<p class="text-muted mb-0">No shared advising notes yet.</p>@endforelse</div>
                </section>
            </div>
        </div>
    @else
        <div class="row g-4">
            <div class="col-12 col-xl-5">
                <section class="card border-0 shadow-sm mb-4"><div class="card-body"><h2 class="h5">Assign an advisor</h2><form method="POST" action="{{ route('advising.assignments.store') }}" class="row g-3">@csrf
                    <div class="col-12"><label class="form-label" for="student_id">Student</label><select id="student_id" name="student_id" class="form-select" required><option value="">Select student</option>@foreach($students as $student)<option value="{{ $student->id }}">{{ $student->student_id }} — {{ $student->user->name }}</option>@endforeach</select></div>
                    <div class="col-12"><label class="form-label" for="advisor_id">Advisor</label><select id="advisor_id" name="advisor_id" class="form-select" required><option value="">Select advisor</option>@foreach($advisors as $advisor)<option value="{{ $advisor->id }}">{{ $advisor->name }} ({{ $advisor->role->name ?? 'Staff' }})</option>@endforeach</select></div>
                    <div class="col-12"><textarea name="notes" class="form-control" rows="2" placeholder="Assignment context (optional)"></textarea></div>
                    <div class="col-12"><button class="btn btn-primary">Assign advisor</button></div>
                </form></div></section>
                <section class="card border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h5 mb-0">Active assignments</h2></div><div class="list-group list-group-flush">@forelse($assignments as $assignment)<div class="list-group-item"><div class="fw-semibold">{{ $assignment->student->user->name }}</div><small class="text-muted">{{ $assignment->advisor->name }} · Since {{ $assignment->assigned_at->format('M j, Y') }}</small></div>@empty<div class="list-group-item text-muted">No active assignments.</div>@endforelse</div></section>
            </div>
            <div class="col-12 col-xl-7">
                <section class="card border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h5 mb-0">Upcoming appointments</h2></div><div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Student</th><th>Date</th><th>Topic</th><th>Action</th></tr></thead><tbody>
                @forelse($appointments as $appointment)<tr><td>{{ $appointment->student->user->name }}</td><td>{{ $appointment->scheduled_at->format('M j, Y g:i A') }}</td><td>{{ $appointment->topic }}</td><td><form method="POST" action="{{ route('advising.appointments.update', $appointment) }}" class="d-flex gap-2">@csrf @method('PATCH')<select name="status" class="form-select form-select-sm"><option value="confirmed">Confirm</option><option value="completed">Complete</option><option value="cancelled">Cancel</option></select><button class="btn btn-sm btn-outline-primary">Save</button></form></td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-4">No pending appointments.</td></tr>@endforelse
                </tbody></table></div></section>
                @if(auth()->user()->isTeacher() || auth()->user()->isStaff() || auth()->user()->isAdmin() || auth()->user()->isRegistrar())
                    <section class="card border-0 shadow-sm mt-4"><div class="card-body"><h2 class="h5">Record an advising note</h2><form method="POST" action="{{ route('advising.notes.store') }}" class="row g-3">@csrf<div class="col-12"><select name="student_id" class="form-select" required><option value="">Select student</option>@foreach($students as $student)<option value="{{ $student->id }}">{{ $student->student_id }} — {{ $student->user->name }}</option>@endforeach</select></div><div class="col-12"><textarea name="body" class="form-control" rows="4" placeholder="Discussion summary and agreed actions" required></textarea></div><div class="col-md-6"><label class="form-label">Follow-up date</label><input type="date" name="follow_up_at" class="form-control"></div><div class="col-md-6 form-check mt-5 ms-2"><input type="checkbox" name="is_private" value="1" class="form-check-input" id="is_private"><label class="form-check-label" for="is_private">Private staff note</label></div><div class="col-12"><button class="btn btn-primary">Save note</button></div></form></div></section>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
