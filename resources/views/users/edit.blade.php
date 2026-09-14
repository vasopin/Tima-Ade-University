@extends('layouts.app')

@section('title', 'Edit User Account')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit User: {{ $user->name }}</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('users.update', $user) }}" class="needs-validation" enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label small fw-semibold text-muted">FULL NAME</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="faculty_id" class="form-label small fw-semibold text-muted">DEAN FACULTY (IF APPLICABLE)</label>
                                <select name="faculty_id" id="faculty_id" class="form-select @error('faculty_id') is-invalid @enderror">
                                    <option value="">Not faculty-scoped</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}" @selected(old('faculty_id', $user->faculty_id) == $faculty->id)>{{ $faculty->name }}</option>
                                    @endforeach
                                </select>
                                @error('faculty_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label small fw-semibold text-muted">EMAIL ADDRESS</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label small fw-semibold text-muted">PHONE NUMBER</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="avatar" class="form-label small fw-semibold text-muted">PROFILE PHOTO</label>
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
                                @error('avatar')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="role_id" class="form-label small fw-semibold text-muted">ROLE</label>
                                <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label small fw-semibold text-muted">ACCOUNT STATUS</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', $user->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="pending" {{ old('status', $user->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label small fw-semibold text-muted">RESET PASSWORD (LEAVE BLANK TO KEEP CURRENT)</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="New password (optional)">
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm" style="background-color: #016ED5; border-color: #016ED5;" data-loading-text="<span class=\"spinner-border spinner-border-sm spinner-border-sm-custom\" role=\"status\" aria-hidden=\"true\"></span> Saving...">
                                <i class="bi bi-save me-1"></i> Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
