@extends('layouts.app')

@section('title', 'University Portal')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">University Portal</li>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- Welcome Banner -->
    <div class="welcome-banner mb-4" style="background: linear-gradient(135deg, #016ED5, #014B92); border-radius: 1rem; color: white; padding: 2rem;">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark px-3 py-1 fw-bold rounded-pill">
                        <i class="bi bi-shield-check me-1"></i> University Portal
                    </span>
                    <span class="text-white-50 small"><i class="bi bi-clock me-1"></i>{{ now()->format('l, F j, Y') }}</span>
                </div>
                <h2 class="welcome-title mb-1 fw-bold">Welcome, {{ auth()->user()->name }}! 👋</h2>
                <p class="mb-0 text-white-50">Track the academic journey, daily attendance, examination grades, and school fee records for your children.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <span class="badge bg-white text-dark px-3 py-2 fs-6 rounded-pill shadow-sm">
                    <i class="bi bi-people-fill text-primary me-1"></i> {{ $children->count() }} Linked {{ Str::plural('Child', $children->count()) }}
                </span>
            </div>
        </div>
    </div>

    <!-- ===== SEARCH COMPONENT ===== -->
    @if($children->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4">
            <i class="bi bi-mortarboard text-muted display-4 mb-3"></i>
            <h4 class="fw-bold text-dark mb-2">No Linked Students Found</h4>
            <p class="text-muted mb-3">Your parent account is active, but no student records have been linked to your profile yet.</p>
            <p class="small text-muted mb-0">Please contact school administration at <strong>admissions@timaade.edu</strong> to link your scholars.</p>
        </div>
    @else

        <!-- Child Switcher (if multiple children) -->
        @if($children->count() > 1)
            <div id="children" class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="fw-bold text-dark small text-uppercase tracking-wider">
                        <i class="bi bi-people me-1 text-primary"></i> Select Scholar to View:
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($children as $ch)
                            <a href="{{ route('dashboard', ['student_id' => $ch->id]) }}" 
                               class="btn btn-sm rounded-pill px-3 fw-semibold {{ ($selectedChild && $selectedChild->id === $ch->id) ? 'btn-primary shadow-sm' : 'btn-outline-secondary' }}"
                               style="{{ ($selectedChild && $selectedChild->id === $ch->id) ? 'background-color: #016ED5; border-color: #016ED5;' : '' }}">
                                <i class="bi bi-person-fill me-1"></i> {{ $ch->user->name }}
                                <span class="badge bg-white text-dark ms-1">{{ $ch->schoolClass->name ?? 'Class' }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div id="children"></div>
        @endif

        @if($selectedChild)
            <!-- Scholar Profile Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $selectedChild->user->avatar_url }}" alt="{{ $selectedChild->user->name }}" class="rounded-circle shadow-sm border border-2 border-primary" style="width: 60px; height: 60px; object-fit: cover;">
                                <div>
                                    <h4 class="fw-bold text-dark mb-1">{{ $selectedChild->user->name }}</h4>
                                    <div class="text-muted small">
                                        Roll No: <strong class="text-dark">{{ $selectedChild->roll_number }}</strong> &bull;
                                        Adm No: <strong class="text-dark">{{ $selectedChild->admission_number }}</strong> &bull;
                                        Class: <strong class="text-primary">{{ $selectedChild->schoolClass->name ?? 'N/A' }} ({{ $selectedChild->section->name ?? 'N/A' }})</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            {!! $selectedChild->status_badge !!}
                            <span class="badge bg-light text-dark border ms-2 px-3 py-2">
                                <i class="bi bi-award-fill text-warning me-1"></i> Grade: <strong>{{ $overallGrade }}</strong> ({{ $overallGpaPct }}%)
                            </span>
                            <div class="mt-3 d-flex justify-content-md-end gap-2 flex-wrap">
                                <a href="{{ route('attendance.index', ['student_id' => $selectedChild->id]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-calendar-check me-1"></i> Attendance
                                </a>
                                <a href="{{ route('attendance.report', ['student_id' => $selectedChild->id]) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-bar-chart me-1"></i> Attendance Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Metrics for Selected Child -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center text-white" style="background-color: #016ED5; width: 48px; height: 48px;">
                                <i class="bi bi-calendar-check-fill fs-5"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold">ATTENDANCE RATE</div>
                                <div class="fs-4 fw-bold text-dark">{{ $attendanceRate }}%</div>
                                <div class="small text-muted">{{ $presentDays }} of {{ $totalDays }} days present</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center bg-success text-white" style="width: 48px; height: 48px;">
                                <i class="bi bi-award-fill fs-5"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold">OVERALL PERFORMANCE</div>
                                <div class="fs-4 fw-bold text-dark">{{ $overallGrade }}</div>
                                <div class="small text-muted">{{ $overallGpaPct }}% average score</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center bg-warning text-dark" style="width: 48px; height: 48px;">
                                <i class="bi bi-cash-stack fs-5"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold">FEES CONTRIBUTED</div>
                                <div class="fs-4 fw-bold text-dark">${{ number_format($totalFeePaid, 2) }}</div>
                                <div class="small text-muted">{{ $pendingFeeCount }} pending invoices</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center text-white" style="background-color: #FB8B01; width: 48px; height: 48px;">
                                <i class="bi bi-journal-check fs-5"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold">UPCOMING EXAMS</div>
                                <div class="fs-4 fw-bold text-dark">{{ $upcomingExams->count() }}</div>
                                <div class="small text-muted">Scheduled assessments</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Performance Summary -->
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-check me-2 text-success"></i>Test Performance</h5>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $testAttempts->count() }} result{{ $testAttempts->count() === 1 ? '' : 's' }}</span>
                        </div>
                        <div class="card-body p-0">
                            @if($testAttempts->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Test</th>
                                                <th>Class</th>
                                                <th>Status</th>
                                                <th>Score</th>
                                                <th>Updated</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($testAttempts as $attempt)
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold text-dark">{{ $attempt->test->title ?? 'Assessment' }}</div>
                                                        <div class="small text-muted">{{ $attempt->test->subject->name ?? 'Subject' }}</div>
                                                    </td>
                                                    <td class="small text-muted">{{ $attempt->test->schoolClass->name ?? 'Class' }}</td>
                                                    <td>
                                                        @if(($attempt->status ?? '') === 'results_released')
                                                            <span class="badge bg-success">Released</span>
                                                        @elseif(($attempt->status ?? '') === 'graded')
                                                            <span class="badge bg-info">Graded</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($attempt->status === 'results_released')
                                                            <strong>{{ $attempt->score ?? 0 }}</strong> / {{ $attempt->test->total_marks ?? 0 }}
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="small text-muted">{{ optional($attempt->submitted_at)->format('M d, Y') ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-4 text-center text-muted">No test results have been released for this child yet.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Tabs for Child Data -->
            <div class="row g-4 mb-4">
                <!-- Exam Results -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-award me-2 text-primary"></i>Recent Exam Marks</h5>
                            <a href="{{ route('exams.results', ['student_id' => $selectedChild->id]) }}" class="btn btn-sm btn-outline-primary">Full Matrix</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Exam / Subject</th>
                                            <th>Date</th>
                                            <th>Marks</th>
                                            <th>Grade</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($examMarks->take(6) as $mark)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold text-dark">{{ $mark->exam->name ?? 'Exam' }}</div>
                                                    <div class="small text-muted">{{ $mark->exam->subject->name ?? 'General' }}</div>
                                                </td>
                                                <td class="small text-muted">{{ $mark->exam->exam_date ? \Carbon\Carbon::parse($mark->exam->exam_date)->format('M d, Y') : '—' }}</td>
                                                <td>
                                                    <span class="fw-bold">{{ $mark->marks_obtained }}</span> / {{ $mark->exam->total_marks ?? 100 }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary px-2 py-1">{{ $mark->grade ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    @if($mark->marks_obtained >= ($mark->exam->pass_marks ?? 40))
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Pass</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Fail</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">No examination marks published yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fee Statement & Upcoming Exams -->
                <div class="col-lg-5">
                    <!-- Fee Breakdown -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-cash-stack me-2 text-warning"></i>Fee Ledger</h5>
                            <a href="{{ route('fees.student', $selectedChild) }}" class="btn btn-sm btn-outline-warning text-dark">Statement</a>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($feePayments->take(4) as $fee)
                                    <li class="list-group-item p-3 d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $fee->feeStructure->fee_type ?? 'Tuition Fee' }}</div>
                                            <div class="small text-muted">Receipt: {{ $fee->receipt_number }} &bull; {{ \Carbon\Carbon::parse($fee->payment_date)->format('M d, Y') }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-dark">${{ number_format($fee->amount_paid, 2) }}</div>
                                            <span class="badge bg-success px-2 py-1">{{ ucfirst($fee->status) }}</span>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item p-4 text-center text-muted">No fee records found.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <!-- Upcoming Exams -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white border-bottom p-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-event me-2 text-info"></i>Upcoming Schedule</h5>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($upcomingExams as $ue)
                                    <li class="list-group-item p-3 d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $ue->name }}</div>
                                            <div class="small text-muted">{{ $ue->subject->name ?? 'Subject' }} &bull; {{ \Carbon\Carbon::parse($ue->exam_date)->format('D, M d Y') }}</div>
                                        </div>
                                        <span class="badge bg-light text-dark border">{{ $ue->start_time ?? '09:00 AM' }}</span>
                                    </li>
                                @empty
                                    <li class="list-group-item p-4 text-center text-muted">No scheduled exams at this time.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Official Notices Section -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom p-3">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-megaphone-fill me-2 text-primary"></i>School Announcements & Notices</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                @forelse($notices as $notice)
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary text-white">{{ ucfirst($notice->type ?? 'General') }}</span>
                                <span class="text-muted small">{{ $notice->created_at->format('M d, Y') }}</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $notice->title }}</h6>
                            <p class="text-muted small mb-0">{{ Str::limit($notice->content, 120) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">No active school notices.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ===== MONTHLY CALENDAR ===== -->
    <div class="mt-8 mb-6">
    </div>
</div>
@endsection
