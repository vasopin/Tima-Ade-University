@extends('layouts.hemis')

@section('title', 'University Administration Portal')

@section('content')
@php($student = $student ?? null)
@php($searchedId = $searchedId ?? null)
<div class="hemis-reference-shell">
    <header class="hemis-reference-header">
        <a href="{{ route('dashboard') }}" class="hemis-reference-brand">
            <img src="{{ asset('images/tima-ade-university-logo-premium.svg') }}" alt="Tima-Ade University logo">
            <span><strong>Tima-Ade University</strong><small>University Administration Portal</small></span>
        </a>
        <a href="{{ route('dashboard') }}" class="hemis-reference-admin">Admin Dashboard <i class="bi bi-arrow-up-right ms-1" aria-hidden="true"></i></a>
    </header>

    <main class="hemis-reference-main">
        <section class="hemis-reference-copy" aria-labelledby="hemis-title">
            <img src="{{ asset('images/tima-ade-university-logo-premium.svg') }}" alt="Tima-Ade University logo" class="hemis-reference-hero-logo">
            <h1 id="hemis-title">Tima-Ade University Administration Portal</h1>
            <p class="hemis-reference-intro">Enter your University Student ID to check the official university record. The search works with capital, small, or mixed letters.</p>
            <aside class="hemis-reference-notice" aria-labelledby="hemis-notice-title">
                <i class="bi bi-shield-lock" aria-hidden="true"></i>
                <div><h2 id="hemis-notice-title">Important notice:</h2><p>Use the Student ID issued by Tima-Ade University. If your record is not found, check the ID carefully or contact the Academic Office for support. This protected lookup is for authorized administration staff only.</p></div>
            </aside>
        </section>

        <section class="hemis-reference-action" aria-labelledby="hemis-check-title">
            <div class="hemis-reference-action-inner">
                <p class="hemis-reference-eyebrow">Secure record lookup</p>
                <h2 id="hemis-check-title">Check university record</h2>
                <p class="hemis-reference-action-copy">Search the official student record using the University Student ID.</p>
            @include('partials._alerts')
            <form method="POST" action="{{ route('hemis.lookup') }}" class="hemis-lookup-form">
                @csrf
                <label for="hemis-student-id" class="visually-hidden">University Student ID</label>
                <div class="hemis-lookup-controls">
                    <input id="hemis-student-id" name="student_id" type="text" value="{{ old('student_id', $searchedId) }}" autocomplete="off" required maxlength="100" placeholder="Enter University Student ID" @error('student_id') aria-invalid="true" aria-describedby="hemis-id-error" @enderror>
                    <button type="submit" class="btn btn-crimson">Check</button>
                </div>
                @error('student_id')<p id="hemis-id-error" class="hemis-form-error" role="alert">{{ $message }}</p>@enderror
            </form>

            @if($searchedId !== null)
                @if($student)
                    <section class="hemis-record-result" aria-labelledby="hemis-record-title" role="status">
                        <p class="hemis-kicker">Authorized record found</p>
                        <h2 id="hemis-record-title">{{ $student->user?->name ?? 'Student record' }}</h2>
                        <dl>
                            <div><dt>University Student ID</dt><dd>{{ $student->student_id }}</dd></div>
                            <div><dt>Academic level</dt><dd>{{ $student->schoolClass?->name ?? 'Not recorded' }}</dd></div>
                            <div><dt>Status</dt><dd>{{ ucfirst($student->status ?? 'Not recorded') }}</dd></div>
                        </dl>
                    </section>
                @else
                    <section class="hemis-record-empty" role="status" aria-live="polite"><i class="bi bi-search" aria-hidden="true"></i><h2>No record found</h2><p>No student record matched that identifier. Confirm the official ID and contact the appropriate administrator if the issue continues.</p></section>
                @endif
            @endif

            </div>
        </section>
    </main>
</div>
@endsection
