@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <img src="{{ asset('images/tima-ade-university-logo-premium.svg') }}" alt="Tima-Ade University logo" class="auth-brand-image">
        <h3 class="auth-title">Reset Password</h3>
        <p class="auth-subtitle">Tima-Ade University Account Recovery</p>
    </div>

    <div class="auth-body-content">
        @include('partials._alerts')

        @if (session('status'))
            <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        @if (session('dev_reset_url'))
            <div class="alert alert-info small mb-3">
                <div class="fw-bold mb-1"><i class="bi bi-info-circle me-1"></i> Direct Password Reset Link:</div>
                <a href="{{ session('dev_reset_url') }}" class="text-break fw-semibold text-decoration-none">
                    {{ session('dev_reset_url') }}
                </a>
            </div>
        @endif

        <p class="text-muted small mb-3">
            Enter the registered email address associated with your Tima-Ade University account. We will provide instructions to reset your password.
        </p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label text-muted small fw-semibold">REGISTERED EMAIL</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-secondary"></i></span>
                    <input type="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@timaade.edu">
                </div>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-3" style="background-color: #016ED5; border-color: #016ED5;">
                <i class="bi bi-send-fill me-2"></i>Send Password Reset Link
            </button>
        </form>

        <div class="text-center pt-3 border-top">
            <a href="{{ route('login') }}" class="small fw-semibold text-secondary text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Return to Sign In
            </a>
        </div>
    </div>
</div>
@endsection
