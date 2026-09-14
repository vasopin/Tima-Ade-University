@extends('layouts.app')

@section('title', $test->title . ' — Submissions')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher.tests.index') }}">Tests</a></li>
    <li class="breadcrumb-item active">{{ $test->title }} - Submissions</li>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- ===== PAGE HEADER ===== -->
    <div class="welcome-banner mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="welcome-title mb-1">{{ $test->title }} — Student Submissions</h2>
                <p class="welcome-text mb-0">
                    <i class="bi bi-book me-1"></i>{{ $test->subject->name }} | 
                    <i class="bi bi-building me-1"></i>{{ $test->schoolClass->name }}
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('teacher.tests.show', $test) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Test
                </a>
            </div>
        </div>
    </div>

    <!-- ===== STAT CARDS ===== -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle p-3 bg-primary text-white">
                            <i class="bi bi-inbox fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-semibold">Total Submissions</small>
                            <h5 class="mb-0">{{ $test->attempts->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle p-3 bg-success text-white">
                            <i class="bi bi-check-circle fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-semibold">Graded</small>
                            <h5 class="mb-0">{{ $test->attempts->where('status', 'graded')->count() + $test->attempts->where('status', 'results_released')->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle p-3 bg-warning text-white">
                            <i class="bi bi-hourglass-split fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-semibold">Pending Review</small>
                            <h5 class="mb-0">{{ $test->attempts->where('status', 'submitted')->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle p-3 bg-info text-white">
                            <i class="bi bi-graph-up fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-semibold">Avg. Score</small>
                            <h5 class="mb-0">
                                @php
                                    $avgScore = $test->attempts->whereIn('status', ['graded', 'results_released'])->avg('score');
                                @endphp
                                {{ round($avgScore) ?? 'N/A' }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SUBMISSIONS TABLE ===== -->
    <div class="card custom-card">
        <div class="card-header bg-white border-bottom">
            <h5 class="card-title mb-0"><i class="bi bi-table me-2"></i>All Submissions</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Roll No</th>
                        <th>Attempt</th>
                        <th>Status</th>
                        <th>Score</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($test->attempts as $attempt)
                        <tr>
                            <td>
                                <strong>{{ $attempt->student->user->name }}</strong><br>
                                <small class="text-muted">{{ $attempt->student->student_id }}</small>
                            </td>
                            <td>{{ $attempt->student->roll_number }}</td>
                            <td>{{ $attempt->attempt_number }}/{{ $test->attempt_limit ?? '∞' }}</td>
                            <td>
                                @if($attempt->status === 'in_progress')
                                    <span class="badge bg-info">In Progress</span>
                                @elseif($attempt->status === 'submitted')
                                    <span class="badge bg-warning text-dark">Submitted</span>
                                @elseif($attempt->status === 'graded')
                                    <span class="badge bg-info">Graded</span>
                                @elseif($attempt->status === 'results_released')
                                    <span class="badge bg-success">Released</span>
                                @endif
                            </td>
                            <td>
                                @if($attempt->score !== null)
                                    <strong>{{ $attempt->score }}/{{ $test->total_marks }}</strong><br>
                                    <small class="@if($attempt->is_passed) text-success @else text-danger @endif">
                                        @if($attempt->is_passed)
                                            ✓ Passed
                                        @else
                                            ✗ Failed
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">Pending</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $attempt->submitted_at?->format('M d, Y H:i') ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('teacher.tests.grade-submission', $attempt) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 mb-2 d-block"></i>
                                No submissions yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Release Results Button -->
    @if($test->attempts->where('status', 'graded')->count() > 0 || $test->attempts->where('status', 'results_released')->count() > 0)
        <div class="mt-4">
            @if(!$test->canViewResults() && $test->results_release_date > now())
                <form action="{{ route('teacher.tests.release-results', $test) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-unlock me-1"></i> Release All Results Now
                    </button>
                    <small class="text-muted d-block mt-2">Scheduled for {{ $test->results_release_date->format('M d, Y H:i') }}</small>
                </form>
            @elseif($test->canViewResults())
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-1"></i>
                    <strong>Results Released:</strong> Students can view their results as of {{ $test->results_release_date?->format('M d, Y H:i') }}
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
