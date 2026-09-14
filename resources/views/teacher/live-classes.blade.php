@extends('layouts.app')

@section('title', 'Live Classes')

@push('styles')
<style>
    .live-page-shell { --live-ink: #102a43; --live-muted: #627d98; --live-teal: #087f8c; --live-coral: #e05d44; --live-line: #d9e2ec; background: linear-gradient(135deg, #f7fbfc 0%, #eef5f7 52%, #fff8f3 100%); border: 1px solid #e1ebef; border-radius: 18px; padding: clamp(1rem, 2vw, 2rem); }
    .live-page-header { align-items: flex-end !important; border-bottom: 1px solid rgba(16, 42, 67, .12); padding-bottom: 1.25rem; }
    .live-page-kicker { color: var(--live-teal); font-size: .72rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
    .live-page-header h2 { color: var(--live-ink); font-size: clamp(1.55rem, 2.4vw, 2.1rem); font-weight: 800; letter-spacing: 0; }
    .live-page-header p { color: var(--live-muted) !important; }
    .live-create-button { background: var(--live-ink); border-color: var(--live-ink); border-radius: 9px; box-shadow: 0 8px 18px rgba(16, 42, 67, .16); color: #fff; font-weight: 700; }
    .live-create-button:hover, .live-create-button:focus-visible { background: var(--live-teal); border-color: var(--live-teal); }
    .live-form-card, .live-class-card { border: 1px solid var(--live-line) !important; border-radius: 14px; box-shadow: 0 12px 30px rgba(16, 42, 67, .07) !important; }
    .live-form-card { background: rgba(255, 255, 255, .88); }
    .live-form-card .form-label { color: var(--live-ink); font-size: .78rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
    .live-form-card .form-control, .live-form-card .form-select, .live-class-card .form-control { border-color: var(--live-line); border-radius: 8px; min-height: 42px; }
    .live-form-card .form-control:focus, .live-form-card .form-select:focus, .live-class-card .form-control:focus { border-color: var(--live-teal); box-shadow: 0 0 0 .2rem rgba(8, 127, 140, .14); }
    .live-class-card { background: #fff; overflow: hidden; position: relative; }
    .live-class-card::before { background: var(--live-teal); content: ''; height: 4px; left: 0; position: absolute; right: 0; top: 0; }
    .live-class-card .card-body { padding: 1.35rem; }
    .live-class-card h5 { color: var(--live-ink); font-weight: 800; }
    .live-class-card .text-muted { color: var(--live-muted) !important; }
    .live-status { border-radius: 999px; font-size: .68rem; font-weight: 800; letter-spacing: .06em; padding: .42rem .68rem; text-transform: uppercase; }
    .live-status-created { background: #d9f3f0; color: #075e65; }
    .live-status-scheduled { background: #fff0c7; color: #765400; }
    .live-status-live { background: #ffe1dc; color: #a33120; }
    .live-status-ended { background: #e8edf1; color: #52606d; }
    .live-action-primary { background: var(--live-coral); border-color: var(--live-coral); border-radius: 8px; font-weight: 700; }
    .live-action-primary:hover, .live-action-primary:focus-visible { background: #c94c36; border-color: #c94c36; }
    .live-action-outline { border-color: var(--live-teal); border-radius: 8px; color: var(--live-teal); font-weight: 700; }
    .live-action-outline:hover, .live-action-outline:focus-visible { background: var(--live-teal); border-color: var(--live-teal); color: #fff; }
    @media (max-width: 575.98px) { .live-page-header { align-items: flex-start !important; } .live-create-button { width: 100%; } .live-class-card .card-body { padding: 1rem; } }
</style>
@endpush

@section('content')
<div class="container-fluid py-3 live-page-shell">
    <div class="d-flex justify-content-between mb-4 flex-wrap gap-3 live-page-header">
        <div>
            <div class="live-page-kicker mb-2">Faculty workspace / Virtual learning</div>
            <h2 class="mb-1">Live Classes</h2>
            <p class="text-muted mb-0">Schedule and manage virtual lessons for your assigned courses.</p>
        </div>
        <button type="submit" id="createLiveClassButton" class="btn live-create-button" form="createLiveClassForm" aria-controls="createLiveClassForm">
            <i class="bi bi-camera-video me-1"></i> Create Live Class
        </button>
    </div>

    <div class="card live-form-card mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('teacher.live-classes.create') }}" id="createLiveClassForm" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Class Title</label>
                    <input type="text" name="title" id="liveClassTitle" class="form-control" required>
                </div>
                <div class="col-md-3" data-schedule-field>
                    <label class="form-label">Course</label>
                        <select name="school_class_id" id="liveClassSchoolClass" class="form-select" required>
                        <option value="">Select a class</option>
                        @foreach($teacherClasses as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Subject</label>
                        <select name="subject_id" id="liveClassSubject" class="form-select" required>
                        <option value="">Select a subject</option>
                        @foreach($teacherClasses as $class)
                            @foreach($class->subjects as $subject)
                                    <option value="{{ $subject->id }}" data-class-id="{{ $class->id }}">{{ $subject->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control">
                </div>
                <div class="col-md-2" data-schedule-field>
                    <label class="form-label">Duration</label>
                    <input type="number" name="duration_minutes" class="form-control" value="45" min="15" max="240">
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3">
        @forelse($classes as $liveClass)
            <div class="col-lg-6">
                <div class="card live-class-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3 gap-3 flex-wrap">
                            <div>
                                <span class="badge live-status live-status-{{ $liveClass->status }} mb-2">
                                    {{ ucfirst($liveClass->status) }}
                                </span>
                                <h5 class="mb-1">{{ $liveClass->title }}</h5>
                                <p class="text-muted mb-0">{{ $liveClass->schoolClass->name ?? 'Class' }} • {{ $liveClass->subject->name ?? 'Course' }}</p>
                            </div>
                            @if($liveClass->status === 'created')
                                <span class="badge bg-info text-dark">Ready to schedule</span>
                            @elseif($liveClass->status === 'scheduled')
                                <form method="POST" action="{{ route('live-classes.start', $liveClass) }}">
                                    @csrf
                                    <button type="submit" class="btn live-action-primary btn-sm">Go Live</button>
                                </form>
                            @elseif($liveClass->status === 'live')
                                <form method="POST" action="{{ route('live-classes.end', $liveClass) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary btn-sm">End Class</button>
                                </form>
                            @endif
                        </div>
                        <div class="small text-muted mb-3">
                            @if($liveClass->status === 'created')
                                <form method="POST" action="{{ route('live-classes.schedule', $liveClass) }}" class="row g-2 align-items-end">
                                    @csrf
                                    @method('PATCH')
                                    <div class="col-md-5"><label class="form-label">Date and time</label><input type="datetime-local" name="scheduled_at" class="form-control" required></div>
                                    <div class="col-md-3"><label class="form-label">Duration</label><input type="number" name="duration_minutes" class="form-control" value="45" min="15" max="240" required></div>
                                    <div class="col-md-4"><button type="submit" class="btn btn-success w-100">Schedule Live Class</button></div>
                                </form>
                            @else
                                <div><i class="bi bi-calendar3 me-1"></i>{{ $liveClass->scheduled_at?->format('M d, Y h:i A') }}</div>
                            @endif
                            <div><i class="bi bi-clock-history me-1"></i>{{ $liveClass->duration_minutes }} minutes</div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('live-classes.show', $liveClass) }}" class="btn live-action-outline btn-sm">Open Classroom</a>
                            @if($liveClass->status === 'live')
                                <a href="{{ route('live-classes.show', $liveClass) }}" class="btn live-create-button btn-sm">Join Live Class</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border">No live classes scheduled yet.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const classSelect = document.getElementById('liveClassSchoolClass');
    const subjectSelect = document.getElementById('liveClassSubject');
    if (!classSelect || !subjectSelect) return;

    const filterSubjects = () => {
        const classId = classSelect.value;
        subjectSelect.value = '';
        subjectSelect.querySelectorAll('option[data-class-id]').forEach((option) => {
            option.hidden = !classId || option.dataset.classId !== classId;
        });
    };

    classSelect.addEventListener('change', filterSubjects);
    filterSubjects();
})();
</script>
@endpush
