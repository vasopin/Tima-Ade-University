@extends('layouts.app')

@section('title', $exam->name . ' — Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Examinations</a></li>
    <li class="breadcrumb-item active">{{ $exam->name }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title mb-1">{{ $exam->name }}</h3>
            <p class="text-muted small mb-0">Class: <strong>{{ $exam->schoolClass->name }}</strong> | Subject: <strong>{{ $exam->subject->name }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('exams.marks', $exam) }}" class="btn btn-success">
                <i class="bi bi-pencil-square me-1"></i> Record / Edit Marks
            </a>
            <a href="{{ route('exams.edit', $exam) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Exam Info Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card custom-card text-center p-3">
                <span class="text-muted small">Exam Date</span>
                <h5 class="fw-bold mb-0 text-navy">{{ $exam->exam_date->format('M d, Y') }}</h5>
                <small class="text-muted">{{ $exam->start_time ? $exam->start_time->format('h:i A') : 'Time TBD' }}</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card custom-card text-center p-3">
                <span class="text-muted small">Total / Pass Marks</span>
                <h5 class="fw-bold mb-0 text-primary">{{ $exam->total_marks }} / {{ $exam->pass_marks }}</h5>
                <small class="text-muted">Min {{ round(($exam->pass_marks / $exam->total_marks) * 100) }}% to pass</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card custom-card text-center p-3">
                <span class="text-muted small">Students Evaluated</span>
                <h5 class="fw-bold mb-0 text-success">{{ $markedCount }} / {{ $students->count() }}</h5>
                <small class="text-muted">{{ $passCount }} Passed</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card custom-card text-center p-3">
                <span class="text-muted small">Class Average</span>
                <h5 class="fw-bold mb-0 text-danger">{{ $avgMarks }} / {{ $exam->total_marks }}</h5>
                <small class="text-muted">{{ $exam->total_marks > 0 ? round(($avgMarks / $exam->total_marks) * 100, 1) : 0 }}% Score</small>
            </div>
        </div>
    </div>

    @if($exam->instructions)
        <div class="alert alert-info d-flex align-items-center mb-4">
            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
            <div>
                <strong>Instructions & Syllabus:</strong> {{ $exam->instructions }}
            </div>
        </div>
    @endif

    <!-- Students Marks Table -->
    <div class="card custom-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bi bi-people-fill me-2 text-danger"></i>Enrolled Students & Results</h5>
            <span class="badge bg-light text-dark">{{ $students->count() }} Total Students</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Status</th>
                        <th>Marks Obtained</th>
                        <th>Grade</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        @php
                            $mark = $exam->marks->firstWhere('student_id', $student->id);
                        @endphp
                        <tr>
                            <td><span class="badge bg-light text-dark">{{ $student->roll_number }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $student->user->avatar_url }}" class="rounded-circle me-2" width="32" height="32" alt="">
                                    <div>
                                        <div class="fw-bold">{{ $student->user->name }}</div>
                                        <small class="text-muted">{{ $student->admission_number }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if(!$mark)
                                    <span class="badge bg-secondary">Not Graded</span>
                                @elseif($mark->is_absent)
                                    <span class="badge bg-danger">Absent</span>
                                @elseif($mark->marks_obtained >= $exam->pass_marks)
                                    <span class="badge bg-success">Passed</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </td>
                            <td>
                                @if($mark)
                                    <strong>{{ $mark->is_absent ? '0 (Absent)' : $mark->marks_obtained }}</strong> / {{ $exam->total_marks }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($mark && !$mark->is_absent && $mark->grade)
                                    <span class="badge bg-primary fs-6">{{ $mark->grade }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $mark?->remarks ?? '—' }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No students enrolled in this class.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
