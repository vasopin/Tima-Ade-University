@extends('layouts.app')

@section('title', 'User Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header border-0 text-center py-4" style="background: linear-gradient(135deg, #016ED5, #014B92); color: white;">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle shadow border border-3 border-white mb-2" style="width: 90px; height: 90px; object-fit: cover;">
                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <div class="d-flex justify-content-center gap-2 align-items-center">
                        <span class="badge rounded-pill bg-light text-dark px-3 py-1 fw-semibold">
                            {{ $user->role->name ?? 'User' }}
                        </span>
                        {!! $user->status_badge !!}
                    </div>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted"><i class="bi bi-envelope me-2 text-primary"></i>Email</span>
                            <span class="fw-semibold">{{ $user->email }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted"><i class="bi bi-telephone me-2 text-success"></i>Phone</span>
                            <span class="fw-semibold">{{ $user->phone ?? 'Not Set' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted"><i class="bi bi-calendar3 me-2 text-warning"></i>Registration Date</span>
                            <span class="fw-semibold">{{ $user->created_at->format('M d, Y H:i') }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted"><i class="bi bi-clock-history me-2 text-info"></i>Last Updated</span>
                            <span class="fw-semibold">{{ $user->updated_at->diffForHumans() }}</span>
                        </li>
                    </ul>

                    {{-- Quick Reset Password Form for Admin --}}
                    <div class="card border rounded-3 p-3 bg-light mb-3">
                        <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-key-fill text-warning me-1"></i> Quick Admin Password Reset</h6>
                        <form method="POST" action="{{ route('users.reset-password', $user) }}" class="d-flex gap-2">
                            @csrf
                            <input type="password" name="password" class="form-control form-control-sm" placeholder="New Password (min 8 chars)" required minlength="8">
                            <button type="submit" class="btn btn-sm btn-dark text-nowrap">Reset Password</button>
                        </form>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back to Users
                        </a>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning text-dark fw-semibold" style="background-color: #FB8B01; border-color: #FB8B01;">
                            <i class="bi bi-pencil me-1"></i> Edit Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
