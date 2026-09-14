@extends('layouts.app')

@section('title', 'My Profile & Account Settings')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Profile</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="row g-4">
        <!-- User Summary Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header border-0 text-center py-4" style="background: linear-gradient(135deg, #016ED5, #014B92); color: white;">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle shadow border border-3 border-white" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <div class="d-flex justify-content-center gap-2 align-items-center">
                        <span class="badge rounded-pill bg-light text-dark px-3 py-1 fw-semibold">
                            {{ $user->role->name ?? 'User' }}
                        </span>
                        {!! $user->status_badge !!}
                    </div>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-bottom">
                            <span class="text-muted small"><i class="bi bi-envelope me-2 text-primary"></i>Email</span>
                            <span class="fw-semibold text-dark">{{ $user->email }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-bottom">
                            <span class="text-muted small"><i class="bi bi-telephone me-2 text-success"></i>Phone</span>
                            <span class="fw-semibold text-dark">{{ $user->phone ?? 'Not Set' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-bottom">
                            <span class="text-muted small"><i class="bi bi-calendar3 me-2 text-warning"></i>Member Since</span>
                            <span class="fw-semibold text-dark">{{ $user->created_at->format('M d, Y') }}</span>
                        </li>
                    </ul>

                    {{-- Role-specific details --}}
                    @if($user->isStudent() && $user->student)
                        <div class="mt-3 p-3 bg-light rounded-3">
                            <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-mortarboard me-1 text-primary"></i> Academic Details</h6>
                            <div class="small text-muted d-flex justify-content-between mb-1">
                                <span>Class:</span>
                                <span class="fw-semibold text-dark">{{ $user->student->schoolClass->name ?? 'N/A' }} ({{ $user->student->section->name ?? 'N/A' }})</span>
                            </div>
                            <div class="small text-muted d-flex justify-content-between mb-1">
                                <span>Roll Number:</span>
                                <span class="fw-semibold text-dark">{{ $user->student->roll_number }}</span>
                            </div>
                            <div class="small text-muted d-flex justify-content-between">
                                <span>Admission No:</span>
                                <span class="fw-semibold text-dark">{{ $user->student->admission_number }}</span>
                            </div>
                        </div>
                    @elseif($user->isTeacher() && $user->teacher)
                        <div class="mt-3 p-3 bg-light rounded-3">
                            <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-briefcase me-1 text-success"></i> Faculty Details</h6>
                            <div class="small text-muted d-flex justify-content-between mb-1">
                                <span>Employee ID:</span>
                                <span class="fw-semibold text-dark">{{ $user->teacher->employee_id }}</span>
                            </div>
                            <div class="small text-muted d-flex justify-content-between mb-1">
                                <span>Specialization:</span>
                                <span class="fw-semibold text-dark">{{ $user->teacher->specialization ?? 'General' }}</span>
                            </div>
                            <div class="small text-muted d-flex justify-content-between">
                                <span>Qualification:</span>
                                <span class="fw-semibold text-dark">{{ $user->teacher->qualification ?? 'Faculty' }}</span>
                            </div>
                        </div>
                    @elseif($user->isParent() && $user->guardian)
                        <div class="mt-3 p-3 bg-light rounded-3">
                            <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-people me-1 text-warning"></i> Guardian Details</h6>
                            <div class="small text-muted d-flex justify-content-between mb-1">
                                <span>Relationship:</span>
                                <span class="fw-semibold text-dark">{{ ucfirst($user->guardian->relationship ?? 'Parent') }}</span>
                            </div>
                            <div class="small text-muted d-flex justify-content-between">
                                <span>Linked Scholars:</span>
                                <span class="badge bg-primary rounded-pill">{{ $user->guardian->students->count() }} Children</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Edit Profile & Password Tabs -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3">
                    <ul class="nav nav-pills card-header-pills" id="profileTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-semibold" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">
                                <i class="bi bi-person-gear me-1"></i> Personal Information
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                                <i class="bi bi-shield-lock me-1"></i> Change Password
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="profileTabContent">
                        <!-- Personal Info Form -->
                        <div class="tab-pane fade show active" id="personal" role="tabpanel">
                            @if($user->isAdmin())
                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label small fw-semibold text-muted">FULL NAME</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-person text-secondary"></i></span>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        </div>
                                        @error('name')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label small fw-semibold text-muted">EMAIL ADDRESS</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-envelope text-secondary"></i></span>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        </div>
                                        @error('email')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="phone" class="form-label small fw-semibold text-muted">PHONE NUMBER</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-telephone text-secondary"></i></span>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+1-555-0000">
                                        </div>
                                        @error('phone')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="avatar" class="form-label small fw-semibold text-muted">PROFILE PHOTO</label>
                                        <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
                                        <div class="form-text small">Max 2MB (JPG, PNG, WebP)</div>
                                        @error('avatar')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm" style="background-color: #016ED5; border-color: #016ED5;">
                                        <i class="bi bi-save me-1"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                            @else
                            <div class="alert alert-light border mb-0" role="status">
                                <i class="bi bi-lock me-2 text-secondary" aria-hidden="true"></i>
                                Personal information is managed by an administrator. You can view it here and change your password from the security tab.
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <label for="readonly-name" class="form-label small fw-semibold text-muted">FULL NAME</label>
                                    <input id="readonly-name" type="text" class="form-control" value="{{ $user->name }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="readonly-email" class="form-label small fw-semibold text-muted">EMAIL ADDRESS</label>
                                    <input id="readonly-email" type="email" class="form-control" value="{{ $user->email }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="readonly-phone" class="form-label small fw-semibold text-muted">PHONE NUMBER</label>
                                    <input id="readonly-phone" type="text" class="form-control" value="{{ $user->phone ?? 'Not Set' }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted">PROFILE PHOTO</label>
                                    <div class="form-control bg-light text-muted">Managed by an administrator</div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Change Password Form -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <form method="POST" action="{{ route('profile.password') }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="current_password" class="form-label small fw-semibold text-muted">CURRENT PASSWORD</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-key text-secondary"></i></span>
                                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required placeholder="••••••••">
                                    </div>
                                    @error('current_password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="new_password" class="form-label small fw-semibold text-muted">NEW PASSWORD (MIN 8 CHARS)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-lock text-secondary"></i></span>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="new_password" name="password" required placeholder="••••••••">
                                        </div>
                                        @error('password')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label small fw-semibold text-muted">CONFIRM NEW PASSWORD</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-lock-fill text-secondary"></i></span>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="••••••••">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-warning px-4 fw-semibold shadow-sm text-dark" style="background-color: #FB8B01; border-color: #FB8B01;">
                                        <i class="bi bi-shield-check me-1"></i> Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
