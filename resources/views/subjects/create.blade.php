@extends('layouts.app')

@section('title', 'Add Subject')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">Subjects</a></li>
    <li class="breadcrumb-item active">Add Subject</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-header-title mb-1">Create New Subject</h3>
            <p class="text-muted small mb-0">Add subject course title, syllabus code, and options.</p>
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
                    <form method="POST" action="{{ route('subjects.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label required fw-semibold">Subject Title</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Physics or World History">
                        </div>

                        <div class="mb-3">
                            <label class="form-label required fw-semibold">Subject Code</label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required placeholder="e.g. PHY101 or HIST201">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description / Syllabus</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Brief outline of syllabus...">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                            <label class="form-check-label fw-semibold" for="isActive">Subject active in syllabus</label>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('subjects.index') }}" class="btn btn-light me-2">Cancel</a>
                            <button type="submit" class="btn btn-crimson px-4 fw-semibold">
                                <i class="bi bi-check-lg me-1"></i> Save Subject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
