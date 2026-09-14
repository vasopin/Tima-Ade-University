@extends('layouts.public')

@section('title', 'Academic Programs — Tima-Ade University')
@section('meta_description', 'Explore Tima-Ade University academic programs, grade levels, subject catalogue, and the courses that shape the university curriculum.')

@section('content')
@php($programImages = config('programs.images'))
<!-- Academic Hero -->
<section class="academics-hero public-page-header text-white">
    <div class="container position-relative py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 academics-reveal">
                <span class="badge bg-crimson mb-3"><i class="bi bi-mortarboard-fill me-1"></i> Curriculum & Standards</span>
                <h1 class="display-4 fw-bold mb-3">Academic programs with purpose.</h1>
                <p class="lead opacity-90 mb-4">Explore the existing curriculum, grade levels, and subject allocations that shape the Tima-Ade University learning experience.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#grade-levels" class="btn btn-crimson px-4 py-2 fw-semibold academics-button">Explore grade levels <i class="bi bi-arrow-down ms-1"></i></a>
                    <a href="#course-catalog" class="btn btn-outline-light px-4 py-2 fw-semibold academics-button">Browse course catalog <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
                <nav aria-label="breadcrumb" class="mt-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('public.home') }}" class="text-light text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item active text-danger" aria-current="page">Academics</li>
                    </ol>
                </nav>
            </div>
            <div class="col-lg-5 academics-reveal">
                <div class="academics-hero-visual">
                    <img src="{{ asset($programImages['hero']['src']) }}" alt="{{ $programImages['hero']['alt'] }}" loading="eager">
                    <div class="academics-hero-note"><span class="d-block text-danger fw-bold">Learning in context</span><small>Classes, subjects, and sections organized for clarity.</small></div>
                </div>
            </div>
        </div>
        <div class="academics-hero-stats academics-reveal" aria-label="Academic overview">
            <div><strong>{{ $classes->count() }}</strong><span>Active grade levels</span></div>
            <div><strong>{{ $subjects->count() }}</strong><span>Catalog subjects</span></div>
            <div><strong>{{ $classes->sum(fn($class) => $class->sections->count()) }}</strong><span>Active sections</span></div>
        </div>
    </div>
</section>

<!-- Classes Breakdown -->
<section id="grade-levels" class="py-5 bg-white academics-section">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold text-uppercase small tracking-wider">Structure</span>
            <h2 class="section-heading fw-bold mt-1">Grade Levels & Classrooms</h2>
            <p class="text-muted">Each grade is structured to deliver age-appropriate challenge, intellectual depth, and personal development.</p>
        </div>

        <div class="row g-4 mb-5">
            @forelse($classes as $class)
                <div class="col-lg-6">
                    <div class="card h-100 border-0 rounded-4 shadow-sm overflow-hidden academic-class-card">
                        <div class="card-header bg-navy text-white p-4 d-flex justify-content-between align-items-center academic-class-header">
                            <div>
                                <span class="badge bg-crimson mb-1">Grade {{ $class->grade_level ?? 'Level' }}</span>
                                <h4 class="fw-bold mb-0 text-white">{{ $class->name }}</h4>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-light text-dark">{{ $class->sections->count() }} Sections</span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted mb-3">{{ $class->description ?? 'Comprehensive academic curriculum with dedicated teacher supervision.' }}</p>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="p-3 border rounded-3 bg-light small academic-meta">
                                        <span class="text-muted d-block">Class Teacher</span>
                                        <strong>{{ $class->classTeacher?->user->name ?? 'Faculty Assigned' }}</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 border rounded-3 bg-light small academic-meta">
                                        <span class="text-muted d-block">Active Sections</span>
                                        <strong>
                                            @foreach($class->sections as $sec)
                                                {{ $sec->name }}@if(!$loop->last), @endif
                                            @endforeach
                                        </strong>
                                    </div>
                                </div>
                            </div>

                            <h6 class="fw-bold mb-2 small text-uppercase text-secondary">Assigned Subjects ({{ $class->subjects->count() }})</h6>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($class->subjects as $subj)
                                    <span class="badge bg-secondary-subtle text-dark border">
                                        {{ $subj->name }} ({{ $subj->code }})
                                    </span>
                                @empty
                                    <span class="text-muted small">Standard subjects list.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">No academic classes configured.</div>
            @endforelse
        </div>

        <!-- Subjects Catalog -->
        <div id="course-catalog" class="border-top pt-5 academics-catalog">
            <div class="text-center max-w-700 mx-auto mb-4">
                <span class="text-danger fw-bold text-uppercase small tracking-wider">Courses</span>
                <h2 class="section-heading fw-bold mt-1">Course Catalog & Disciplines</h2>
            </div>

            <div class="row g-3">
                @forelse($subjects as $subj)
                    <div class="col-md-6 col-lg-4">
                        <div class="p-3 border rounded-3 bg-light d-flex align-items-center justify-content-between academic-subject-card">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">{{ $subj->name }}</h6>
                                <small class="text-muted">{{ $subj->code }}</small>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                {{ $subj->classes_count }} Grades
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted">No courses listed.</div>
                @endforelse
            </div>
        </div>
    </div>
</section>

<section class="programs-pathways-section bg-light"><div class="container py-5 py-lg-6"><div class="row g-5 align-items-center"><div class="col-lg-5 programs-reveal"><p class="programs-kicker text-danger">THE ACADEMIC ECOSYSTEM</p><h2 class="programs-section-title">A clear path from curiosity to capability.</h2><p class="text-muted">Use the academic catalogue to explore the current structure, then plan ahead with the calendar and build toward professional growth.</p></div><div class="col-lg-7"><div class="row g-3 programs-reveal"><div class="col-sm-4"><a href="{{ route('public.programs') }}#grade-levels" class="programs-pathway"><span>01</span><h3>Discover</h3><p>Explore active academic levels and subjects.</p></a></div><div class="col-sm-4"><a href="{{ route('public.programs.calendar') }}" class="programs-pathway"><span>02</span><h3>Plan</h3><p>Keep important academic dates in view.</p></a></div><div class="col-sm-4"><a href="{{ route('public.programs.careers') }}" class="programs-pathway"><span>03</span><h3>Progress</h3><p>Prepare for the next stage of development.</p></a></div></div></div></div></div></section>

<section class="programs-feature-section bg-navy text-white"><div class="container py-5 py-lg-6"><div class="row g-5 align-items-center"><div class="col-lg-6"><img src="{{ asset($programImages['library']['src']) }}" alt="{{ $programImages['library']['alt'] }}" class="programs-feature-image" loading="lazy"></div><div class="col-lg-5 offset-lg-1"><p class="programs-kicker text-info">ACADEMIC EXPERIENCE</p><h2 class="programs-section-title text-white">Learning that asks students to think, test, and apply.</h2><p class="text-white-50">Tima-Ade’s academic environment connects classroom foundations with inquiry, technology, and student development. The catalogue above reflects the current data available in the institution’s academic system.</p><a href="{{ route('public.about') }}" class="btn btn-outline-light px-4 py-3 fw-semibold mt-3">Learn about Tima-Ade <i class="bi bi-arrow-up-right ms-2"></i></a></div></div></div></section>

<section class="programs-next-section bg-white"><div class="container py-5 py-lg-6"><div class="row g-4"><div class="col-lg-6"><div class="programs-next-panel"><p class="programs-kicker text-danger">PLAN AHEAD</p><h2 class="programs-card-title">Academic Calendar</h2><p class="text-muted">Find a structured place for registration, teaching periods, examinations, and other dates as the official calendar is confirmed.</p><a href="{{ route('public.programs.calendar') }}" class="fw-bold text-danger text-decoration-none">View Academic Calendar <i class="bi bi-arrow-right ms-1"></i></a></div></div><div class="col-lg-6"><div class="programs-next-panel"><p class="programs-kicker text-danger">LOOKING FORWARD</p><h2 class="programs-card-title">Careers &amp; Certifications</h2><p class="text-muted">Explore professional-development information and clearly marked opportunities for future verified additions.</p><a href="{{ route('public.programs.careers') }}" class="fw-bold text-danger text-decoration-none">Explore Careers &amp; Certifications <i class="bi bi-arrow-right ms-1"></i></a></div></div></div></div></section>
@endsection
