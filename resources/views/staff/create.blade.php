@extends('layouts.app')

@section('title', 'Add Staff Member')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff.index') }}">Staff Management</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add Staff Member</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: #1F2937;">Add Staff Member</h3>
            <p class="text-muted small mb-0">Create a new staff account using the existing role-based user management workflow.</p>
        </div>
        <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Staff Management
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-4 border-0 shadow-sm">
            <div class="fw-semibold mb-2">Please correct the following:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('staff.store') }}" method="POST">
                @csrf

                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Jane Doe" required>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="name@school.com" required>
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="Optional">
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold">Account Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label for="password" class="form-label fw-semibold">Temporary Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Minimum 8 characters" required>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2 justify-content-end">
                    <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary" style="background-color: #016ED5; border-color: #016ED5;">
                        <i class="bi bi-check-circle me-1"></i> Create Staff Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
