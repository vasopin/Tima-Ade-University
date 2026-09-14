@extends('layouts.auth')

@section('title', 'Set New Password')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <img src="{{ asset('images/tima-ade-university-logo-premium.svg') }}" alt="Tima-Ade University logo" class="auth-brand-image">
        <h3 class="auth-title">New Password</h3>
        <p class="auth-subtitle">Tima-Ade University Account Security</p>
    </div>

    <div class="auth-body-content">
        @include('partials._alerts')

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label for="email" class="form-label text-muted small fw-semibold">EMAIL ADDRESS</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-secondary"></i></span>
                    <input type="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email', $email) }}" required autofocus readonly>
                </div>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label text-muted small fw-semibold">NEW PASSWORD (MIN 8 CHARS)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-secondary"></i></span>
                    <input type="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                           id="password" name="password" required placeholder="••••••••">
                </div>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label text-muted small fw-semibold">CONFIRM NEW PASSWORD</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-secondary"></i></span>
                    <input type="password" class="form-control border-start-0 ps-0" 
                           id="password_confirmation" name="password_confirmation" required placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-3" style="background-color: #016ED5; border-color: #016ED5;">
                <i class="bi bi-check-circle-fill me-2"></i>Reset & Update Password
            </button>
        </form>

        <div class="text-center pt-3 border-top">
            <a href="{{ route('login') }}" class="small fw-semibold text-secondary text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Back to Sign In
            </a>
        </div>
    </div>
</div>
@endsection
