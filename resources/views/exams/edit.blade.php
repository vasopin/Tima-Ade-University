@extends('layouts.app')

@section('title', 'Edit Exam — ' . $exam->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Examinations</a></li>
    <li class="breadcrumb-item active">Edit Exam</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Examination</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('exams.update', $exam) }}" class="needs-validation">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Exam Title <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $exam->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Exam Type <span class="text-danger">*</span></label>
                                <select name="exam_type" class="form-select" required>
                                    <option value="midterm" {{ old('exam_type', $exam->exam_type) == 'midterm' ? 'selected' : '' }}>Mid-Term</option>
                                    <option value="final" {{ old('exam_type', $exam->exam_type) == 'final' ? 'selected' : '' }}>Final Examination</option>
                                    <option value="unit_test" {{ old('exam_type', $exam->exam_type) == 'unit_test' ? 'selected' : '' }}>Unit Test</option>
                                    <option value="quiz" {{ old('exam_type', $exam->exam_type) == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                    <option value="assignment" {{ old('exam_type', $exam->exam_type) == 'assignment' ? 'selected' : '' }}>Assignment</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Target Class <span class="text-danger">*</span></label>
                                <select name="school_class_id" class="form-select" required>
                                    @foreach($classes as $c)
                                        <option value="{{ $c->id }}" {{ old('school_class_id', $exam->school_class_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                                <select name="subject_id" class="form-select" required>
                                    @foreach($subjects as $s)
                                        <option value="{{ $s->id }}" {{ old('subject_id', $exam->subject_id) == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Exam Date <span class="text-danger">*</span></label>
                                <input type="date" name="exam_date" class="form-control" 
                                       value="{{ old('exam_date', $exam->exam_date->toDateString()) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Start Time</label>
                                <input type="time" name="start_time" class="form-control" 
                                       value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('H:i') : '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Duration (Minutes)</label>
                                <input type="number" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="1">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Total Marks <span class="text-danger">*</span></label>
                                <input type="number" name="total_marks" class="form-control" value="{{ old('total_marks', $exam->total_marks) }}" required min="1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Passing Marks <span class="text-danger">*</span></label>
                                <input type="number" name="pass_marks" class="form-control" value="{{ old('pass_marks', $exam->pass_marks) }}" required min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="scheduled" {{ old('status', $exam->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="ongoing" {{ old('status', $exam->status) == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="completed" {{ old('status', $exam->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $exam->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Instructions / Syllabus</label>
                            <textarea name="instructions" rows="3" class="form-control">{{ old('instructions', $exam->instructions) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check2-circle me-1"></i> Update Exam</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
