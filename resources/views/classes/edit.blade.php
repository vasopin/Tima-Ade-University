@extends('layouts.app')

@section('title', 'Edit Class')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('classes.index') }}">Classes</a></li>
    <li class="breadcrumb-item active">Edit {{ $class->name }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-header-title mb-1">Edit Class: {{ $class->name }}</h3>
            <p class="text-muted small mb-0">Modify class designation, grade level, and settings.</p>
        </div>
        <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-building me-2 text-danger"></i>Class Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('classes.update', $class) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label required fw-semibold">Class Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $class->name) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Grade Level</label>
                                <input type="text" name="grade_level" class="form-control" value="{{ old('grade_level', $class->grade_level) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description / Notes</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $class->description) }}</textarea>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', $class->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isActive">Active for current academic year</label>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('classes.index') }}" class="btn btn-light me-2">Cancel</a>
                            <button type="submit" class="btn btn-crimson px-4 fw-semibold">
                                <i class="bi bi-check-lg me-1"></i> Update Class
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
