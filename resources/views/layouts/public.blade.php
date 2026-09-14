<!DOCTYPE html>
<html lang="en" class="public-document">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Excellence in Education') — Tima-Ade University</title>
    <meta name="description" content="@yield('meta_description', 'Tima-Ade University is a premier academic institution dedicated to academic excellence, innovation, and leadership.')">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Tima-Ade University">
    <meta property="og:title" content="@yield('title', 'Excellence in Education') — Tima-Ade University">
    <meta property="og:description" content="@yield('meta_description', 'Tima-Ade University is a premier academic institution dedicated to academic excellence, innovation, and leadership.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/home/campus.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @if(request()->routeIs('public.facilities'))
        <!-- Leaflet map -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    @endif
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">

    @stack('styles')
</head>
@php
    $publicPageClass = '';
    if (request()->routeIs('public.home')) {
        $publicPageClass = 'home-page';
    } elseif (request()->routeIs('public.about')) {
        $publicPageClass = 'about-page';
    } elseif (request()->routeIs('public.academics')) {
        $publicPageClass = 'academics-page';
    } elseif (request()->routeIs('public.programs*')) {
        $publicPageClass = 'programs-page';
    } elseif (request()->routeIs('public.student-life*') || request()->routeIs('public.notices*')) {
        $publicPageClass = 'student-life-page';
    } elseif (request()->routeIs('public.facilities') || request()->routeIs('public.e-campus*')) {
        $publicPageClass = 'facilities-page e-campus-page';
    } elseif (request()->routeIs('public.admissions*')) {
        $publicPageClass = 'admissions-page';
    }
@endphp
<body class="public-body page-enter {{ $publicPageClass }}">
    <a class="skip-link" href="#main-content">Skip to main content</a>

    <div class="public-site-header">
    <!-- Top Institutional Utility Bar -->
    <div class="top-announcement-bar">
        <div class="container d-flex justify-content-between align-items-center flex-wrap py-1 gap-2">
            <nav class="institutional-utility-nav" aria-label="Institutional portals">
                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                        <a href="{{ route('hemis') }}" aria-label="Open University Administration Portal">University Administration Portal</a>
                    @endif
                    @if(auth()->user()->isTeacher() || auth()->user()->isStudent() || auth()->user()->isParent())
                        <a href="{{ route('exams.results') }}" aria-label="Open University Portal">University Portal</a>
                    @endif
                @else
                    <a href="{{ route('admin.login') }}" aria-label="Sign in to the University Admin Portal">University Admin Sign In</a>
                    <a href="{{ route('university.login') }}" aria-label="Sign in to the University Portal">University Portal Sign In</a>
                @endauth
            </nav>
            <div class="header-contact-details d-flex align-items-center gap-3 small text-light">
                <a class="header-contact-link location-gold" href="{{ route('public.facilities') }}#campus-access" aria-label="View the Tima-Ade University campus map"><i class="bi bi-geo-alt-fill text-danger me-1" aria-hidden="true"></i> Campus Location</a>
                <span class="d-none d-md-inline">•</span>
                <a class="header-contact-link" href="tel:+15552345678" aria-label="Call Tima-Ade University at +1 (555) 234-5678"><i class="bi bi-telephone-fill text-danger me-1" aria-hidden="true"></i> +1 (555) 234-5678</a>
                <span class="d-none d-lg-inline">•</span>
                <a class="header-contact-link" href="mailto:admissions@timaade.edu" aria-label="Email Tima-Ade University at admissions@timaade.edu"><i class="bi bi-envelope-fill text-danger me-1" aria-hidden="true"></i> admissions@timaade.edu</a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="public-header sticky-top">
        <nav class="navbar navbar-expand-lg navbar-dark bg-navy">
            <div class="container">
                <!-- Brand -->
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('public.home') }}">
                    <img src="{{ asset('images/tima-ade-university-logo-premium.svg') }}" alt="Tima-Ade University logo" class="brand-logo-image">
                    <div class="brand-text">
                        <span class="brand-name">Tima-Ade University</span>
                        <span class="brand-tagline location-gold">Education that transforms lives</span>
                    </div>
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbar" aria-controls="publicNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation Items -->
                <div class="collapse navbar-collapse" id="publicNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-1">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('public.home') ? 'active' : '' }}" href="{{ route('public.home') }}">
                                Home
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('public.about*') ? 'active' : '' }}" href="{{ route('public.about') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                About
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('public.about') }}">About Tima-Ade</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.about.board') }}">Board of Directors</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.about.campuses') }}">Campuses</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('public.programs*') || request()->routeIs('public.academics') ? 'active' : '' }}" href="{{ route('public.programs') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Programs
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('public.programs') }}">Academic Programs</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.programs.calendar') }}">Academic Calendar</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.programs.careers') }}">Careers &amp; Certifications</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('public.admissions*') ? 'active' : '' }}" href="{{ route('public.admissions') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Admissions
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end admissions-menu">
                                <li><a class="dropdown-item" href="{{ route('public.admissions') }}">Admissions Overview</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.admissions.how-to-apply') }}">How to Apply</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.admissions.requirements') }}">Admission Requirements</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.admissions.process') }}">Application Process</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.admissions.dates') }}">Important Dates</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.admissions.fees') }}">Fees &amp; Funding</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.admissions.international') }}">International Admissions</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.admissions.faq') }}">Frequently Asked Questions</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('public.facilities') || request()->routeIs('public.e-campus*') || request()->routeIs('dashboard') || request()->routeIs('student.*') || request()->routeIs('teacher.*') ? 'active' : '' }}" href="{{ route('public.facilities') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                E-Campus
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end e-campus-menu">
                                <li><a class="dropdown-item" href="{{ route('public.facilities') }}">E-Campus Overview</a></li>
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">University Portal <span class="e-campus-item-note">Sign in</span></a></li>
                                <li><a class="dropdown-item" href="{{ route('public.e-campus.learning') }}">Learning &amp; Courses</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.e-campus.resources') }}">Academic Resources</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.e-campus.student-services') }}">Student Services</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.e-campus.help') }}">Help &amp; Support</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('public.student-life*') || request()->routeIs('public.notices*') ? 'active' : '' }}" href="{{ route('public.student-life') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Student Life
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end student-life-menu">
                                <li><a class="dropdown-item" href="{{ route('public.student-life.alumni') }}">Alumni</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.student-life.research') }}">Research</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.student-life.news') }}">News</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('public.contact') ? 'active' : '' }}" href="{{ route('public.contact') }}">
                                Contact
                            </a>
                        </li>
                        <li class="nav-item public-header-actions d-flex gap-2 align-items-center">
                            <form action="{{ route('public.search') }}" method="GET" role="search" class="public-search-form">
                                <label class="visually-hidden" for="global-search">Search Tima-Ade University</label>
                                <i class="bi bi-search" aria-hidden="true"></i>
                                <input id="global-search" name="q" type="search" value="{{ request()->routeIs('public.search') ? request('q') : '' }}" placeholder="Search" autocomplete="off">
                                <button type="submit" aria-label="Submit search"><span class="visually-hidden">Search</span><i class="bi bi-search" aria-hidden="true"></i></button>
                            </form>
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn btn-crimson btn-sm px-3 shadow-sm">
                                    <i class="bi bi-grid-fill me-1"></i> Dashboard
                                </a>
                            @else
                                <a href="{{ route('public.apply') }}" class="btn btn-crimson btn-sm px-3">
                                    Apply Now
                                </a>
                            @endauth
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    </div>

    <!-- Content Area -->
    <main id="main-content" tabindex="-1">
        @include('partials._alerts')
        @yield('content')
    </main>

    <!-- Public Footer -->
    <footer class="public-footer">
        <div class="footer-top py-5">
            <div class="container">
                <div class="row g-4">
                    <!-- About Column -->
                    <div class="col-lg-4 col-md-6">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <img src="{{ asset('images/tima-ade-university-logo-premium.svg') }}" alt="Tima-Ade University logo" class="brand-logo-image">
                            <div>
                                <h4 class="text-white fw-bold mb-0">Tima-Ade University</h4>
                                <span class="text-muted small tagline-gold">Excellence in Academics & Innovation</span>
                            </div>
                        </div>
                        <p class="text-light small mb-4 opacity-75">
                            Empowering future leaders through rigorous curriculum, modern scientific labs, and holistic character development. Registered and accredited with international academic standards.
                        </p>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/" class="social-btn" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="bi bi-facebook" aria-hidden="true"></i></a>
                            <a href="https://x.com/" class="social-btn" target="_blank" rel="noopener noreferrer" aria-label="X"><i class="bi bi-twitter-x" aria-hidden="true"></i></a>
                            <a href="https://www.instagram.com/" class="social-btn" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a>
                            <a href="https://www.linkedin.com/" class="social-btn" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="bi bi-linkedin" aria-hidden="true"></i></a>
                            <a href="https://www.youtube.com/" class="social-btn" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="bi bi-youtube" aria-hidden="true"></i></a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-6 col-6">
                        <h6 class="footer-heading">Quick Links</h6>
                        <ul class="footer-links">
                            <li><a href="{{ route('public.home') }}"><i class="bi bi-chevron-right me-1 small"></i> Home</a></li>
                            <li><a href="{{ route('public.about') }}"><i class="bi bi-chevron-right me-1 small"></i> About</a></li>
                            <li><a href="{{ route('public.academics') }}"><i class="bi bi-chevron-right me-1 small"></i> Academics</a></li>
                            <li><a href="{{ route('public.faculty') }}"><i class="bi bi-chevron-right me-1 small"></i> Faculty Directory</a></li>
                            <li><a href="{{ route('public.admissions') }}"><i class="bi bi-chevron-right me-1 small"></i> Admissions 2026</a></li>
                        </ul>
                    </div>

                    <!-- Campus & Academics -->
                    <div class="col-lg-3 col-md-6 col-6">
                        <h6 class="footer-heading">Portals & Services</h6>
                        <ul class="footer-links">
                            <li><a href="{{ route('admin.login') }}"><i class="bi bi-chevron-right me-1 small"></i> University Admin Portal</a></li>
                            <li><a href="{{ route('university.login') }}"><i class="bi bi-chevron-right me-1 small"></i> University Portal</a></li>
                            <li><a href="{{ route('public.notices') }}"><i class="bi bi-chevron-right me-1 small"></i> Official Noticeboard</a></li>
                            <li><a href="{{ route('public.contact') }}"><i class="bi bi-chevron-right me-1 small"></i> Campus Inquiries</a></li>
                        </ul>
                    </div>

                    <!-- Contact & Location -->
                    <div class="col-lg-3 col-md-6">
                        <h6 class="footer-heading">Campus Address</h6>
                        <ul class="footer-contact-list">
                            <li>
                                <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                                <span class="location-gold">Campus Location</span>
                            </li>
                            <li>
                                <i class="bi bi-telephone-fill text-danger me-2"></i>
                                <span>+1 (555) 234-5678 / +1 (555) 234-5679</span>
                            </li>
                            <li>
                                <i class="bi bi-envelope-fill text-danger me-2"></i>
                                <span>info@timaade.edu</span>
                            </li>
                            <li>
                                <i class="bi bi-clock-fill text-danger me-2"></i>
                                <span>Mon - Fri: 8:00 AM - 5:00 PM</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom py-3 border-top border-secondary-subtle">
            <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2 text-muted small">
                <div>&copy; {{ date('Y') }} Tima-Ade University. All Rights Reserved.</div>
                <div class="d-flex gap-3">
                    <a href="{{ route('public.about') }}" class="text-muted text-decoration-none">Privacy Policy</a>
                    <a href="{{ route('public.about') }}" class="text-muted text-decoration-none">Terms of Service</a>
                    <a href="{{ route('admin.login') }}" class="text-danger text-decoration-none fw-semibold">University Admin Portal</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @if(request()->routeIs('public.facilities'))
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @endif
    <script src="{{ asset('js/public.js') }}"></script>
    <script src="{{ asset('js/animations.js') }}"></script>
    @stack('scripts')
</body>
</html>
