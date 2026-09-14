@extends('layouts.public')

@section('title', 'E-Campus — Tima-Ade University')
@section('meta_description', 'Explore Tima-Ade University facilities and E-Campus — campus infrastructure, the Tima-Ade campus map, and pathways into the digital learning portal.')

@section('content')
<section class="facilities-hero public-page-header text-white">
    <div class="container position-relative py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 facilities-reveal">
                <span class="badge bg-crimson mb-3"><i class="bi bi-grid-1x2 me-1"></i> E-Campus overview</span>
                <h1 class="display-4 fw-bold mb-3">Your digital campus, connected.</h1>
                <p class="lead opacity-90 mb-4">Enter the university's digital services, explore campus facilities, and find the right place to continue your academic journey.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-crimson px-4 py-2 fw-semibold facilities-button">Enter E-Campus <i class="bi bi-arrow-up-right ms-1"></i></a>
                    <a href="#facility-grid" class="btn btn-outline-light px-4 py-2 fw-semibold facilities-button">Explore facilities <i class="bi bi-arrow-down ms-1"></i></a>
                </div>
                <nav aria-label="breadcrumb" class="mt-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('public.home') }}" class="text-light text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item active text-danger" aria-current="page">Facilities</li>
                    </ol>
                </nav>
            </div>
            <div class="col-lg-5 facilities-reveal">
                <div class="facilities-hero-visual">
                    <img src="/images/home/campus.jpg" alt="Tima-Ade University campus environment" loading="eager">
                    <div class="facilities-hero-note"><span class="d-block text-danger fw-bold">Campus environment</span><small>Resources designed to support teaching, research and student life.</small></div>
                </div>
            </div>
        </div>
        <div class="facilities-hero-stats facilities-reveal" aria-label="Facilities overview">
            <div><strong>{{ $facilities->count() }}</strong><span>Facility areas</span></div>
            <div><strong><i class="bi bi-mortarboard-fill"></i></strong><span>Learning & research</span></div>
            <div><strong><i class="bi bi-heart-pulse-fill"></i></strong><span>Student wellbeing</span></div>
        </div>
    </div>
</section>

<nav class="e-campus-section-nav" aria-label="E-Campus pages"><div class="container"><div class="e-campus-section-nav-scroll">
    <a class="is-current" href="{{ route('public.facilities') }}" aria-current="page">Overview</a>
    <a href="{{ route('public.e-campus.learning') }}">Learning &amp; Courses</a>
    <a href="{{ route('public.e-campus.resources') }}">Academic Resources</a>
    <a href="{{ route('public.e-campus.student-services') }}">Student Services</a>
    <a href="{{ route('public.e-campus.help') }}">Help &amp; Support</a>
</div></div></nav>

@php
    $featuredFacilities = $facilities->take(3);
    $hargeisaCampus = [
        'name' => 'Tima-Ade University — Hargeisa Campus',
        'city' => 'Hargeisa, Somaliland',
        'latitude' => 9.5762577,
        'longitude' => 44.0338193,
    ];
@endphp

<section class="py-5 bg-light facilities-section">
    <div class="container py-4">
        <div class="row align-items-end g-4 mb-5">
            <div class="col-lg-7 facilities-reveal">
                <span class="text-danger fw-bold text-uppercase small tracking-wider">Campus overview</span>
                <h2 class="section-heading fw-bold mt-1 mb-2">A learning environment designed for focus, connection, and support.</h2>
            </div>
            <div class="col-lg-5 facilities-reveal">
                <p class="text-muted mb-0">From quiet study spaces to specialist laboratories and student support services, each area of campus is shaped to strengthen the student experience.</p>
            </div>
        </div>

        <div class="row g-4">
            @foreach($featuredFacilities as $facility)
                <div class="col-md-6 col-lg-4 facilities-reveal">
                    <article class="facility-highlight-card h-100">
                        <div class="facility-highlight-image">
                            <img src="{{ $facility['image'] }}" alt="{{ $facility['title'] }} at Tima-Ade University" loading="lazy">
                            <span class="facility-highlight-icon"><i class="bi {{ $facility['icon'] }}"></i></span>
                        </div>
                        <div class="facility-highlight-body">
                            <span class="facility-highlight-label">Featured space</span>
                            <h3 class="h5 fw-bold mb-2">{{ $facility['title'] }}</h3>
                            <p class="text-muted small mb-0">{{ $facility['description'] }}</p>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="facility-grid" class="py-5 bg-white facilities-section">
    <div class="container py-4">
        <div class="row align-items-end g-4 mb-5">
            <div class="col-lg-7 facilities-reveal">
                <span class="text-danger fw-bold text-uppercase small tracking-wider">Campus infrastructure</span>
                <h2 class="section-heading fw-bold mt-1 mb-2">A considered environment for every part of campus life.</h2>
            </div>
            <div class="col-lg-5 facilities-reveal"><p class="text-muted mb-0">Tima-Ade University provides modern, secure and accessible facilities designed to enhance teaching, research and student life.</p></div>
        </div>

        <div class="row g-4">
            @foreach($facilities as $f)
                <div class="col-md-6 col-lg-4 facilities-reveal">
                    <article class="facility-card h-100">
                        <button type="button" class="facility-image-button" data-facility-image="{{ $f['image'] }}" data-facility-title="{{ $f['title'] }}" data-facility-description="{{ $f['description'] }}" aria-label="View {{ $f['title'] }} image">
                            <img src="{{ $f['image'] }}" alt="{{ $f['title'] }} at Tima-Ade University" loading="lazy">
                            <span class="facility-image-action"><i class="bi bi-arrows-fullscreen"></i></span>
                        </button>
                        <div class="facility-card-body">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="facility-icon"><i class="bi {{ $f['icon'] }}"></i></span>
                                <div><h3 class="h5 fw-bold mb-1">{{ $f['title'] }}</h3><span class="facility-index">0{{ $loop->iteration }}</span></div>
                            </div>
                            <p class="text-muted small mb-4">{{ $f['description'] }}</p>
                            <button type="button" class="facility-detail-button mt-auto" data-facility-image="{{ $f['image'] }}" data-facility-title="{{ $f['title'] }}" data-facility-description="{{ $f['description'] }}">View facility <i class="bi bi-arrow-up-right"></i></button>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="campus-access" class="py-5 bg-light facilities-section">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-5 facilities-reveal">
                <span class="text-danger fw-bold text-uppercase small tracking-wider">Campus access</span>
                <h2 class="section-heading fw-bold mt-1 mb-3">Find your way around campus.</h2>
                <p class="text-muted">Our campus is fully accessible and includes wayfinding signs, ramps, elevators and dedicated transport services for students and visitors. Use the interactive map below to locate buildings and services.</p>
                <ul class="list-unstyled small text-muted facilities-checklist">
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>24/7 Campus Security & CCTV</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>On-site Health Clinic & Counseling</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>High-speed campus Wi-Fi across all buildings</li>
                </ul>
            </div>
            <div class="col-lg-7 facilities-reveal">
                <div class="facility-map-frame" data-facility-map data-campus-name="{{ $hargeisaCampus['name'] }}" data-campus-city="{{ $hargeisaCampus['city'] }}" data-campus-latitude="{{ $hargeisaCampus['latitude'] }}" data-campus-longitude="{{ $hargeisaCampus['longitude'] }}">
                    <div class="facility-map-status" data-map-status role="status" aria-live="polite"><span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span>Loading Hargeisa map...</span></div>
                    <div class="facility-map-canvas" data-map-canvas role="application" aria-label="Interactive map showing Tima-Ade University Hargeisa Campus"></div>
                    <section class="facility-navigation-panel" data-navigation-panel aria-label="Campus navigation" hidden>
                        <div class="facility-navigation-heading"><strong>Your Location</strong><span class="facility-navigation-arrow">↓</span><strong>Tima-Ade University — Hargeisa Campus</strong><span data-route-destination>Hargeisa, Somaliland</span><span data-location-status role="status" aria-live="polite" hidden></span></div>
                        <div class="facility-navigation-modes" role="group" aria-label="Travel mode">
                            <button type="button" class="is-active" data-route-mode="walking"><i class="bi bi-person-walking" aria-hidden="true"></i> Walk</button>
                            <button type="button" data-route-mode="driving"><i class="bi bi-car-front-fill" aria-hidden="true"></i> Drive</button>
                        </div>
                        <div class="facility-navigation-summary" data-route-summary>Choose a travel mode to calculate your route.</div>
                        <div class="facility-navigation-next" data-route-next hidden><span>Next</span><strong></strong></div>
                        <button type="button" class="facility-navigation-start" data-route-start disabled>Start</button>
                        <ol class="facility-navigation-steps" data-route-steps hidden></ol>
                        <button type="button" class="facility-navigation-stop" data-route-stop>End navigation</button>
                    </section>
                    <div class="facility-map-fallback" data-map-fallback role="alert" hidden><i class="bi bi-map" aria-hidden="true"></i><p>We couldn't load the interactive map.</p><a class="btn btn-outline-danger btn-sm" data-directions-link href="https://www.google.com/maps/dir/?api=1&amp;destination={{ $hargeisaCampus['latitude'] }},{{ $hargeisaCampus['longitude'] }}" target="_blank" rel="noopener">Open directions in Google Maps <i class="bi bi-box-arrow-up-right ms-1" aria-hidden="true"></i></a></div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="facility-lightbox" id="facilityLightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="facilityLightboxTitle">
    <div class="facility-lightbox-panel">
        <button type="button" class="facility-lightbox-close" data-facility-close aria-label="Close facility image"><i class="bi bi-x-lg"></i></button>
        <img id="facilityLightboxImage" src="" alt="">
        <div class="facility-lightbox-copy"><span class="text-danger fw-bold text-uppercase small">Facility detail</span><h2 id="facilityLightboxTitle"></h2><p id="facilityLightboxDescription"></p></div>
    </div>
</div>

<section class="py-5 bg-navy text-white text-center facilities-cta">
    <div class="container py-4 facilities-reveal">
        <span class="text-danger fw-bold text-uppercase small tracking-wider">Visit the campus</span>
        <h2 class="fw-bold mt-2 mb-3">Experience the facilities firsthand.</h2>
        <p class="mb-4">Schedule a guided tour to experience the facilities firsthand and speak with our admissions team.</p>
        <a href="{{ route('public.contact') }}" class="btn btn-crimson btn-lg px-4 facilities-button">Schedule a Campus Visit <i class="bi bi-arrow-up-right ms-1"></i></a>
    </div>
</section>
@endsection