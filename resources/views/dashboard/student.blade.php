@extends('layouts.app')

@section('title', 'University Portal — ' . auth()->user()->name)

@section('breadcrumb')
    <li class="breadcrumb-item active">Student Dashboard</li>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- ===== STUDENT HEADER & ID CARD ===== -->
    <div class="welcome-banner mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <span class="badge bg-white text-dark px-3 py-1 fw-bold rounded-pill">
                        <i class="bi bi-mortarboard-fill text-success me-1"></i> University Portal
                    </span>
                    @if($student)
                        <span class="text-white-50 small"><i class="bi bi-person-badge me-1"></i>Roll: <strong>{{ $student->roll_number }}</strong></span>
                        <span class="badge bg-info-subtle text-info fw-semibold">{{ $student->schoolClass->name ?? 'Grade 10' }} ({{ $student->section->name ?? 'A' }})</span>
                    @endif
                </div>
                <h2 class="welcome-title mb-1">Welcome back, {{ auth()->user()->name }}! 🎓</h2>
                <p class="welcome-text mb-0">University Student ID: <strong>{{ $student->student_id ?? 'Pending' }}</strong> | Admission ID: <strong>{{ $student->admission_number ?? 'N/A' }}</strong> | Status: <span class="badge bg-success-subtle text-success">Active Scholar</span></p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    <a href="{{ route('exams.results', ['student_id' => $student->id ?? 1]) }}" class="btn btn-crimson shadow-sm">
                        <i class="bi bi-file-earmark-bar-graph me-1"></i> Academic Transcript
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SEARCH COMPONENT ===== -->
    <!-- ===== 4 PRIMARY METRIC CARDS ===== -->
    <div class="row g-3 mb-4">
        <!-- Attendance Rate -->
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card stat-card-students">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Attendance Rate</span>
                        <h3 class="stat-number">{{ $attendanceRate }}%</h3>
                        <span class="stat-subtext text-success">
                            <i class="bi bi-check-circle-fill me-1"></i>{{ $presentDays }} / {{ $totalDays }} Days Present
                        </span>
                    </div>
                    <div class="stat-icon-wrapper icon-students">
                        <i class="bi bi-calendar2-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic GPA Standing -->
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card stat-card-teachers">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Academic Standing</span>
                        <h3 class="stat-number">{{ $overallGrade }} <span class="fs-6 text-muted">({{ $overallGpaPct }}%)</span></h3>
                        <span class="stat-subtext text-info">
                            <i class="bi bi-award-fill me-1"></i>Cumulative GPA
                        </span>
                    </div>
                    <div class="stat-icon-wrapper icon-teachers">
                        <i class="bi bi-award-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrolled Subjects -->
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card stat-card-classes">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Curriculum</span>
                        <h3 class="stat-number">{{ count($enrolledSubjects) }} Subjects</h3>
                        <span class="stat-subtext text-warning">
                            <i class="bi bi-book-fill me-1"></i>{{ $student->schoolClass->name ?? 'Class' }}
                        </span>
                    </div>
                    <div class="stat-icon-wrapper icon-classes">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Status -->
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card stat-card-fees">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Fee Status</span>
                        <h3 class="stat-number">
                            @if($pendingFeeCount == 0)
                                <span class="text-success fs-5"><i class="bi bi-check-circle-fill me-1"></i>Cleared</span>
                            @else
                                <span class="text-danger fs-5">{{ $pendingFeeCount }} Pending</span>
                            @endif
                        </h3>
                        <span class="stat-subtext text-muted">
                            <i class="bi bi-cash-coin me-1"></i>${{ number_format($totalFeePaid, 2) }} Paid
                        </span>
                    </div>
                    <div class="stat-icon-wrapper icon-fees">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dashboard.student-live-classes')

    <div class="lms-quick-nav mb-4" id="my-courses">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
                <span class="eyebrow-label">Learning Path</span>
                <h4 class="dashboard-section-header mb-0">My Courses & Study Progress</h4>
            </div>
            <span class="badge bg-success-subtle text-success px-3 py-2">{{ count($enrolledSubjects) }} active courses</span>
        </div>
        <div class="row g-3">
            @forelse($enrolledSubjects as $subject)
                <div class="col-lg-4 col-md-6">
                    <div class="course-portfolio-card h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="course-chip">{{ $student->schoolClass->name ?? 'Course' }}</span>
                            <span class="badge bg-light text-dark">{{ $subject->code ?? 'Course' }}</span>
                        </div>
                        <h6 class="fw-bold mb-2">{{ $subject->name }}</h6>
                        <p class="small text-muted mb-3">{{ $subject->description ?? 'Continue working through this subject by reviewing class materials and upcoming tasks.' }}</p>
                        <div class="mini-stat-grid">
                            <span><i class="bi bi-play-circle me-1"></i>{{ $subject->lectureVideos()->count() ?? 0 }} videos</span>
                            <span><i class="bi bi-file-earmark-pdf me-1"></i>{{ $subject->courseMaterials()->count() ?? 0 }} files</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-lms-state">
                        <i class="bi bi-journal-bookmark me-2"></i>
                        No course enrollments are currently assigned to your student record. Your learning modules will appear here once a course is linked.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- ===== PERFORMANCE CHART & ATTENDANCE LOG ===== -->
    <div class="row g-3 mb-4">
        <!-- Subject Performance Chart -->
        <div class="col-lg-8">
            <div class="card custom-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-bar-chart-line-fill me-2 text-danger"></i>Subject Performance Analysis</h5>
                    <span class="badge bg-light text-dark">Current Term</span>
                </div>
                <div class="card-body">
                    @if(count($subjectScores) > 0)
                        <div style="height: 260px; position: relative;">
                            <canvas id="studentSubjectChart"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-bar-chart fs-1 mb-2 text-secondary"></i>
                            <h6>No Examination Scores Recorded Yet</h6>
                            <p class="small">Scores and percentage progress bars will display here once teachers grade your tests.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Attendance Streak & Recent Records -->
        <div class="col-lg-4">
            <div class="card custom-card h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0"><i class="bi bi-calendar-check me-2 text-primary"></i>Recent Attendance</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between p-3 rounded bg-light mb-3">
                        <div>
                            <span class="small text-muted d-block">Overall Attendance</span>
                            <h4 class="fw-bold mb-0 text-success">{{ $attendanceRate }}%</h4>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success-subtle text-success me-1">{{ $presentDays }} Present</span>
                            <span class="badge bg-danger-subtle text-danger">{{ $absentDays }} Absent</span>
                        </div>
                    </div>

                    <h6 class="small fw-bold text-uppercase text-muted mb-2">Last 7 Roll Calls:</h6>
                    <ul class="list-group list-group-flush small">
                        @forelse($recentAttendance as $att)
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-calendar3 me-2 text-muted"></i>{{ $att->attendance_date->format('M d, Y') }}</span>
                                @if($att->status === 'present')
                                    <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Present</span>
                                @elseif($att->status === 'late')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Late</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-lg me-1"></i>Absent</span>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item px-0 py-3 text-center text-muted">No attendance marked yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== EXAM SCORES LEDGER & UPCOMING TESTS ===== -->
    <div class="row g-3 mb-4">
        <!-- Examination Scores Ledger -->
        <div class="col-lg-7">
            <div class="card custom-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-journal-check me-2 text-primary"></i>My Examination Results</h5>
                    <a href="{{ route('exams.results', ['student_id' => $student->id ?? 1]) }}" class="btn btn-sm btn-outline-secondary">Full Transcript</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Subject</th>
                                <th>Exam Name</th>
                                <th>Score</th>
                                <th>Grade</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($examMarks as $mark)
                                <tr>
                                    <td class="fw-bold">{{ $mark->exam->subject->name ?? 'General' }}</td>
                                    <td>{{ $mark->exam->name }}</td>
                                    <td>
                                        <strong>{{ $mark->is_absent ? '0 (Absent)' : $mark->marks_obtained }}</strong> / {{ $mark->exam->total_marks }}
                                    </td>
                                    <td><span class="badge bg-primary fs-6">{{ $mark->is_absent ? 'F' : $mark->grade }}</span></td>
                                    <td>
                                        @if($mark->is_absent)
                                            <span class="badge bg-danger">Absent</span>
                                        @elseif($mark->marks_obtained >= $mark->exam->pass_marks)
                                            <span class="badge bg-success">Passed</span>
                                        @else
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No exam results recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Upcoming Schedule -->
        <div class="col-lg-5">
            <div class="card custom-card h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0"><i class="bi bi-calendar-event me-2 text-warning"></i>Upcoming Tests & Assessments</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($upcomingExams as $test)
                            <li class="list-group-item p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-dark">{{ $test->name }}</div>
                                    <small class="text-muted">
                                        <i class="bi bi-book me-1"></i>{{ $test->subject->name ?? 'General' }} | 
                                        <i class="bi bi-calendar me-1"></i>{{ $test->exam_date->format('M d, Y') }}
                                    </small>
                                </div>
                                <span class="badge bg-light text-dark text-uppercase">{{ $test->exam_type }}</span>
                            </li>
                        @empty
                            <li class="list-group-item p-4 text-center text-muted">No upcoming exams scheduled.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== TESTS / EXAMS SECTION ===== -->
    @if(isset($upcomingTests) && $upcomingTests->count() > 0)
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="eyebrow-label">Assessments</span>
                <a href="{{ route('student.tests.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-arrow-right me-1"></i>View All Tests
                </a>
            </div>
            <h4 class="dashboard-section-header mb-3">Available Tests & Online Assessments</h4>
        </div>

        <!-- Available Tests -->
        @php
            $availableTests = $upcomingTests->where('status', 'published')->whereDate('close_date', '>=', now())->take(3);
        @endphp
        @forelse($availableTests as $test)
            <div class="col-lg-4 col-md-6">
                <div class="card custom-card h-100 test-card">
                    <div class="card-body d-flex flex-column">
                        <div class="mb-3">
                            <span class="badge bg-success mb-2"><i class="bi bi-play-circle me-1"></i>Available</span>
                            <h6 class="fw-bold mb-1">{{ $test->title }}</h6>
                            <small class="text-muted d-block mb-1">{{ $test->subject->name ?? 'Subject' }}</small>
                        </div>

                        <div class="mb-3 p-2 bg-light rounded flex-grow-1">
                            <div class="small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Marks:</span>
                                    <strong>{{ $test->total_marks }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Duration:</span>
                                    <strong>{{ $test->duration_minutes }} min</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Pass:</span>
                                    <strong>{{ $test->pass_marks }}</strong>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('student.tests.show', $test) }}" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-arrow-right me-1"></i>Open Test
                        </a>
                    </div>
                </div>
            </div>
        @empty
        @endforelse

        @if($availableTests->count() === 0)
            <div class="col-12">
                <div class="card custom-card text-center py-4">
                    <i class="bi bi-inbox fs-3 text-muted mb-2 d-block"></i>
                    <p class="text-muted mb-0">No tests are currently available. Check back soon!</p>
                </div>
            </div>
        @endif
    </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card custom-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-play-circle me-2 text-primary"></i>Latest Lecture Videos</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($studentVideos as $video)
                        <div class="border-bottom p-3">
                            <div class="fw-semibold text-dark mb-2">{{ $video->title }}</div>
                            <video controls class="w-100 rounded" style="max-height: 220px; background:#0a1f44;">
                                <source src="{{ $video->file_url }}" type="{{ $video->mime_type ?? 'video/mp4' }}">
                            </video>
                            <div class="small text-muted mt-2">{{ $video->subject->name ?? 'Course' }} • {{ $video->schoolClass->name ?? 'Class' }}</div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">No lecture videos uploaded for your class yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card custom-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF Course Materials</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($studentMaterials as $material)
                        <div class="border-bottom p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="fw-semibold text-dark">{{ $material->title }}</div>
                                    <small class="text-muted d-block">{{ $material->subject->name ?? 'Course' }} • {{ $material->teacher->user->name ?? 'Teacher' }}</small>
                                </div>
                                <span class="badge bg-light text-dark">{{ strtoupper($material->mime_type ?? 'PDF') }}</span>
                            </div>
                            <div class="mt-3 d-flex gap-2 flex-wrap">
                                <a href="{{ $material->file_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary"><i class="bi bi-box-arrow-up-right me-1"></i>Open</a>
                                <a href="{{ $material->file_url }}" download class="btn btn-sm btn-outline-secondary"><i class="bi bi-download me-1"></i>Download</a>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">No PDF learning materials available for your enrolled courses.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card custom-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-card-checklist me-2 text-warning"></i>Assignments</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($studentAssignments as $assignment)
                        <div class="border-bottom p-3">
                            <div class="fw-semibold text-dark">{{ $assignment->title }}</div>
                            <small class="text-muted d-block">{{ $assignment->subject->name ?? 'Course' }} • Due {{ optional($assignment->due_date)->format('M d, Y') }}</small>
                            @if($assignment->file_path)
                                <div class="mt-2">
                                    <a href="{{ $assignment->file_url }}" target="_blank" class="btn btn-sm btn-outline-success"><i class="bi bi-download me-1"></i>Download</a>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">No assignments posted for your classes yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- ===== CAMPUS NOTICES & CIRCULARS ===== -->
    <div class="card custom-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bi bi-megaphone me-2 text-danger"></i>Campus Circulars & Announcements</h5>
            <a href="{{ route('public.notices') }}" target="_blank" class="btn btn-sm btn-outline-secondary">Public Board</a>
        </div>
        <div class="card-body p-0">
            <div class="row g-0">
                @forelse($notices as $n)
                    <div class="col-md-6 p-3 border-bottom border-end">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="badge bg-light text-dark text-uppercase small">{{ $n->type }}</span>
                            <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">{{ $n->title }}</h6>
                        <p class="text-muted small mb-2">{{ Str::limit($n->content, 110) }}</p>
                        <a href="{{ route('public.notices.single', $n->slug ?? $n->id) }}" target="_blank" class="small fw-semibold text-danger text-decoration-none">
                            Read Notice <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted w-100">No active circulars posted.</div>
                @endforelse
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
    const chartCtx = document.getElementById('studentSubjectChart');
    if (chartCtx) {
        const scores = @json($subjectScores);
        if (scores.length > 0) {
            new Chart(chartCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: scores.map(s => s.subject),
                    datasets: [{
                        label: 'Score Percentage (%)',
                        data: scores.map(s => s.pct),
                        backgroundColor: '#9f1239',
                        borderRadius: 4,
                        maxBarThickness: 36
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) { return value + "%"; }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    }
});
</script>
@endpush
