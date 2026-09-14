@extends('layouts.app')

@section('title', $test->title . ' — Answer Questions')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('student.tests.index') }}">Tests</a></li>
    <li class="breadcrumb-item active">{{ $test->title }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- ===== TIMER & PROGRESS BAR (Fixed Top) ===== -->
    <div class="sticky-top bg-white border-bottom p-3 mb-4 shadow-sm" style="z-index: 100;">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">{{ $test->title }}</h5>
                <small class="text-muted">{{ $test->schoolClass->name }} • {{ $test->subject->name }}</small>
            </div>
            <div class="col-md-6 text-end">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <!-- Timer -->
                    <div id="timer-container" class="p-2 rounded" style="background-color: #f8f9fa;">
                        <small class="text-muted d-block">Time Remaining</small>
                        <h5 class="mb-0" id="timer-display">--:--</h5>
                    </div>
                    
                    <!-- Submit Button -->
                    <form id="submit-form" action="{{ route('student.tests.submit', $attempt) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-lg" id="submit-btn">
                            <i class="bi bi-check-circle me-1"></i> Submit Test
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="mt-3">
            <small class="text-muted">Question Progress</small>
            <div class="progress" style="height: 8px;">
                <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <!-- ===== QUESTIONS ===== -->
    <div class="row g-3">
        <div class="col-lg-8">
            <div id="questions-container">
                @foreach($test->questions->sortBy('order_index') as $question)
                    <div class="card custom-card mb-3 question-card" data-question-id="{{ $question->test_question_id }}">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1">Question {{ $loop->iteration }} of {{ $test->questions->count() }}</h5>
                                    <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }} • <strong>{{ $question->marks }}</strong> marks</small>
                                </div>
                                <span class="badge bg-light text-dark">Q{{ $loop->iteration }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Question Text -->
                            <p class="fw-bold mb-3">{{ $question->question_text }}</p>

                            <!-- Answer Options -->
                            <div class="answer-options">
                                @if($question->question_type === 'multiple_choice')
                                    <!-- MCQ Options -->
                                    @foreach($question->options ?? [] as $option)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input answer-input" type="radio" 
                                                   name="question_{{ $question->id }}" 
                                                   value="{{ $option['id'] }}"
                                                   id="option_{{ $option['id'] }}"
                                                   data-question-id="{{ $question->test_question_id }}"
                                                   data-option-id="{{ $option['id'] }}">
                                            <label class="form-check-label" for="option_{{ $option['id'] }}">
                                                {{ $option['option_text'] }}
                                            </label>
                                        </div>
                                    @endforeach

                                @elseif($question->question_type === 'true_false')
                                    <!-- True/False Options -->
                                    <div class="form-check mb-2">
                                        <input class="form-check-input answer-input" type="radio" 
                                               name="question_{{ $question->id }}" 
                                               value="true"
                                               id="true_{{ $question->id }}"
                                               data-question-id="{{ $question->test_question_id }}"
                                               data-option-id="true">
                                        <label class="form-check-label" for="true_{{ $question->id }}">
                                            <i class="bi bi-check-circle me-1 text-success"></i>True
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input answer-input" type="radio" 
                                               name="question_{{ $question->id }}" 
                                               value="false"
                                               id="false_{{ $question->id }}"
                                               data-question-id="{{ $question->test_question_id }}"
                                               data-option-id="false">
                                        <label class="form-check-label" for="false_{{ $question->id }}">
                                            <i class="bi bi-x-circle me-1 text-danger"></i>False
                                        </label>
                                    </div>

                                @elseif($question->question_type === 'short_answer')
                                    <!-- Short Answer Text -->
                                    <textarea class="form-control answer-input mb-2" 
                                              name="question_{{ $question->id }}" 
                                              rows="4" 
                                              placeholder="Type your answer here..."
                                              data-question-id="{{ $question->test_question_id }}"
                                              data-option-id="text"></textarea>
                                    <small class="text-muted">Your answer will be reviewed by your teacher.</small>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ===== SIDEBAR: QUESTION NAVIGATOR & STATUS ===== -->
        <div class="col-lg-4">
            <div class="card custom-card sticky-top" style="top: 200px;">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0"><i class="bi bi-list me-2"></i>Question Navigator</h6>
                </div>
                <div class="card-body">
                    <div class="question-navigator mb-3">
                        @foreach($test->questions as $qIndex => $q)
                            <a href="#question-{{ $q->test_question_id }}" 
                               class="question-nav-item d-inline-flex align-items-center justify-content-center rounded"
                               data-question-id="{{ $q->test_question_id }}"
                               title="Question {{ $qIndex + 1 }}">
                                {{ $qIndex + 1 }}
                            </a>
                        @endforeach
                    </div>
                    <style>
                        .question-nav-item {
                            width: 40px;
                            height: 40px;
                            margin: 5px;
                            background-color: #f0f0f0;
                            border: 2px solid #ddd;
                            cursor: pointer;
                            font-weight: bold;
                            text-decoration: none;
                            color: #333;
                            transition: all 0.3s;
                        }
                        .question-nav-item:hover {
                            background-color: #e9e9e9;
                            border-color: #016ED5;
                        }
                        .question-nav-item.answered {
                            background-color: #d4edda;
                            border-color: #28a745;
                            color: #155724;
                        }
                        .question-nav-item.current {
                            background-color: #016ED5;
                            border-color: #016ED5;
                            color: white;
                        }
                    </style>

                    <hr class="my-3">
                    
                    <h6 class="fw-bold small mb-2"><i class="bi bi-info-circle me-1"></i>Instructions</h6>
                    <small class="text-muted d-block">
                        • Answers are saved automatically<br>
                        • You can review and change answers<br>
                        • Click "Submit Test" when finished<br>
                        • Test will auto-submit when time expires
                    </small>

                    <hr class="my-3">

                    <div class="alert alert-info small" role="alert">
                        <strong><i class="bi bi-clock me-1"></i>Time Expires:</strong> <span id="time-expires">--:--</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ===== SCRIPTS ===== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Timer Setup
    const testDuration = {{ $test->duration_minutes }};
    const startedAt = new Date('{{ $attempt->started_at }}');
    const durationMs = testDuration * 60 * 1000;

    function updateTimer() {
        const now = new Date();
        const elapsed = now - startedAt;
        const remaining = durationMs - elapsed;

        if (remaining <= 0) {
            document.getElementById('timer-display').textContent = '00:00';
            document.getElementById('submit-form').submit();
            return;
        }

        const minutes = Math.floor(remaining / 60000);
        const seconds = Math.floor((remaining % 60000) / 1000);
        document.getElementById('timer-display').textContent = 
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

        // Update expiry time
        const expiryTime = new Date(startedAt.getTime() + durationMs);
        document.getElementById('time-expires').textContent = expiryTime.toLocaleTimeString();

        // Change color when time is low
        const timerContainer = document.getElementById('timer-container');
        if (remaining < 300000) { // 5 minutes
            timerContainer.classList.add('border', 'border-danger');
        }
    }

    updateTimer();
    setInterval(updateTimer, 1000);

    // Auto-save answers
    document.querySelectorAll('.answer-input').forEach(input => {
        input.addEventListener('change', function() {
            saveAnswer(this.dataset.questionId, this.value);
            updateNavigator();
            updateProgressBar();
        });

        input.addEventListener('input', function() {
            if (this.tagName === 'TEXTAREA') {
                saveAnswer(this.dataset.questionId, this.value);
                updateProgressBar();
            }
        });
    });

    function saveAnswer(questionId, answer) {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('question_id', questionId);
        formData.append('answer', answer);

        fetch('{{ route("student.tests.save-answer", $attempt) }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Answer saved for question ' + questionId);
            }
        })
        .catch(error => console.error('Error saving answer:', error));
    }

    function updateNavigator() {
        const totalQuestions = {{ $test->questions->count() }};
        let answered = 0;

        document.querySelectorAll('.answer-input:checked').forEach(() => answered++);
        document.querySelectorAll('textarea').forEach(ta => {
            if (ta.value.trim()) answered++;
        });

        document.querySelectorAll('.question-nav-item').forEach((item, index) => {
            const questionCard = document.querySelector(`[data-question-id="${item.dataset.questionId}"]`);
            const hasAnswer = questionCard.querySelector('input:checked') || 
                            (questionCard.querySelector('textarea')?.value.trim());
            
            if (hasAnswer) {
                item.classList.add('answered');
            } else {
                item.classList.remove('answered');
            }
        });
    }

    function updateProgressBar() {
        const totalQuestions = {{ $test->questions->count() }};
        let answered = 0;

        document.querySelectorAll('.answer-input:checked, textarea').forEach(input => {
            if (input.value.trim()) answered++;
        });

        const progress = (answered / totalQuestions) * 100;
        document.getElementById('progress-bar').style.width = progress + '%';
    }

    // Initialize
    updateNavigator();
    updateProgressBar();
});
</script>

<style>
    .question-card {
        scroll-margin-top: 250px;
    }
</style>
@endsection
