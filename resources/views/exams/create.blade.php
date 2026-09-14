@extends('layouts.app')

@section('title', 'Schedule Exam')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Examinations</a></li>
    <li class="breadcrumb-item active">Schedule Exam</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-calendar-plus me-2 text-danger"></i>Schedule New Examination</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('exams.store') }}" class="needs-validation">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Exam Title <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       placeholder="e.g. Mid-Term Exam 2024" value="{{ old('name') }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Exam Type <span class="text-danger">*</span></label>
                                <select name="exam_type" class="form-select @error('exam_type') is-invalid @enderror" required>
                                    <option value="midterm" {{ old('exam_type') == 'midterm' ? 'selected' : '' }}>Mid-Term</option>
                                    <option value="final" {{ old('exam_type') == 'final' ? 'selected' : '' }}>Final Examination</option>
                                    <option value="unit_test" {{ old('exam_type') == 'unit_test' ? 'selected' : '' }}>Unit Test</option>
                                    <option value="quiz" {{ old('exam_type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                    <option value="assignment" {{ old('exam_type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Target Class <span class="text-danger">*</span></label>
                                <select name="school_class_id" class="form-select @error('school_class_id') is-invalid @enderror" required>
                                    <option value="">-- Select Class --</option>
                                    @foreach($classes as $c)
                                        <option value="{{ $c->id }}" {{ old('school_class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                                <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                    <option value="">-- Select Subject --</option>
                                    @foreach($subjects as $s)
                                        <option value="{{ $s->id }}" {{ old('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Exam Date <span class="text-danger">*</span></label>
                                <input type="date" name="exam_date" class="form-control @error('exam_date') is-invalid @enderror" 
                                       value="{{ old('exam_date', today()->addDays(7)->toDateString()) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Start Time</label>
                                <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror" 
                                       value="{{ old('start_time', '09:00') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Duration (Minutes)</label>
                                <input type="number" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', 60) }}" min="1">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Total Marks <span class="text-danger">*</span></label>
                                <input type="number" name="total_marks" class="form-control" value="{{ old('total_marks', 100) }}" required min="1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Passing Marks <span class="text-danger">*</span></label>
                                <input type="number" name="pass_marks" class="form-control" value="{{ old('pass_marks', 40) }}" required min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="scheduled" selected>Scheduled</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Instructions / Syllabus</label>
                            <textarea name="instructions" rows="3" class="form-control" placeholder="Special exam guidelines, allowed materials, or covered chapters...">{{ old('instructions') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-crimson px-4"><i class="bi bi-check2-circle me-1"></i> Save & Schedule</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
