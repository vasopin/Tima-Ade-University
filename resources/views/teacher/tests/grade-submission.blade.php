@extends('layouts.app')

@section('title', 'Grade Submission — ' . $attempt->student->user->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher.tests.index') }}">Tests</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher.tests.submissions', $attempt->test) }}">Submissions</a></li>
    <li class="breadcrumb-item active">Grade</li>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- ===== PAGE HEADER ===== -->
    <div class="welcome-banner mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="welcome-title mb-1">Grade Submission</h2>
                <p class="welcome-text mb-0">
                    <strong>{{ $attempt->student->user->name }}</strong> | 
                    <i class="bi bi-file me-1"></i>{{ $attempt->test->title }}
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('teacher.tests.submissions', $attempt->test) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Submissions
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <!-- Questions Review -->
            <div id="questions-container">
                @php $shortAnswerCount = 0; @endphp
                
                @foreach($attempt->test->questions->sortBy('order_index') as $question)
                    @php
                        $answer = $attempt->answers->where('test_question_id', $question->id)->first();
                        if ($question->question_type === 'short_answer') {
                            $shortAnswerCount++;
                        }
                    @endphp

                    <div class="card custom-card mb-3">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1">Question {{ $loop->iteration }}</h5>
                                    <small class="text-muted">
                                        {{ ucfirst(str_replace('_', ' ', $question->question_type)) }} • 
                                        <strong>{{ $question->marks }}</strong> marks
                                    </small>
                                </div>
                                <span class="badge @if($question->question_type === 'short_answer') bg-warning @else bg-success @endif">
                                    @if($question->question_type === 'short_answer')
                                        Needs Review
                                    @else
                                        Auto-graded
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Question Text -->
                            <p class="fw-bold mb-3">{{ $question->question_text }}</p>

                            @if(!$answer)
                                <div class="alert alert-info small mb-3">
                                    <i class="bi bi-dash-circle me-1"></i>Not answered
                                </div>
                            @else
                                <!-- Student's Answer -->
                                <div class="p-3 bg-light rounded mb-3 border-left" style="border-left: 3px solid #016ED5;">
                                    <small class="text-muted d-block mb-1">Student's Answer:</small>
                                    @if($question->question_type === 'multiple_choice')
                                        <strong>{{ $answer->selectedOption?->option_text ?? 'N/A' }}</strong>
                                    @elseif($question->question_type === 'true_false')
                                        <strong>{{ ucfirst($answer->answer_text) }}</strong>
                                    @else
                                        <strong>{{ $answer->answer_text }}</strong>
                                    @endif
                                </div>

                                @if($question->question_type === 'short_answer')
                                    <!-- Grading Form for Short Answer -->
                                    <form action="{{ route('teacher.tests.save-grade', $answer) }}" method="POST" class="grading-form">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="marks_{{ $answer->id }}" class="form-label fw-bold">
                                                Enter Marks (0-{{ $question->marks }})
                                            </label>
                                            <input type="number" name="marks_obtained" id="marks_{{ $answer->id }}" 
                                                   class="form-control" 
                                                   value="{{ $answer->marks_obtained ?? 0 }}"
                                                   min="0" 
                                                   max="{{ $question->marks }}"
                                                   required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="feedback_{{ $answer->id }}" class="form-label fw-bold">
                                                Feedback (Optional)
                                            </label>
                                            <textarea name="teacher_feedback" id="feedback_{{ $answer->id }}" 
                                                      class="form-control" rows="3" 
                                                      placeholder="Provide constructive feedback...">{{ $answer->teacher_feedback }}</textarea>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="bi bi-check-circle me-1"></i> Save Grade
                                        </button>
                                    </form>
                                @else
                                    <!-- Auto-graded Display -->
                                    <div class="p-3 bg-light rounded mb-3">
                                        <small class="text-muted d-block mb-2">Correct Answer:</small>
                                        <strong class="text-success">
                                            @if($question->question_type === 'multiple_choice')
                                                {{ $question->options->where('is_correct', true)->first()?->option_text }}
                                            @else
                                                {{ ucfirst($question->correct_answer) }}
                                            @endif
                                        </strong>
                                    </div>

                                    <div class="alert @if($answer->is_correct) alert-success @else alert-danger @endif small">
                                        <i class="bi bi-@if($answer->is_correct)check-circle @else x-circle @endif me-1"></i>
                                        <strong>
                                            @if($answer->is_correct)
                                                Correct - {{ $answer->marks_obtained }}/{{ $question->marks }} marks
                                            @else
                                                Incorrect - 0/{{ $question->marks }} marks
                                            @endif
                                        </strong>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Finalize Grading -->
            @if($shortAnswerCount > 0 || $attempt->status !== 'graded')
                <div class="card custom-card">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0"><i class="bi bi-check-all me-2"></i>Finalize Grading</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Once all short-answer questions are graded, finalize the submission.</p>
                        
                        @php
                            $ungraded = $attempt->answers->whereNull('marks_obtained')->count();
                        @endphp

                        @if($ungraded > 0)
                            <div class="alert alert-warning small">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                {{ $ungraded }} question(s) still need grading.
                            </div>
                        @else
                            <form action="{{ route('teacher.tests.finalize-grade', $attempt) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i> Finalize & Calculate Score
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- ===== SIDEBAR ===== -->
        <div class="col-lg-4">
            <div class="card custom-card sticky-top" style="top: 100px;">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Grading Summary</h6>
                </div>
                <div class="card-body small">
                    <div class="mb-3">
                        <small class="text-muted d-block">Student</small>
                        <h6 class="fw-bold">{{ $attempt->student->user->name }}</h6>
                        <small class="text-muted">Roll: {{ $attempt->student->roll_number }}</small>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted d-block">Submission Status</small>
                        <h6 class="fw-bold">
                            @if($attempt->status === 'submitted')
                                <span class="badge bg-warning text-dark">Pending Review</span>
                            @elseif($attempt->status === 'graded')
                                <span class="badge bg-info">Graded</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($attempt->status) }}</span>
                            @endif
                        </h6>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <small class="text-muted d-block">Test Details</small>
                        <div class="small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Total Marks</span>
                                <strong>{{ $attempt->test->total_marks }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Pass Marks</span>
                                <strong>{{ $attempt->test->pass_marks }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Questions</span>
                                <strong>{{ $attempt->test->questions->count() }}</strong>
                            </div>
                        </div>
                    </div>

                    <hr>

                    @php
                        $gradedAnswers = $attempt->answers->where('marks_obtained', '!=', null)->count();
                        $totalAnswered = $attempt->answers->count();
                    @endphp

                    <div class="mb-3">
                        <small class="text-muted d-block">Grading Progress</small>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $totalAnswered > 0 ? ($gradedAnswers / $totalAnswered) * 100 : 0 }}%"></div>
                        </div>
                        <small>{{ $gradedAnswers }}/{{ $totalAnswered }} questions graded</small>
                    </div>

                    <div class="alert alert-info small" role="alert">
                        <i class="bi bi-lightbulb me-1"></i>
                        <strong>Tip:</strong> Grade all short-answer questions, then click "Finalize" to calculate the total score.
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
