@extends('layouts.app')

@section('title', 'Student Live Classes')

@push('styles')
<style>
    .student-live-page { --student-ink: #102a43; --student-muted: #627d98; --student-teal: #087f8c; --student-coral: #d9573f; --student-line: #d9e2ec; background: linear-gradient(135deg, #f7fbfc 0%, #eef5f7 55%, #fff8f3 100%); border: 1px solid #e1ebef; border-radius: 18px; padding: clamp(1rem, 2vw, 2rem); }
    .student-live-header { border-bottom: 1px solid rgba(16, 42, 67, .12); padding-bottom: 1.25rem; }
    .student-live-header .eyebrow-label { color: var(--student-teal); font-weight: 800; letter-spacing: .12em; }
    .student-live-header h2 { color: var(--student-ink); font-size: clamp(1.6rem, 2.5vw, 2.2rem); font-weight: 800; }
    .student-live-header p { color: var(--student-muted) !important; }
    .student-live-page .student-live-classes { margin-top: 1.5rem; }
    .student-live-page .student-live-classes > .d-flex { align-items: flex-end !important; }
    .student-live-page .dashboard-section-header { color: var(--student-ink); font-weight: 800; }
    .student-live-page .student-live-classes > .d-flex .eyebrow-label { color: var(--student-teal); font-size: .72rem; font-weight: 800; letter-spacing: .1em; }
    .student-live-page [data-live-class-id] { border: 1px solid var(--student-line) !important; border-radius: 14px; box-shadow: 0 12px 28px rgba(16, 42, 67, .07) !important; overflow: hidden; position: relative; }
    .student-live-page [data-live-class-id]::before { background: var(--student-teal); content: ''; height: 4px; left: 0; position: absolute; right: 0; top: 0; }
    .student-live-page [data-live-class-id][data-live-class-status="live"]::before { background: var(--student-coral); }
    .student-live-page [data-live-class-id] .card-body { padding: 1.35rem; }
    .student-live-page [data-live-class-id] h5 { color: var(--student-ink); font-weight: 800; }
    .student-live-page [data-live-class-id] .text-muted { color: var(--student-muted) !important; }
    .student-live-page [data-live-class-badge] { border-radius: 999px; font-size: .68rem; font-weight: 800; letter-spacing: .07em; padding: .42rem .68rem; text-transform: uppercase; }
    .student-live-page [data-live-class-status="live"] [data-live-class-badge] { box-shadow: 0 0 0 4px rgba(217, 87, 63, .1); }
    .student-live-page [data-live-class-id] button { border-radius: 8px; font-weight: 700; min-width: 170px; }
    .student-live-page [data-live-class-status="live"] button { background: var(--student-coral); border-color: var(--student-coral); box-shadow: 0 7px 16px rgba(217, 87, 63, .18); }
    .student-live-page [data-live-class-status="live"] button:hover, .student-live-page [data-live-class-status="live"] button:focus-visible { background: #bd4531; border-color: #bd4531; }
    .student-live-page [data-live-class-id] button:disabled { cursor: not-allowed; opacity: .62; }
    .student-live-empty { background: rgba(255, 255, 255, .72); border-color: var(--student-line) !important; border-radius: 12px; color: var(--student-muted); padding: 1.25rem; }
    @media (max-width: 575.98px) { .student-live-page { border-radius: 12px; } .student-live-page [data-live-class-id] .card-body { align-items: flex-start !important; padding: 1rem; } .student-live-page [data-live-class-id] form, .student-live-page [data-live-class-id] button { width: 100%; } }
</style>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Student Dashboard</a></li>
    <li class="breadcrumb-item active">Live Classes</li>
@endsection

@section('content')
<div class="container-fluid py-3 student-live-page">
    <div class="mb-4 student-live-header">
        <span class="eyebrow-label">Student LMS</span>
        <h2 class="page-header-title mb-1">Live Classes</h2>
        <p class="text-muted mb-0">Join live lessons for your enrolled courses.</p>
    </div>

    @include('dashboard.student-live-classes')
</div>
@endsection
