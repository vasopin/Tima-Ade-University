@extends('layouts.app')

@section('title', $test->title . ' — Student Portal')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('student.tests.index') }}">Tests</a></li>
    <li class="breadcrumb-item active">{{ $test->title }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- ===== TEST DETAILS ===== -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card custom-card">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="card-title mb-1">{{ $test->title }}</h3>
                            <p class="text-muted mb-0">
                                <i class="bi bi-book me-1"></i>{{ $test->subject->name ?? 'Subject' }} | 
                                <i class="bi bi-building me-1"></i>{{ $test->schoolClass->name ?? 'Class' }}
                            </p>
                        </div>
                        @if($test->isAvailable())
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Available</span>
                        @elseif($test->status === 'draft')
                            <span class="badge bg-secondary">Draft</span>
                        @elseif($test->status === 'closed')
                            <span class="badge bg-danger">Closed</span>
                        @else
                            <span class="badge bg-info">{{ ucfirst($test->status) }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Instructions -->
                    <div class="mb-4 p-3 rounded bg-light">
                        <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-1 text-info"></i>Instructions</h6>
                        <p class="mb-0">{{ $test->instructions ?? 'No instructions provided.' }}</p>
                    </div>

                    <!-- Test Details Grid -->
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 border rounded">
                                <small class="text-muted d-block mb-1">Total Marks</small>
                                <h5 class="mb-0">{{ $test->total_marks }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 border rounded">
                                <small class="text-muted d-block mb-1">Pass Marks</small>
                                <h5 class="mb-0">{{ $test->pass_marks }}</h5>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 border rounded">
                                <small class="text-muted d-block mb-1">Duration</small>
                                <h5 class="mb-0">{{ $test->duration_minutes }} minutes</h5>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 border rounded">
                                <small class="text-muted d-block mb-1">Attempt Limit</small>
                                <h5 class="mb-0">{{ $test->attempt_limit ?? '∞' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions Preview -->
            <div class="card custom-card mt-3">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0"><i class="bi bi-question-circle me-2"></i>Questions ({{ $test->questions->count() }})</h5>
                </div>
                <div class="card-body">
                    @forelse($test->questions->sortBy('order_index') as $question)
                        <div class="p-3 border rounded mb-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong class="d-block mb-1">Q{{ $loop->iteration }}: {{ $question->question_text }}</strong>
                                    <small class="text-muted">
                                        <span class="badge bg-light text-dark me-2">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                        <span class="badge bg-info">{{ $question->marks }} marks</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">No questions in this test yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ===== ACTION SIDEBAR ===== -->
        <div class="col-lg-4">
            <div class="card custom-card sticky-top" style="top: 100px;">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-lightning me-1 text-warning"></i>Ready to Start?</h6>
                    
                    @if(!$test->isAvailable())
                        <div class="alert alert-warning" role="alert">
                            <strong>This test is not available right now.</strong>
                            @if($test->status === 'draft')
                                <p class="mb-0 small mt-2">The test is still in draft mode and not yet published.</p>
                            @elseif($test->status === 'closed')
                                <p class="mb-0 small mt-2">This test has closed. You can no longer submit answers.</p>
                            @elseif($test->publish_date > now())
                                <p class="mb-0 small mt-2">Available from {{ $test->publish_date->format('M d, Y H:i') }}</p>
                            @elseif($test->close_date && $test->close_date < now())
                                <p class="mb-0 small mt-2">This test closed on {{ $test->close_date->format('M d, Y H:i') }}</p>
                            @endif
                        </div>
                    @else
                        <form action="{{ route('student.tests.start', $test) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg w-100 mb-2">
                                <i class="bi bi-play-fill me-1"></i> Start Test
                            </button>
                        </form>
                        <small class="text-muted d-block text-center">You will have {{ $test->duration_minutes }} minutes to complete this test.</small>
                    @endif

                    <!-- Test Availability Info -->
                    <hr class="my-3">
                    <h6 class="fw-bold mb-3 small">Test Dates</h6>
                    <div class="small text-muted">
                        @if($test->publish_date)
                            <p class="mb-2">
                                <i class="bi bi-unlock me-1 text-success"></i>
                                <strong>Opens:</strong> {{ $test->publish_date->format('M d, Y H:i') }}
                            </p>
                        @endif
                        @if($test->close_date)
                            <p class="mb-2">
                                <i class="bi bi-lock me-1 text-danger"></i>
                                <strong>Closes:</strong> {{ $test->close_date->format('M d, Y H:i') }}
                            </p>
                        @endif
                        @if($test->results_release_date)
                            <p class="mb-2">
                                <i class="bi bi-eye me-1 text-info"></i>
                                <strong>Results:</strong> {{ $test->results_release_date->format('M d, Y H:i') }}
                            </p>
                        @endif
                    </div>

                    <!-- Attempt History -->
                    @php
                        $myAttempts = $test->attempts->where('student_id', auth()->user()->student->id ?? null);
                    @endphp
                    @if($myAttempts->count() > 0)
                        <hr class="my-3">
                        <h6 class="fw-bold mb-3 small">Your Attempts</h6>
                        <div class="small">
                            @foreach($myAttempts as $attempt)
                                <div class="p-2 border rounded mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong>Attempt #{{ $attempt->attempt_number }}</strong>
                                        <span class="badge @if($attempt->is_passed) bg-success @elseif($attempt->status === 'results_released') bg-danger @else bg-secondary @endif">
                                            {{ ucfirst($attempt->status) }}
                                        </span>
                                    </div>
                                    @if($attempt->status === 'results_released')
                                        <small class="text-muted">Score: {{ $attempt->score }}/{{ $test->total_marks }}</small><br>
                                    @endif
                                    <small class="text-muted">{{ $attempt->submitted_at?->format('M d, Y H:i') ?? 'In progress' }}</small>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
