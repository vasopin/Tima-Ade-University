@extends('layouts.auth')

@section('title', $portalContext === 'admin' ? 'University Admin Portal Sign In' : ($portalContext === 'university' ? 'University Portal Sign In' : 'Portal Sign In'))

@section('content')
<style>
    /* Book UI Styling - Lightweight and Fast */
    .auth-document,
    .auth-document body.auth-body {
        overflow: hidden;
    }
    
    /* Back to Home - IMMEDIATE navigation */
    .auth-back-link {
        position: absolute;
        top: 1.5rem;
        left: 1.5rem;
        z-index: 100;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #f8fafc;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .auth-back-link:hover {
        background: rgba(255, 255, 255, 0.12);
        border-color: rgba(255, 255, 255, 0.25);
    }

    /* Main container */
    .book-auth-container {
        width: 100%;
        max-width: 1320px;
        margin: 0 auto;
        position: fixed;
        inset: 0;
        padding: 0 clamp(1rem, 3vw, 2rem);
        pointer-events: none;
    }

    /* Background */
    .book-auth-bg {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, #0a1f1a 0%, #0d2b21 50%, #061410 100%);
        z-index: -1;
    }

    /* Closed Book State */
    .book-cover-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        inset: 0;
        padding: 5.5rem 0 1rem;
        perspective: 1200px;
        pointer-events: none;
    }

    .book-cover {
        position: relative;
        width: min(420px, calc(100vw - 2rem), calc((100dvh - 7rem) * 0.762));
        height: min(560px, calc((100vw - 2rem) * 1.333), calc(100dvh - 7rem));
        background: linear-gradient(135deg, #1a5c54 0%, #0d3f38 100%);
        border-radius: 12px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4), inset -2px 0 8px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1.5rem;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
        pointer-events: auto;
    }

    .book-cover:hover {
        transform: translateY(-4px);
        box-shadow: 0 24px 70px rgba(0, 0, 0, 0.45), inset -2px 0 8px rgba(0, 0, 0, 0.3);
    }

    .book-cover:focus-visible {
        outline: 3px solid #4ade80;
        outline-offset: 2px;
    }

    .book-cover-emblem {
        font-size: 3.5rem;
        color: #c7f0d8;
        opacity: 0.9;
    }

    .book-cover-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #ffffff;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .book-cover-subtitle {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.7);
        margin: 0;
        font-weight: 500;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    /* Open Book State */
    .book-open-wrapper {
        display: none;
        position: absolute;
        inset: 0;
        align-items: center;
        justify-content: center;
        padding: 5.5rem 0 1rem;
        pointer-events: none;
    }

    .book-open-wrapper.is-open {
        display: flex;
    }

    .book-pages-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
        width: 100%;
        max-width: 1240px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.35);
        overflow: hidden;
        min-height: 0;
        transform: scale(var(--book-scale, 1));
        transform-origin: center center;
        pointer-events: auto;
    }

    /* Book Page styling */
    .book-page {
        display: flex;
        flex-direction: column;
        padding: 3rem;
        justify-content: center;
        align-items: flex-start;
        gap: 1.5rem;
    }

    .book-page-left {
        background: #f8fafc;
        border-right: 1px solid #e2e8f0;
    }

    .book-page-right {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    /* Logo on right page */
    .book-logo {
        width: 100px;
        height: 100px;
        object-fit: contain;
        margin-bottom: 1rem;
        opacity: 0.95;
    }

    .book-welcome-title {
        font-size: 1.8rem;
        font-weight: 800;
        color: #0a1f1a;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .book-welcome-subtitle {
        font-size: 1rem;
        color: #64748b;
        margin: 0;
        line-height: 1.6;
        max-width: 280px;
    }

    /* Login Form styling */
    .book-login-form {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }

    .book-form-heading {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 700;
        color: #0a1f1a;
        letter-spacing: -0.01em;
    }

    .book-form-subheading {
        margin: 0;
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.5;
    }

    .book-form-field {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .book-form-field label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #0a1f1a;
        letter-spacing: 0.02em;
    }

    .book-form-field input {
        padding: 0.75rem 1rem;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        background: #ffffff;
        color: #0a1f1a;
    }

    .book-form-field input:focus {
        outline: none;
        border-color: #0d3f38;
        box-shadow: 0 0 0 3px rgba(13, 63, 56, 0.1);
    }

    .book-form-field input.is-invalid {
        border-color: #dc2626;
    }

    .book-form-field input::placeholder {
        color: #94a3b8;
    }

    .book-form-error {
        font-size: 0.75rem;
        color: #dc2626;
        margin: 0;
    }

    .book-form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        gap: 1rem;
    }

    .book-form-checkbox {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        color: #475569;
    }

    .book-form-checkbox input {
        width: auto;
        margin: 0;
        cursor: pointer;
    }

    .book-form-link {
        color: #0d3f38;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .book-form-link:hover {
        color: #0a1f1a;
    }

    .book-submit-btn {
        padding: 0.85rem 1.5rem;
        background: linear-gradient(135deg, #0d3f38 0%, #0a1f1a 100%);
        color: #ffffff;
        border: none;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        margin-top: 0.5rem;
    }

    .book-submit-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(13, 63, 56, 0.25);
    }

    .book-submit-btn:active:not(:disabled) {
        transform: translateY(0);
    }

    .book-submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .book-portal-switcher {
        font-size: 0.85rem;
        color: #64748b;
        text-align: center;
        padding-top: 0.5rem;
        border-top: 1px solid #e2e8f0;
    }

    .book-portal-switcher p {
        margin: 0 0 0.5rem;
    }

    .book-portal-link {
        color: #0d3f38;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s ease;
    }

    .book-portal-link:hover {
        color: #0a1f1a;
    }

    /* Alert styling */
    .book-alert {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.9rem 1rem;
        border-radius: 6px;
        font-size: 0.85rem;
        line-height: 1.5;
    }

    .book-alert-danger {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #7f1d1d;
    }

    .book-alert i {
        margin-top: 0.15rem;
        flex-shrink: 0;
    }

    .book-alert-link {
        display: inline-block;
        margin-top: 0.35rem;
        color: inherit;
        font-weight: 700;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    /* Demo buttons (if enabled) */
    .book-demo-section {
        width: 100%;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .book-demo-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin: 0;
    }

    .book-demo-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }

    .book-demo-btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
        background: rgba(13, 63, 56, 0.1);
        border: 1px solid rgba(13, 63, 56, 0.3);
        border-radius: 4px;
        color: #0d3f38;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .book-demo-btn:hover {
        background: rgba(13, 63, 56, 0.2);
        border-color: rgba(13, 63, 56, 0.5);
    }

    /* Responsive Design */
    @media (max-width: 991.98px) {
        .book-pages-container {
            grid-template-columns: 1fr;
        }

        .book-page-left {
            border-right: none;
            border-bottom: 1px solid #e2e8f0;
        }

        .book-page {
            padding: 2rem;
        }

        .book-logo {
            width: 80px;
            height: 80px;
        }

        .book-welcome-title {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 575.98px) {
        .auth-back-link {
            top: 1rem;
            left: 1rem;
            font-size: 0.8rem;
            padding: 0.5rem 0.8rem;
        }

        .book-cover-wrapper {
            inset: 5rem 0 1rem;
        }

        .book-cover {
            width: min(390px, calc(100vw - 2rem), calc((100dvh - 6.5rem) * 0.778));
            height: min(500px, calc((100vw - 2rem) * 1.282), calc(100dvh - 6.5rem));
            gap: 1rem;
            padding: 1.5rem;
        }

        .book-cover-emblem {
            font-size: 2.8rem;
        }

        .book-cover-title {
            font-size: 1.1rem;
        }

        .book-open-wrapper {
            inset: 5rem 0 0.75rem;
            padding: 0.75rem;
        }

        .book-pages-container {
            border-radius: 8px;
        }

        .book-page {
            padding: 1.5rem;
            gap: 1rem;
        }

        .book-form-heading {
            font-size: 1.2rem;
        }

        .book-logo {
            width: 70px;
            height: 70px;
        }

        .book-welcome-title {
            font-size: 1.3rem;
        }

        .book-demo-buttons {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 360px) {
        .book-cover {
            width: min(340px, calc(100vw - 1.5rem), calc((100dvh - 6rem) * 0.8));
            height: min(425px, calc((100vw - 1.5rem) * 1.25), calc(100dvh - 6rem));
            padding: 1.25rem;
        }

        .book-page {
            padding: 1.25rem;
        }

        .book-form-heading {
            font-size: 1.1rem;
        }

        .book-welcome-title {
            font-size: 1.2rem;
        }
    }

    /* Accessibility: Reduced Motion */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation: none !important;
            transition: none !important;
        }

        .book-cover {
            transform: none !important;
        }

        .book-open-wrapper.is-open {
            display: flex !important;
        }
    }
</style>

<!-- Background -->
<div class="book-auth-bg"></div>

<!-- Back to Home link - IMMEDIATE navigation -->
<a href="{{ route('public.home') }}" class="auth-back-link" aria-label="Back to Tima-Ade University home">
    <i class="bi bi-house"></i> Back to Home
</a>

<!-- Book Auth Container -->
<div class="book-auth-container">
    <!-- Closed Book Cover -->
    <div class="book-cover-wrapper" id="bookCoverWrapper">
        <button 
            class="book-cover" 
            id="bookCover" 
            role="button" 
            tabindex="0" 
            aria-label="Click to open Tima-Ade University portal"
            aria-pressed="false"
            type="button">
            <div class="book-cover-emblem">
                <i class="bi bi-book-half"></i>
            </div>
            <p class="book-cover-title">University Portal</p>
            <p class="book-cover-subtitle">Press to Open</p>
        </button>
    </div>

    <!-- Open Book Pages -->
    <div class="book-open-wrapper" id="bookOpenWrapper">
        <div class="book-pages-container auth-visual">
            <!-- Left Page: Login Form -->
            <div class="book-page book-page-left">
                <h2 class="book-form-heading">Sign In</h2>
                <p class="book-form-subheading">
                    @if($portalContext === 'admin')
                        For super administrators, administrators, and staff
                    @else
                        For students, teachers, and parents/guardians
                    @endif
                </p>

                <!-- Alerts -->
                @if(session('portal_access_error'))
                    <div class="book-alert book-alert-danger" role="alert">
                        <i class="bi bi-exclamation-circle"></i>
                        <div>
                            <div>{{ session('portal_access_error') }}</div>
                            @if($portalContext === 'admin')
                                <a href="{{ route('university.login') }}" class="book-alert-link">Go to University Portal Sign-In</a>
                            @else
                                <a href="{{ route('admin.login') }}" class="book-alert-link">Go to University Admin Sign-In</a>
                            @endif
                        </div>
                    </div>
                @endif
                @if($errors->has('auth'))
                    <div class="book-alert book-alert-danger" role="alert">
                        <i class="bi bi-exclamation-circle"></i>
                        {{ $errors->first('auth') }}
                    </div>
                @elseif(!$errors->hasAny(['email', 'password']))
                    @include('partials._alerts')
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ $portalContext === 'admin' ? route('admin.login.post') : ($portalContext === 'university' ? route('university.login.post') : route('login.post')) }}" class="book-login-form" novalidate>
                    @csrf

                    <div class="book-form-field">
                        <label for="email">EMAIL ADDRESS</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="@error('email') is-invalid @enderror" 
                            value="{{ old('email') }}" 
                            placeholder="name@timaade.edu" 
                            required 
                            autofocus>
                        @error('email')
                            <p class="book-form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="book-form-field">
                        <label for="password">PASSWORD</label>
                        <div style="position: relative;">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="@error('password') is-invalid @enderror" 
                                placeholder="••••••••" 
                                required
                                style="width: 100%; padding-right: 2.5rem;">
                            <button 
                                type="button" 
                                id="togglePasswordBtn" 
                                aria-label="Show or hide password"
                                onclick="togglePasswordVisibility()"
                                style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #64748b; cursor: pointer; padding: 0.5rem;">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="book-form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="book-form-options">
                        <label class="book-form-checkbox">
                            <input type="checkbox" name="remember" id="remember" checked>
                            <span>Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="book-form-link">Forgot password?</a>
                    </div>

                    <button type="submit" class="book-submit-btn" id="submitBtn">
                        Login
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <!-- Portal Switcher -->
                <div class="book-portal-switcher">
                    <p>Need a different portal?</p>
                    @if($portalContext === 'admin')
                        <a href="{{ route('university.login') }}" class="book-portal-link">Go to Student Portal →</a>
                    @else
                        <a href="{{ route('admin.login') }}" class="book-portal-link">Go to Admin Portal →</a>
                    @endif
                </div>

                <!-- Demo Credentials (if enabled) -->
                @if (env('DEMO_AUTO_APPROVE', false))
                    <div class="book-demo-section">
                        <p class="book-demo-label"><i class="bi bi-info-circle"></i> Demo:</p>
                        <div class="book-demo-buttons">
                            <button type="button" class="book-demo-btn" onclick="setLogin('superadmin@school.com')">Super Admin</button>
                            <button type="button" class="book-demo-btn" onclick="setLogin('admin@school.com')">Admin</button>
                            <button type="button" class="book-demo-btn" onclick="setLogin('teacher1@school.com')">Teacher</button>
                            <button type="button" class="book-demo-btn" onclick="setLogin('student1@school.com')">Student</button>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Page: Welcome -->
            <div class="book-page book-page-right">
                <img 
                    src="{{ asset('images/tima-ade-university-logo-premium.svg') }}" 
                    alt="Tima-Ade University logo" 
                    class="book-logo" 
                    loading="eager">
                <h3 class="book-welcome-title">Welcome to<br>Tima-Ade University</h3>
                <p class="book-welcome-subtitle">Access your academic portal securely</p>
            </div>
        </div>
    </div>
</div>

<script>
// Book Interaction Logic
const bookCover = document.getElementById('bookCover');
const bookCoverWrapper = document.getElementById('bookCoverWrapper');
const bookOpenWrapper = document.getElementById('bookOpenWrapper');
const submitBtn = document.getElementById('submitBtn');

function fitOpenBook() {
    if (!bookOpenWrapper.classList.contains('is-open')) return;

    const pages = document.querySelector('.book-pages-container');
    if (!pages) return;

    pages.style.setProperty('--book-scale', '1');
    const available = bookOpenWrapper.getBoundingClientRect();
    const natural = pages.getBoundingClientRect();
    const scale = Math.min(
        1,
        (available.width - 8) / natural.width,
        (available.height - 8) / natural.height
    );
    pages.style.setProperty('--book-scale', Math.max(scale, 0.1).toFixed(4));
}

// Open the book
function openBook() {
    if (bookOpenWrapper.classList.contains('is-open')) return;
    
    bookCover.setAttribute('aria-pressed', 'true');
    bookCoverWrapper.style.display = 'none';
    bookOpenWrapper.classList.add('is-open');
    fitOpenBook();
    setTimeout(fitOpenBook, 50);
    
    // Focus email field for accessibility
    setTimeout(() => {
        document.getElementById('email').focus();
    }, 50);
}

// Close the book
function closeBook() {
    if (!bookOpenWrapper.classList.contains('is-open')) return;
    
    bookCover.setAttribute('aria-pressed', 'false');
    bookOpenWrapper.classList.remove('is-open');
    bookCoverWrapper.style.display = 'flex';
    bookCover.focus();
}

// Event listeners
bookCover.addEventListener('click', openBook);
bookCover.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openBook();
    }
});

// Password visibility toggle
function togglePasswordVisibility() {
    const input = document.getElementById('password');
    const icon = document.getElementById('togglePasswordIcon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Demo login helper
function setLogin(email) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = '';
    if (!bookOpenWrapper.classList.contains('is-open')) {
        openBook();
    }
}

// Handle form submission
const form = document.querySelector('form');
if (form) {
    form.addEventListener('submit', function() {
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span style="display: inline-block; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: spin 0.6s linear infinite;"></span> Signing in...';
        }
    });
}

// ESC key to close book
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && bookOpenWrapper.classList.contains('is-open')) {
        closeBook();
    }
});

window.addEventListener('resize', fitOpenBook);

// On page load, check if there are validation errors
// If so, automatically open the book to keep it open during login attempts
const hasErrors = document.querySelectorAll('.book-form-error, .book-alert').length > 0;
if (hasErrors && !bookOpenWrapper.classList.contains('is-open')) {
    openBook();
}
</script>

<style>
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endsection
