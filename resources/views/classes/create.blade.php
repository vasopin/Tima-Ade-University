@extends('layouts.app')

@section('title', 'Add Class')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('classes.index') }}">Classes</a></li>
    <li class="breadcrumb-item active">Add Class</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-header-title mb-1">Create New Class</h3>
            <p class="text-muted small mb-0">Define class name, grade level, and default sections.</p>
        </div>
        <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-building me-2 text-danger"></i>Class Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('classes.store') }}">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label required fw-semibold">Class Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Grade 10 or Senior High A">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Grade Level</label>
                                <input type="text" name="grade_level" class="form-control" value="{{ old('grade_level') }}" placeholder="e.g. 10">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description / Notes</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Brief info about curriculum or track...">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Initial Sections</label>
                            <p class="small text-muted mb-2">Sections created automatically for this class:</p>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="text" name="sections[]" class="form-control" value="Section A" placeholder="Section Name">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="sections[]" class="form-control" value="Section B" placeholder="Section Name">
                                </div>
                            </div>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                            <label class="form-check-label fw-semibold" for="isActive">Active for current academic year</label>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('classes.index') }}" class="btn btn-light me-2">Cancel</a>
                            <button type="submit" class="btn btn-crimson px-4 fw-semibold">
                                <i class="bi bi-check-lg me-1"></i> Create Class
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
