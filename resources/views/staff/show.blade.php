@extends('layouts.app')

@section('title', 'Staff Profile')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff.index') }}">Staff Management</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: #1F2937;">Staff Profile</h3>
            <p class="text-muted small mb-0">Review staff identity, account status, and access details.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Staff Management</a>
            <a href="{{ route('staff.edit', $user) }}" class="btn btn-primary" style="background-color: #016ED5; border-color: #016ED5;"><i class="bi bi-pencil-square me-1"></i> Edit Staff</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle shadow-sm mb-3" style="width: 104px; height: 104px; object-fit: cover;">
                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->role->name ?? 'Staff' }}</p>
                    {!! $user->status_badge !!}
                    <hr>
                    <div class="text-start small text-muted">Staff account created</div>
                    <div class="text-start fw-semibold">{{ $user->created_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3"><h5 class="fw-bold mb-0">Account Details</h5></div>
                <div class="card-body p-4">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted fw-normal mb-3"><i class="bi bi-envelope me-2 text-primary"></i>Email address</dt>
                        <dd class="col-sm-8 fw-semibold mb-3">{{ $user->email }}</dd>
                        <dt class="col-sm-4 text-muted fw-normal mb-3"><i class="bi bi-telephone me-2 text-success"></i>Phone number</dt>
                        <dd class="col-sm-8 fw-semibold mb-3">{{ $user->phone ?? 'Not set' }}</dd>
                        <dt class="col-sm-4 text-muted fw-normal mb-3"><i class="bi bi-shield-check me-2 text-warning"></i>Access area</dt>
                        <dd class="col-sm-8 fw-semibold mb-3">University administration</dd>
                        <dt class="col-sm-4 text-muted fw-normal"><i class="bi bi-clock-history me-2 text-info"></i>Last updated</dt>
                        <dd class="col-sm-8 fw-semibold">{{ $user->updated_at->diffForHumans() }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
