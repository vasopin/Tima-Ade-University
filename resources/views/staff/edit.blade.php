@extends('layouts.app')

@section('title', 'Edit Staff')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff.index') }}">Staff Management</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Staff</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: #1F2937;">Edit Staff Member</h3>
            <p class="text-muted small mb-0">Update {{ $user->name }}'s staff profile and account access.</p>
        </div>
        <a href="{{ route('staff.show', $user) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back to Profile</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-4 border-0 shadow-sm"><ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('staff.update', $user) }}">
                @csrf
                @method('PUT')
                <div class="row g-4">
                    <div class="col-md-6"><label for="name" class="form-label fw-semibold">Full Name</label><input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required></div>
                    <div class="col-md-6"><label for="email" class="form-label fw-semibold">Email Address</label><input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required></div>
                    <div class="col-md-6"><label for="phone" class="form-label fw-semibold">Phone Number</label><input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold">Module Role</label><input type="text" class="form-control" value="{{ $user->role->name ?? 'Staff' }}" disabled><div class="form-text">Staff roles are managed in Staff Management.</div></div>
                    <div class="col-md-6"><label for="status" class="form-label fw-semibold">Account Status</label><select name="status" id="status" class="form-select" required><option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option><option value="pending" {{ old('status', $user->status) === 'pending' ? 'selected' : '' }}>Pending</option><option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option><option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Suspended</option></select></div>
                    <div class="col-md-6"><label for="password" class="form-label fw-semibold">New Password</label><input type="password" class="form-control" id="password" name="password" minlength="8" placeholder="Leave blank to keep current"><div class="form-text">Minimum 8 characters when changing it.</div></div>
                </div>
                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2"><a href="{{ route('staff.show', $user) }}" class="btn btn-outline-secondary">Cancel</a><button type="submit" class="btn btn-primary" style="background-color: #016ED5; border-color: #016ED5;"><i class="bi bi-check-circle me-1"></i> Save Staff Changes</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
