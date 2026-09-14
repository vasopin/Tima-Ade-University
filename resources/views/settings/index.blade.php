@extends('layouts.app')

@section('title', 'School Settings')

@section('breadcrumb')
    <li class="breadcrumb-item">Administration</li>
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title mb-1">Institution Settings & Configuration</h3>
            <p class="text-muted small mb-0">Manage global school information, contact details, grading policy, and portal configurations.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf

        <div class="row g-4">
            <!-- General Settings -->
            <div class="col-lg-6">
                <div class="card custom-card h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0"><i class="bi bi-mortarboard-fill me-2 text-danger"></i>General Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">School Name</label>
                            <input type="text" name="school_name" class="form-control" 
                                   value="{{ $settings['school_name']->value ?? 'Tima-Ade University' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Motto / Tagline</label>
                            <input type="text" name="school_tagline" class="form-control" 
                                   value="{{ $settings['school_tagline']->value ?? 'Excellence in Education Since 1990' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Active Academic Year</label>
                            <input type="text" name="academic_year" class="form-control" 
                                   value="{{ $settings['academic_year']->value ?? '2024-2025' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Public Website URL</label>
                            <input type="url" name="school_website" class="form-control" 
                                   value="{{ $settings['school_website']->value ?? 'http://localhost/Tima-Ade-University/public' }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Settings -->
            <div class="col-lg-6">
                <div class="card custom-card h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0"><i class="bi bi-telephone-fill me-2 text-primary"></i>Official Contact Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Official Contact Email</label>
                            <input type="email" name="school_email" class="form-control" 
                                   value="{{ $settings['school_email']->value ?? 'info@timaade.edu' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="school_phone" class="form-control" 
                                   value="{{ $settings['school_phone']->value ?? '+1 (555) 123-4567' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Campus Address</label>
                            <textarea name="school_address" rows="3" class="form-control">{{ $settings['school_address']->value ?? 'Tima-Ade University Campus, Main Road, Tima-Ade City' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Default Passing Mark (%)</label>
                            <input type="number" name="passing_marks" class="form-control" 
                                   value="{{ $settings['passing_marks']->value ?? 40 }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-crimson px-5 py-2 shadow-sm">
                <i class="bi bi-check2-circle me-1"></i> Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
