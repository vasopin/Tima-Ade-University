@extends('layouts.app')

@section('title', 'Tests — Student Portal')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Tests</li>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- ===== PAGE HEADER ===== -->
    <div class="welcome-banner mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <span class="badge bg-white text-dark px-3 py-1 fw-bold rounded-pill">
                        <i class="bi bi-pencil-square text-warning me-1"></i> Assessments
                    </span>
                </div>
                <h2 class="welcome-title mb-1">Tests & Examinations</h2>
                <p class="welcome-text mb-0">Complete your assigned tests and track your performance across all subjects.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <span class="badge bg-info-subtle text-info px-3 py-2 fs-6 rounded-pill">
                    <i class="bi bi-clock me-1"></i> {{ $upcomingTests->count() }} Available
                </span>
            </div>
        </div>
    </div>

    <!-- ===== TEST CATEGORIES ===== -->
    <div class="row g-3 mb-4">
        @php
            $available = $upcomingTests->filter(function ($test) {
                return $test->status === 'published'
                    && (!$test->close_date || now() >= $test->close_date);
            });

            $upcoming = $upcomingTests->filter(function ($test) {
                return $test->status === 'draft' || ($test->publish_date && now() < $test->publish_date);
            });

            $inProgress = $testAttempts->filter(fn ($attempt) => $attempt->status === 'in_progress');
            $submitted = $testAttempts->filter(fn ($attempt) => in_array($attempt->status, ['submitted', 'graded', 'results_released'], true));
        @endphp

        <!-- Available Tests -->
        <div class="col-lg-6">
            <div class="card custom-card h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0"><i class="bi bi-play-circle me-2 text-success"></i>Available Tests</h5>
                        <span class="badge bg-success">{{ $available->count() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($available as $test)
                        <div class="test-item d-flex justify-content-between align-items-start p-3 border rounded mb-2">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $test->title }}</h6>
                                <small class="text-muted">
                                    <i class="bi bi-book me-1"></i>{{ $test->subject->name ?? 'Subject' }}
                                </small><br>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $test->duration_minutes }} minutes | 
                                    <strong>{{ $test->total_marks }}</strong> marks
                                </small>
                            </div>
                            <a href="{{ route('student.tests.show', $test) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-right me-1"></i>Open
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 mb-2 d-block"></i>
                            <p>No tests available right now.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Results -->
        <div class="col-lg-6">
            <div class="card custom-card h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0"><i class="bi bi-file-earmark-check me-2 text-primary"></i>Recent Results</h5>
                        <span class="badge bg-primary">{{ $submitted->where('status', 'results_released')->count() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($submitted->where('status', 'results_released')->take(5) as $attempt)
                        <div class="result-item d-flex justify-content-between align-items-start p-3 border rounded mb-2">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $attempt->test->title }}</h6>
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>{{ $attempt->submitted_at?->format('M d, Y') }}
                                </small><br>
                                <small>
                                    Score: <strong>{{ $attempt->score ?? 0 }}/{{ $attempt->test->total_marks }}</strong> 
                                    <span class="badge @if($attempt->is_passed) bg-success @else bg-danger @endif">
                                        {{ $attempt->is_passed ? 'Passed' : 'Not Passed' }}
                                    </span>
                                </small>
                            </div>
                            <a href="{{ route('student.tests.results', $attempt->test) }}" class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 mb-2 d-block"></i>
                            <p>No results released yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- ===== TEST HISTORY TABLE ===== -->
    @if($submitted->count() > 0)
    <div class="card custom-card mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2 text-info"></i>All Test Submissions</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Test Name</th>
                        <th>Subject</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submitted as $attempt)
                        <tr>
                            <td><strong>{{ $attempt->test->title }}</strong></td>
                            <td>{{ $attempt->test->subject->name ?? 'General' }}</td>
                            <td>
                                @if($attempt->status === 'results_released')
                                    {{ $attempt->score }}/{{ $attempt->test->total_marks }}
                                @else
                                    <span class="text-muted">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($attempt->status === 'submitted')
                                    <span class="badge bg-warning text-dark">Grading</span>
                                @elseif($attempt->status === 'graded')
                                    <span class="badge bg-info">Graded</span>
                                @elseif($attempt->status === 'results_released')
                                    <span class="badge bg-success">Released</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($attempt->status) }}</span>
                                @endif
                            </td>
                            <td><small>{{ $attempt->submitted_at?->format('M d, Y H:i') }}</small></td>
                            <td>
                                @if($attempt->status === 'results_released')
                                    <a href="{{ route('student.tests.results', $attempt->test) }}" class="btn btn-sm btn-link">View Results</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
