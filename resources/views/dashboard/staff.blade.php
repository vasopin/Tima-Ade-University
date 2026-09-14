@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Staff Dashboard</li>
@endsection

@push('styles')
<style>
    .staff-overview .section-kicker { letter-spacing: .08em; font-size: .7rem; }
    .staff-overview .metric-card { border: 1px solid #e5e7eb; border-left: 4px solid #016ED5; }
    .staff-overview .metric-card.metric-success { border-left-color: #198754; }
    .staff-overview .metric-card.metric-warning { border-left-color: #d97706; }
    .staff-overview .metric-card.metric-muted { border-left-color: #64748b; }
    .staff-overview .record-row:last-child { border-bottom: 0 !important; }
    .staff-overview .quick-action { border: 1px solid #dbe3ec; transition: border-color .15s ease, box-shadow .15s ease; }
    .staff-overview .quick-action:hover, .staff-overview .quick-action:focus { border-color: #016ED5; box-shadow: 0 4px 14px rgba(1, 110, 213, .12); }
</style>
@endpush

@section('content')
<div class="container-fluid px-0 staff-overview">
    <div class="welcome-banner mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-white text-dark px-3 py-1 fw-bold rounded-pill">
                    <i class="bi bi-person-badge text-primary me-1"></i> Staff Workspace
                </span>
                <h2 class="welcome-title mb-1 mt-3">Welcome back, {{ $user->name }}.</h2>
                <p class="welcome-text mb-0">A focused workspace for student services, family records, and institutional updates.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    <a href="{{ route('students.create') }}" class="btn btn-crimson shadow-sm">
                        <i class="bi bi-person-plus me-1"></i> Add Student
                    </a>
                    <a href="{{ route('students.index') }}" class="btn btn-light text-dark shadow-sm">
                        <i class="bi bi-search me-1"></i> Find Student
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SEARCH COMPONENT ===== -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3"><div class="card metric-card shadow-sm h-100"><div class="card-body"><div class="text-muted text-uppercase fw-bold section-kicker">Student records</div><div class="d-flex align-items-end justify-content-between mt-2"><strong class="fs-2 text-dark">{{ $studentStats['total'] }}</strong><i class="bi bi-people fs-3 text-primary"></i></div><div class="small text-muted mt-1">{{ $studentStats['active'] }} active enrollments</div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card metric-card metric-success shadow-sm h-100"><div class="card-body"><div class="text-muted text-uppercase fw-bold section-kicker">Parents & guardians</div><div class="d-flex align-items-end justify-content-between mt-2"><strong class="fs-2 text-dark">{{ $parentCount }}</strong><i class="bi bi-person-heart fs-3 text-success"></i></div><div class="small text-muted mt-1">Linked family records</div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card metric-card metric-warning shadow-sm h-100"><div class="card-body"><div class="text-muted text-uppercase fw-bold section-kicker">Applications</div><div class="d-flex align-items-end justify-content-between mt-2"><strong class="fs-2 text-dark">{{ $pendingApplications }}</strong><i class="bi bi-file-earmark-person fs-3 text-warning"></i></div><div class="small text-muted mt-1">Awaiting attention</div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card metric-card metric-muted shadow-sm h-100"><div class="card-body"><div class="text-muted text-uppercase fw-bold section-kicker">Records requiring attention</div><div class="d-flex align-items-end justify-content-between mt-2"><strong class="fs-2 text-dark">{{ $studentStats['attention'] }}</strong><i class="bi bi-clipboard-check fs-3 text-secondary"></i></div><div class="small text-muted mt-1">Non-active student statuses</div></div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase text-primary small fw-bold">University updates</span>
                        <h5 class="fw-bold mb-0 mt-1">Latest Notices</h5>
                    </div>
                    <a href="{{ route('public.notices') }}" class="btn btn-sm btn-outline-primary">View all</a>
                </div>
                <div class="card-body p-0">
                    @forelse($notices as $notice)
                        <article class="px-4 py-3 border-bottom">
                            <h6 class="fw-bold mb-1">{{ $notice->title }}</h6>
                            <p class="text-muted small mb-1">{{ \Illuminate\Support\Str::limit(strip_tags($notice->content), 180) }}</p>
                            <time class="text-muted small" datetime="{{ optional($notice->published_date)->toDateString() }}">{{ optional($notice->published_date)->format('F j, Y') }}</time>
                        </article>
                    @empty
                        <div class="p-4 text-muted">No published notices are available yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <span class="text-uppercase text-primary small fw-bold">Account information</span>
                    <h5 class="fw-bold mb-0 mt-1">Your Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-sm-5 text-muted">Name</dt>
                        <dd class="col-sm-7">{{ $user->name }}</dd>
                        <dt class="col-sm-5 text-muted">Email</dt>
                        <dd class="col-sm-7 text-break">{{ $user->email }}</dd>
                        <dt class="col-sm-5 text-muted">Phone</dt>
                        <dd class="col-sm-7">{{ $user->phone ?: 'Not provided' }}</dd>
                        <dt class="col-sm-5 text-muted">Status</dt>
                        <dd class="col-sm-7 text-capitalize">{{ $user->status ?? 'active' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div><span class="text-uppercase text-primary fw-bold section-kicker">Student services</span><h5 class="fw-bold mb-0 mt-1">Recently added students</h5></div>
                    <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-primary">Open directory</a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentStudents as $student)
                        <a href="{{ route('students.show', $student) }}" class="record-row d-flex align-items-center gap-3 px-4 py-3 border-bottom text-decoration-none">
                            <img src="{{ $student->user->avatar_url }}" alt="" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            <div class="flex-grow-1"><div class="fw-semibold text-dark">{{ $student->user->name }}</div><div class="small text-muted">{{ $student->student_id ?? $student->admission_number }} · {{ $student->schoolClass->name ?? 'Class not assigned' }}</div></div>
                            <span class="badge rounded-pill {{ $student->status === 'active' ? 'bg-success-subtle text-success' : 'bg-light text-secondary' }} text-capitalize">{{ $student->status }}</span>
                        </a>
                    @empty
                        <div class="p-4 text-muted">No student records are available yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div><span class="text-uppercase text-success fw-bold section-kicker">Family records</span><h5 class="fw-bold mb-0 mt-1">Recently added parents</h5></div>
                    <a href="{{ route('parents.index') }}" class="btn btn-sm btn-outline-success">View all</a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentParents as $parent)
                        <a href="{{ route('parents.show', $parent) }}" class="record-row d-flex align-items-center gap-3 px-4 py-3 border-bottom text-decoration-none">
                            <img src="{{ $parent->user->avatar_url }}" alt="" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            <div class="flex-grow-1"><div class="fw-semibold text-dark">{{ $parent->user->name }}</div><div class="small text-muted">{{ $parent->students->count() }} linked student{{ $parent->students->count() === 1 ? '' : 's' }}</div></div>
                            <i class="bi bi-chevron-right text-muted" aria-hidden="true"></i>
                        </a>
                    @empty
                        <div class="p-4 text-muted">No parent or guardian records are available yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-md-4">
            <a href="{{ route('students.index') }}" class="card border-0 shadow-sm rounded-4 h-100 text-decoration-none">
                <div class="card-body p-4">
                    <i class="bi bi-people fs-2 text-primary"></i>
                    <h5 class="fw-bold text-dark mt-3">Student Directory</h5>
                    <p class="text-muted small mb-0">Search authorized student enrollment and contact information.</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('parents.index') }}" class="card border-0 shadow-sm rounded-4 h-100 text-decoration-none">
                <div class="card-body p-4">
                    <i class="bi bi-person-heart fs-2 text-success"></i>
                    <h5 class="fw-bold text-dark mt-3">Parent Directory</h5>
                    <p class="text-muted small mb-0">Review guardian contacts and linked students.</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.applications') }}" class="quick-action card border-0 shadow-sm rounded-4 h-100 text-decoration-none">
                <div class="card-body p-4"><i class="bi bi-file-earmark-person fs-2 text-warning"></i><h5 class="fw-bold text-dark mt-3">Application Register</h5><p class="text-muted small mb-0">Review {{ $pendingApplications }} application{{ $pendingApplications === 1 ? '' : 's' }} requiring attention.</p></div>
            </a>
        </div>
    </div>

    <!-- ===== MONTHLY CALENDAR ===== -->
    <div class="mt-8 mb-6">
    </div>
</div>
@endsection
