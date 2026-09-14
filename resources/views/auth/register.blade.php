@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')
<div class="auth-card" style="max-width: 520px; margin: 0 auto;">
    <div class="auth-header py-4">
        <img src="{{ asset('images/tima-ade-university-logo-premium.svg') }}" alt="Tima-Ade University logo" class="auth-brand-image">
        <h3 class="auth-title">Tima-Ade University</h3>
        <p class="auth-subtitle">New Scholar & Portal Registration</p>
    </div>

    <div class="auth-body-content p-4">
        @include('partials._alerts')

        <form method="POST" action="{{ route('register.post') }}" class="needs-validation">
            @csrf

            <!-- Account Role Selection -->
            <div class="mb-3">
                <label class="form-label text-muted small fw-semibold">ACCOUNT TYPE</label>
                <div class="row g-2">
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="role_slug" id="role_student" value="student" {{ old('role_slug', 'student') == 'student' ? 'checked' : '' }} onchange="toggleStudentClass(true)">
                        <label class="btn btn-outline-dark btn-sm w-100 py-2 d-flex flex-column align-items-center" for="role_student">
                            <i class="bi bi-mortarboard-fill fs-5 mb-1 text-success"></i>
                            <span class="small fw-semibold">Student</span>
                        </label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="role_slug" id="role_parent" value="parent" {{ old('role_slug') == 'parent' ? 'checked' : '' }} onchange="toggleStudentClass(false)">
                        <label class="btn btn-outline-dark btn-sm w-100 py-2 d-flex flex-column align-items-center" for="role_parent">
                            <i class="bi bi-people-fill fs-5 mb-1 text-info"></i>
                            <span class="small fw-semibold">Parent</span>
                        </label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="role_slug" id="role_teacher" value="teacher" {{ old('role_slug') == 'teacher' ? 'checked' : '' }} onchange="toggleStudentClass(false)">
                        <label class="btn btn-outline-dark btn-sm w-100 py-2 d-flex flex-column align-items-center" for="role_teacher">
                            <i class="bi bi-person-badge-fill fs-5 mb-1 text-primary"></i>
                            <span class="small fw-semibold">Teacher</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Full Name -->
            <div class="mb-3">
                <label for="name" class="form-label text-muted small fw-semibold">FULL NAME</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-secondary"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="e.g. Alexander Hayes">
                </div>
                @error('name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label text-muted small fw-semibold">EMAIL ADDRESS</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-secondary"></i></span>
                    <input type="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com">
                </div>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Phone Number & Class Row -->
            <div class="row g-2 mb-3">
                <div class="col-sm-6">
                    <label for="phone" class="form-label text-muted small fw-semibold">PHONE (OPTIONAL)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone text-secondary"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0 @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone') }}" placeholder="+1 555-0123">
                    </div>
                </div>
                <div class="col-sm-6" id="studentClassWrapper">
                    <label for="school_class_id" class="form-label text-muted small fw-semibold">ADMITTED CLASS</label>
                    <select name="school_class_id" id="school_class_id" class="form-select">
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('school_class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label text-muted small fw-semibold">PASSWORD</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-secondary"></i></span>
                    <input type="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                           id="password" name="password" required placeholder="Minimum 6 characters">
                </div>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label text-muted small fw-semibold">CONFIRM PASSWORD</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-check text-secondary"></i></span>
                    <input type="password" class="form-control border-start-0 ps-0" 
                           id="password_confirmation" name="password_confirmation" required placeholder="Re-type your password">
                </div>
            </div>

            <!-- Terms agreement -->
            <div class="form-check mb-4">
                <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" {{ old('terms') ? 'checked' : '' }} required>
                <label class="form-check-label small text-secondary" for="terms">
                    I agree to Tima-Ade University's <a href="{{ route('public.about') }}" target="_blank" class="text-danger text-decoration-none">Terms of Service</a> & <a href="{{ route('public.about') }}" target="_blank" class="text-danger text-decoration-none">Honor Code</a>.
                </label>
                @error('terms')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-crimson w-100 py-2 fw-semibold shadow-sm mb-3">
                <i class="bi bi-person-plus-fill me-2"></i>Create My Account
            </button>
        </form>

        <div class="text-center pt-3 border-top">
            <span class="small text-muted">Already registered with Tima-Ade University?</span>
            <a href="{{ route('login') }}" class="small fw-semibold text-danger ms-1 text-decoration-none">
                Sign In to Portal <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<script>
function toggleStudentClass(isStudent) {
    const wrapper = document.getElementById('studentClassWrapper');
    if (wrapper) {
        wrapper.style.display = isStudent ? 'block' : 'none';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const isStudentSelected = document.getElementById('role_student').checked;
    toggleStudentClass(isStudentSelected);
});
</script>
@endsection
