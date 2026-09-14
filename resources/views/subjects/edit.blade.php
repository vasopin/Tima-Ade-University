@extends('layouts.app')

@section('title', 'Edit Subject')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">Subjects</a></li>
    <li class="breadcrumb-item active">Edit {{ $subject->name }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-header-title mb-1">Edit Subject: {{ $subject->name }}</h3>
            <p class="text-muted small mb-0">Modify subject name, course code, and curriculum description.</p>
        </div>
        <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-book me-2 text-danger"></i>Subject Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('subjects.update', $subject) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label required fw-semibold">Subject Title</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $subject->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required fw-semibold">Subject Code</label>
                            <input type="text" name="code" class="form-control" value="{{ old('code', $subject->code) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description / Syllabus</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $subject->description) }}</textarea>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', $subject->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isActive">Subject active in syllabus</label>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('subjects.index') }}" class="btn btn-light me-2">Cancel</a>
                            <button type="submit" class="btn btn-crimson px-4 fw-semibold">
                                <i class="bi bi-check-lg me-1"></i> Update Subject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
