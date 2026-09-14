@extends('layouts.app')

@section('title', $selectedChild->user->name . ' Overview')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Parent Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('parent.children') }}">My Children</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $selectedChild->user->name }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $selectedChild->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($selectedChild->user->name) . '&background=016ED5&color=fff' }}" alt="{{ $selectedChild->user->name }}" class="rounded-circle shadow-sm border border-2 border-primary" style="width: 64px; height: 64px; object-fit: cover;">
                    <div>
                        <h2 class="fw-bold mb-1 text-dark">{{ $selectedChild->user->name }}</h2>
                        <div class="text-muted small">
                            Roll No: <strong class="text-dark">{{ $selectedChild->roll_number ?? 'N/A' }}</strong> •
                            Admission: <strong class="text-dark">{{ $selectedChild->admission_number ?? 'N/A' }}</strong> •
                            Class: <strong class="text-primary">{{ $selectedChild->schoolClass->name ?? 'N/A' }}</strong> ({{ $selectedChild->section->name ?? 'N/A' }})
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('attendance.index', ['student_id' => $selectedChild->id]) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-calendar-check me-1"></i> Attendance</a>
                    <a href="{{ route('attendance.report', ['student_id' => $selectedChild->id]) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-bar-chart me-1"></i> Report</a>
                    <a href="{{ route('exams.results', ['student_id' => $selectedChild->id]) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-award me-1"></i> Results</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="small text-muted">Attendance Rate</div>
                <div class="fw-bold fs-3 text-dark">{{ $attendanceRate }}%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="small text-muted">Overall Grade</div>
                <div class="fw-bold fs-3 text-dark">{{ $overallGrade }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="small text-muted">Fees Paid</div>
                <div class="fw-bold fs-3 text-dark">${{ number_format($totalFeePaid, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="small text-muted">Pending Fees</div>
                <div class="fw-bold fs-3 text-dark">{{ $pendingFeeCount }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-award me-2 text-primary"></i>Recent Exam Marks</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Exam / Subject</th>
                                    <th>Marks</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($examMarks->take(6) as $mark)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $mark->exam->name ?? 'Exam' }}</div>
                                            <div class="small text-muted">{{ $mark->exam->subject->name ?? 'General' }}</div>
                                        </td>
                                        <td>{{ $mark->marks_obtained }} / {{ $mark->exam->total_marks ?? 100 }}</td>
                                        <td><span class="badge bg-primary px-2 py-1">{{ $mark->grade ?? 'N/A' }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No examination marks have been published yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-check me-2 text-warning"></i>Upcoming Exams</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($upcomingExams as $exam)
                            <li class="list-group-item p-3">
                                <div class="fw-semibold text-dark">{{ $exam->name }}</div>
                                <div class="small text-muted">{{ $exam->subject->name ?? 'Subject' }} • {{ \Carbon\Carbon::parse($exam->exam_date)->format('M d, Y') }}</div>
                            </li>
                        @empty
                            <li class="list-group-item p-4 text-center text-muted">No upcoming exams scheduled.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-megaphone me-2 text-info"></i>Announcements</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($notices as $notice)
                            <li class="list-group-item p-3">
                                <div class="fw-semibold text-dark">{{ $notice->title }}</div>
                                <div class="small text-muted">{{ Str::limit(strip_tags($notice->content), 120) }}</div>
                            </li>
                        @empty
                            <li class="list-group-item p-4 text-center text-muted">No announcements.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
