@extends('layouts.app')

@section('title', 'Create Test — Teacher Portal')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('teacher.tests.index') }}">Tests</a></li>
    <li class="breadcrumb-item active">Create Test</li>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- ===== PAGE HEADER ===== -->
    <div class="welcome-banner mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="welcome-title mb-1">Create New Test</h2>
                <p class="welcome-text mb-0">Define test details, questions, and schedule.</p>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <form action="{{ route('teacher.tests.store') }}" method="POST" id="create-test-form">
                @csrf

                <!-- Test Details Card -->
                <div class="card custom-card mb-3">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0"><i class="bi bi-file-text me-2"></i>Test Details</h5>
                    </div>
                    <div class="card-body">
                        <!-- Course Selection -->
                        <div class="mb-3">
                            <label for="school_class_id" class="form-label fw-bold">
                                <i class="bi bi-building me-1"></i>Class / Course <span class="text-danger">*</span>
                            </label>
                            <select name="school_class_id" id="school_class_id" class="form-control @error('school_class_id') is-invalid @enderror" required>
                                <option value="">-- Select a class --</option>
                                @foreach($authorizedClasses as $class)
                                    <option value="{{ $class->id }}" @if(old('school_class_id') == $class->id) selected @endif>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('school_class_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Subject Selection -->
                        <div class="mb-3">
                            <label for="subject_id" class="form-label fw-bold">
                                <i class="bi bi-book me-1"></i>Subject <span class="text-danger">*</span>
                            </label>
                            <select name="subject_id" id="subject_id" class="form-control @error('subject_id') is-invalid @enderror" required>
                                <option value="">-- Select subject for this class --</option>
                            </select>
                            @error('subject_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">Select the class first to populate available subjects.</small>
                        </div>

                        <!-- Test Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">
                                <i class="bi bi-pencil-square me-1"></i>Test Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                                   placeholder="e.g., Chapter 1-3 Quiz, Midterm Exam" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Instructions -->
                        <div class="mb-3">
                            <label for="instructions" class="form-label fw-bold">
                                <i class="bi bi-info-circle me-1"></i>Instructions
                            </label>
                            <textarea name="instructions" id="instructions" class="form-control" rows="3" 
                                      placeholder="Provide instructions for students taking this test.">{{ old('instructions') }}</textarea>
                            <small class="text-muted d-block mt-1">Students will see these before starting the test.</small>
                        </div>
                    </div>
                </div>

                <!-- Marks & Duration Card -->
                <div class="card custom-card mb-3">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0"><i class="bi bi-speedometer2 me-2"></i>Marks & Duration</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="total_marks" class="form-label fw-bold">
                                        <i class="bi bi-percent me-1"></i>Total Marks <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="total_marks" id="total_marks" class="form-control @error('total_marks') is-invalid @enderror" 
                                           value="{{ old('total_marks', 100) }}" min="1" required>
                                    @error('total_marks')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="pass_marks" class="form-label fw-bold">
                                        <i class="bi bi-check-circle me-1"></i>Pass Marks <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="pass_marks" id="pass_marks" class="form-control @error('pass_marks') is-invalid @enderror" 
                                           value="{{ old('pass_marks', 40) }}" min="0" required>
                                    @error('pass_marks')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration_minutes" class="form-label fw-bold">
                                        <i class="bi bi-clock me-1"></i>Duration (minutes) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="duration_minutes" id="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                           value="{{ old('duration_minutes', 60) }}" min="1" required>
                                    @error('duration_minutes')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="attempt_limit" class="form-label fw-bold">
                                        <i class="bi bi-arrow-repeat me-1"></i>Attempt Limit
                                    </label>
                                    <input type="number" name="attempt_limit" id="attempt_limit" class="form-control @error('attempt_limit') is-invalid @enderror" 
                                           value="{{ old('attempt_limit', 1) }}" min="1">
                                    <small class="text-muted d-block mt-1">How many times can a student attempt this test?</small>
                                    @error('attempt_limit')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Schedule Card -->
                <div class="card custom-card mb-3">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0"><i class="bi bi-calendar3 me-2"></i>Schedule</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="publish_date" class="form-label fw-bold">
                                <i class="bi bi-unlock me-1"></i>Publish Date & Time
                            </label>
                            <input type="datetime-local" name="publish_date" id="publish_date" class="form-control @error('publish_date') is-invalid @enderror" 
                                   value="{{ old('publish_date', now()->format('Y-m-d\TH:i')) }}">
                            <small class="text-muted d-block mt-1">When students can start taking the test</small>
                            @error('publish_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="close_date" class="form-label fw-bold">
                                <i class="bi bi-lock me-1"></i>Close Date & Time
                            </label>
                            <input type="datetime-local" name="close_date" id="close_date" class="form-control @error('close_date') is-invalid @enderror" 
                                   value="{{ old('close_date', now()->addHours(1)->format('Y-m-d\TH:i')) }}">
                            <small class="text-muted d-block mt-1">When the test becomes unavailable for new attempts</small>
                            @error('close_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="results_release_date" class="form-label fw-bold">
                                <i class="bi bi-eye me-1"></i>Results Release Date & Time
                            </label>
                            <input type="datetime-local" name="results_release_date" id="results_release_date" class="form-control @error('results_release_date') is-invalid @enderror" 
                                   value="{{ old('results_release_date', now()->addDays(3)->format('Y-m-d\TH:i')) }}">
                            <small class="text-muted d-block mt-1">When students can view their results</small>
                            @error('results_release_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 mb-4">
                    <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">
                        <i class="bi bi-pencil-square me-1"></i> Save as Draft
                    </button>
                    <button type="submit" name="action" value="publish" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Create & Publish
                    </button>
                    <a href="{{ route('teacher.tests.index') }}" class="btn btn-link">Cancel</a>
                </div>
            </form>
        </div>

        <!-- ===== HELP SIDEBAR ===== -->
        <div class="col-lg-4">
            <div class="card custom-card sticky-top" style="top: 100px;">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0"><i class="bi bi-lightbulb me-2 text-warning"></i>Tips</h6>
                </div>
                <div class="card-body small text-muted">
                    <h6 class="fw-bold text-dark mb-2">Best Practices</h6>
                    <ul class="ps-3 mb-3">
                        <li class="mb-2">Save as draft first to create questions</li>
                        <li class="mb-2">Use clear, unambiguous question text</li>
                        <li class="mb-2">Set realistic pass marks (typically 40%)</li>
                        <li class="mb-2">Allow sufficient time for the test duration</li>
                        <li class="mb-2">Release results at least 1 day after test closes</li>
                    </ul>

                    <h6 class="fw-bold text-dark mb-2">Question Types</h6>
                    <ul class="ps-3">
                        <li class="mb-2"><strong>Multiple Choice:</strong> Auto-graded</li>
                        <li class="mb-2"><strong>True/False:</strong> Auto-graded</li>
                        <li class="mb-2"><strong>Short Answer:</strong> Manual grading</li>
                    </ul>

                    <div class="alert alert-info small mt-3">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Next Step:</strong> After creating the test, you'll be able to add questions.
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
// Populate subjects based on selected class
document.getElementById('school_class_id').addEventListener('change', function() {
    const classId = this.value;
    const subjectSelect = document.getElementById('subject_id');
    
    if (!classId) {
        subjectSelect.innerHTML = '<option value="">-- Select subject for this class --</option>';
        return;
    }

    // Fetch subjects for this class (would require an API endpoint in production)
    // For now, we'll show all subjects
    const allSubjects = {!! json_encode($allSubjects) !!};
    let options = '<option value="">-- Select subject --</option>';
    
    allSubjects.forEach(subject => {
        options += `<option value="${subject.id}">${subject.name}</option>`;
    });
    
    subjectSelect.innerHTML = options;
});
</script>
@endsection
