@extends('layouts.auth')

@section('title', 'Access Restricted')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <img src="{{ asset('images/tima-ade-university-logo-premium.svg') }}" alt="Tima-Ade University logo" class="auth-brand-image">
        <h3 class="auth-title">Access Restricted</h3>
        <p class="auth-subtitle">University Administration Portal</p>
    </div>
    <div class="auth-body-content text-center">
        <p class="text-muted mb-4">Your account does not have permission to access the University Administration Portal.</p>
        <a href="{{ route($dashboardRoute) }}" class="btn btn-primary w-100 py-2 fw-semibold" style="background-color: #016ED5; border-color: #016ED5;">
            <i class="bi bi-arrow-left me-2" aria-hidden="true"></i> Return to Dashboard
        </a>
    </div>
</div>
@endsection