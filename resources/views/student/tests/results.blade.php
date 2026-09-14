@extends('layouts.app')

@section('title', $test->title . ' — Results')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('student.tests.index') }}">Tests</a></li>
    <li class="breadcrumb-item active">{{ $test->title }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- ===== RESULTS HEADER ===== -->
    <div class="welcome-banner mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="welcome-title mb-1">{{ $test->title }} — Results</h2>
                <p class="welcome-text mb-0">
                    <i class="bi bi-book me-1"></i>{{ $test->subject->name ?? 'Subject' }} | 
                    <i class="bi bi-building me-1"></i>{{ $test->schoolClass->name ?? 'Class' }}
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                @if($test->canViewResults())
                    <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i>Results Released</span>
                @else
                    <span class="badge bg-warning px-3 py-2"><i class="bi bi-clock me-1"></i>Results Pending</span>
                @endif
            </div>
        </div>
    </div>

    @php
        $attempt = $test->attempts->where('student_id', auth()->user()->student?->id)->first();
    @endphp

    @if(!$test->canViewResults())
        <!-- Results Not Yet Released -->
        <div class="card custom-card bg-light border-warning">
            <div class="card-body text-center py-5">
                <i class="bi bi-hourglass-split fs-1 text-warning mb-3 d-block"></i>
                <h4 class="fw-bold">Results Are Not Yet Available</h4>
                <p class="text-muted mb-2">Your test has been submitted and is being graded.</p>
                <p class="text-muted mb-0">Results will be released on <strong>{{ $test->results_release_date?->format('M d, Y H:i') ?? 'a later date' }}</strong></p>
            </div>
        </div>
    @elseif($attempt && $attempt->status === 'results_released')
        <!-- Score Card -->
        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="card custom-card text-center" style="border-top: 4px solid #016ED5;">
                    <div class="card-body py-5">
                        <h2 class="fw-bold mb-3">
                            <span class="text-primary">{{ $attempt->score ?? 0 }}</span> / <span class="text-muted">{{ $test->total_marks }}</span>
                        </h2>
                        <h5 class="mb-3">{{ $attempt->percentage ?? 0 }}%</h5>
                        
                        <div class="progress mb-3" style="height: 12px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $attempt->percentage ?? 0 }}%; background-color: @if($attempt->is_passed) #28a745 @else #dc3545 @endif"
                                 aria-valuenow="{{ $attempt->percentage ?? 0 }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>

                        <div class="alert @if($attempt->is_passed) alert-success @else alert-danger @endif mb-3">
                            <strong>
                                @if($attempt->is_passed)
                                    <i class="bi bi-check-circle me-1"></i>PASSED
                                @else
                                    <i class="bi bi-x-circle me-1"></i>NOT PASSED
                                @endif
                            </strong>
                            <small class="d-block mt-1">
                                Pass marks: {{ $test->pass_marks }}
                                @if($attempt->is_passed)
                                    — You scored above the passing threshold ✓
                                @else
                                    — You need {{ $test->pass_marks - ($attempt->score ?? 0) }} more marks to pass
                                @endif
                            </small>
                        </div>

                        <small class="text-muted d-block">
                            <i class="bi bi-calendar me-1"></i>Submitted: {{ $attempt->submitted_at?->format('M d, Y H:i') }}<br>
                            <i class="bi bi-hourglass me-1"></i>Time Spent: {{ $attempt->time_spent_minutes ?? 0 }} minutes
                        </small>
                    </div>
                </div>
            </div>

            <!-- Grade Card -->
            <div class="col-lg-6">
                <div class="card custom-card">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0"><i class="bi bi-award me-2 text-warning"></i>Evaluation</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-2">Grade Letter</small>
                            <h3 class="fw-bold text-primary mb-0">{{ $attempt->grade ?? 'N/A' }}</h3>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-2">Assessment Summary</small>
                            <div class="p-2 bg-light rounded">
                                <div class="small">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Questions</span>
                                        <strong>{{ $test->questions->count() }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Marks</span>
                                        <strong>{{ $test->total_marks }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Marks Obtained</span>
                                        <strong class="text-success">{{ $attempt->score ?? 0 }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Pass Marks</span>
                                        <strong>{{ $test->pass_marks }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Your test was automatically graded for objective questions. 
                            @if($test->questions->where('question_type', 'short_answer')->count() > 0)
                                Short answer questions will be reviewed and graded by your teacher.
                            @else
                                All answers have been graded.
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question-by-Question Review -->
        <div class="card custom-card">
            <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0"><i class="bi bi-list-check me-2"></i>Question Review</h5>
            </div>
            <div class="card-body">
                @php
                    $answers = $attempt->answers ?? collect();
                @endphp

                @forelse($test->questions->sortBy('order_index') as $question)
                    @php
                        $studentAnswer = $answers->where('test_question_id', $question->id)->first();
                    @endphp
                    <div class="border-bottom pb-3 mb-3 last:border-0" style="border-bottom: 1px solid #dee2e6;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-1">Q{{ $loop->iteration }}: {{ $question->question_text }}</h6>
                                <small class="text-muted">
                                    {{ ucfirst(str_replace('_', ' ', $question->question_type)) }} • 
                                    <strong>{{ $question->marks }}</strong> marks
                                </small>
                            </div>
                            @if($studentAnswer)
                                <span class="badge @if($studentAnswer->is_correct) bg-success @elseif($studentAnswer->is_correct === false) bg-danger @else bg-secondary @endif">
                                    @if($studentAnswer->is_correct)
                                        <i class="bi bi-check-circle me-1"></i>Correct
                                    @elseif($studentAnswer->is_correct === false)
                                        <i class="bi bi-x-circle me-1"></i>Incorrect
                                    @else
                                        <i class="bi bi-clock me-1"></i>Pending
                                    @endif
                                </span>
                            @endif
                        </div>

                        @if($studentAnswer)
                            <!-- Student's Answer -->
                            <div class="p-2 bg-light rounded mb-2">
                                <small class="text-muted d-block mb-1">Your Answer:</small>
                                @if($question->question_type === 'multiple_choice')
                                    <strong>
                                        {{ $studentAnswer->selectedOption?->option_text ?? 'Not answered' }}
                                    </strong>
                                @elseif($question->question_type === 'true_false')
                                    <strong>
                                        {{ ucfirst($studentAnswer->answer_text ?? 'Not answered') }}
                                    </strong>
                                @else
                                    <strong>{{ $studentAnswer->answer_text ?? 'Not answered' }}</strong>
                                @endif
                            </div>

                            <!-- Marks -->
                            @if($studentAnswer->marks_obtained !== null)
                                <small class="text-muted d-block mb-2">
                                    Marks: <strong class="text-success">{{ $studentAnswer->marks_obtained }}/{{ $question->marks }}</strong>
                                </small>
                            @endif

                            <!-- Correct Answer (for auto-graded) -->
                            @if($studentAnswer->is_correct !== null && $question->isAutoGradeable())
                                <div class="p-2 bg-light rounded mb-2 border-left" style="border-left: 3px solid #28a745;">
                                    <small class="text-muted d-block mb-1">Correct Answer:</small>
                                    <strong class="text-success">
                                        @if($question->question_type === 'multiple_choice')
                                            {{ $question->options->where('is_correct', true)->first()?->option_text ?? 'N/A' }}
                                        @elseif($question->question_type === 'true_false')
                                            {{ ucfirst($question->correct_answer) }}
                                        @endif
                                    </strong>
                                </div>
                            @endif

                            <!-- Teacher Feedback -->
                            @if($studentAnswer->teacher_feedback)
                                <div class="alert alert-info small mb-0">
                                    <strong><i class="bi bi-chat-left-text me-1"></i>Teacher Feedback:</strong><br>
                                    {{ $studentAnswer->teacher_feedback }}
                                </div>
                            @endif
                        @else
                            <small class="text-muted"><i class="bi bi-dash-circle me-1"></i>Not answered</small>
                        @endif
                    </div>
                @empty
                    <p class="text-muted text-center py-4">No questions in this test.</p>
                @endforelse
            </div>
        </div>

        <!-- Back to Tests -->
        <div class="mt-4">
            <a href="{{ route('student.tests.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-1"></i> Back to Tests
            </a>
        </div>
    @else
        <div class="alert alert-warning" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>No results available</strong> — There was an issue retrieving your test results. Please contact your teacher.
        </div>
    @endif

</div>
@endsection
