@extends('layouts.public')

@section('title', 'Empowering Tomorrow\'s Leaders')
@section('meta_description', 'Tima-Ade University in Gabiley, Somaliland — explore academic programs, campus life, admissions, and the digital E-Campus of a growing institution focused on purposeful education.')

@section('content')
<!-- ===== HERO SECTION ===== -->
<section class="hero-section text-white position-relative">
    <img class="home-hero-media home-hero-image" src="{{ asset('images/home/campus.jpg') }}" alt="Tima-Ade University campus environment">
    <div class="hero-backdrop"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-center min-vh-75 py-5">
            <div class="col-lg-7">
                <span class="badge badge-hero-pill mb-3">
                    <i class="bi bi-stars me-1 text-warning"></i> Admissions Open for Academic Year 2026-2027
                </span>
                <h1 class="hero-title display-4 fw-extrabold mb-3">
                    Inspiring Intellect. Building Character. Shaping the Future.
                </h1>
                <p class="hero-subtitle lead mb-4 opacity-90">
                    Welcome to <span class="text-white fw-bold">Tima-Ade University</span> — an academic community fostering discovery, analytical excellence, and student leadership.
                </p>
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <a href="{{ route('public.apply') }}" class="btn btn-crimson btn-lg px-4 shadow">
                        <i class="bi bi-pencil-square me-2"></i> Apply Now
                    </a>
                    <a href="{{ route('public.academics') }}" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-book-half me-2"></i> Explore Programs
                    </a>
                </div>
                <div class="d-flex align-items-center gap-4 text-light small pt-3 border-top border-secondary">
                    <div><i class="bi bi-check-circle-fill text-danger me-1"></i> Accredited Curriculum</div>
                    <div><i class="bi bi-check-circle-fill text-danger me-1"></i> Dedicated Faculty</div>
                    <div><i class="bi bi-check-circle-fill text-danger me-1"></i> Modern STEM Labs</div>
                </div>
            </div>

            <!-- Quick Action Card in Hero -->
            <div class="col-lg-5 mt-5 mt-lg-0">
                <div class="hero-action-card card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4 text-dark">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="action-icon action-logo-image">
                                <img src="{{ asset('images/tima-ade-university-logo-premium.svg') }}" alt="Tima-Ade University logo">
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">University Portals</h5>
                                <small class="text-muted">Choose the portal for your role</small>
                            </div>
                        </div>
                        <p class="small text-muted mb-3">
                            Students, parents, faculty, administrators, and staff can securely access the tools assigned to their role.
                        </p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('university.login') }}" class="btn btn-navy py-2 fw-semibold">
                                <i class="bi bi-mortarboard-fill me-2 text-danger"></i> University Portal Sign In
                            </a>
                            <a href="{{ route('admin.login') }}" class="btn btn-outline-primary py-2 fw-semibold">
                                <i class="bi bi-shield-lock-fill me-2"></i> University Admin Sign In
                            </a>
                            <a href="{{ route('public.contact') }}" class="btn btn-light border py-2 fw-semibold">
                                <i class="bi bi-chat-dots-fill me-2 text-primary"></i> Inquire with Admissions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== STATS COUNTER STRIP ===== -->
<section class="stats-counter-strip py-4 bg-navy text-white">
    <div class="container">
        <div class="history-stats-grid">
            <article class="history-stat-card reveal-on-scroll">
                <div class="history-stat-value" data-target="2010">2010</div>
                <p class="history-stat-copy">Founded as an institute</p>
            </article>

            <article class="history-stat-card reveal-on-scroll">
                <div class="history-stat-value" data-target="2016">2016</div>
                <p class="history-stat-copy">Upgraded to university status</p>
            </article>

            <article class="history-stat-card reveal-on-scroll">
                <div class="history-stat-value" data-target="6">6</div>
                <p class="history-stat-copy">Academic faculties</p>
            </article>

            <article class="history-stat-card reveal-on-scroll">
                <div class="history-stat-value" data-target="4">4</div>
                <p class="history-stat-copy">Campuses and E-Campus</p>
            </article>
        </div>
    </div>
</section>

<div class="home-hero-transition" aria-hidden="true"></div>

@include('public.components.temporary-video-showcase')

@php
    $promoVideoUrl = null;
    foreach (['videos/home.mp4', 'videos/tima-ade-university-promo.mp4'] as $candidate) {
        if (file_exists(public_path($candidate))) {
            $promoVideoUrl = asset($candidate);
            break;
        }
    }
@endphp

@if($promoVideoUrl)
<section class="home-cinematic-section home-section-reveal" aria-labelledby="home-cinematic-title">
    <div class="container">
        <div class="home-cinematic-copy reveal-on-scroll">
            <span class="text-danger fw-bold text-uppercase small tracking-wider">Tima-Ade University</span>
            <h2 id="home-cinematic-title" class="section-heading fw-bold mt-2 mb-0">A vibrant campus experience designed for ambition.</h2>
        </div>

        <div class="home-cinematic-frame reveal-on-scroll" aria-label="Tima-Ade University promotional video showcase">
            <div class="home-cinematic-video-shell" data-video-parallax>
                <video
                    class="home-cinematic-video"
                    autoplay
                    muted
                    loop
                    playsinline
                    preload="metadata"
                    poster="{{ asset('images/home/campus.jpg') }}"
                    aria-label="Official Tima-Ade University promotional video"
                    title="Official Tima-Ade University promotional video">
                    <source src="{{ $promoVideoUrl }}" type="video/mp4">
                    Your browser does not support HTML5 video.
                </video>
            </div>
        </div>
    </div>
</section>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const promoVideos = document.querySelectorAll('.home-cinematic-video');

        if (!promoVideos.length) {
            return;
        }

        promoVideos.forEach((video) => {
            const section = video.closest('.home-cinematic-section');
            if (!section || reduceMotion) {
                video.pause();
                return;
            }

            const syncVideoState = () => {
                const rect = section.getBoundingClientRect();
                const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
                const isInView = rect.top < viewportHeight * 0.9 && rect.bottom > viewportHeight * 0.18;

                if (isInView) {
                    section.classList.add('is-pinned');
                    video.play().catch(() => {});
                    return;
                }

                section.classList.remove('is-pinned');
                video.pause();
            };

            const sectionObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    const rect = entry.boundingClientRect;
                    const isInView = rect.top < window.innerHeight * 0.9 && rect.bottom > window.innerHeight * 0.15;

                    if (isInView) {
                        section.classList.add('is-pinned');
                        video.play().catch(() => {});
                        return;
                    }

                    section.classList.remove('is-pinned');
                    video.pause();
                });
            }, {
                threshold: [0, 0.1, 0.25, 0.5, 0.75, 1]
            });

            sectionObserver.observe(section);
            window.addEventListener('scroll', syncVideoState, { passive: true });
            window.addEventListener('resize', syncVideoState);
            syncVideoState();
        });
    });
</script>

<!-- ===== CORE PILLARS SECTION ===== -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold text-uppercase small tracking-wider">Why Choose Tima-Ade University</span>
            <h2 class="section-heading fw-bold mt-1">A Comprehensive Environment for Lifelong Success</h2>
            <p class="text-muted">We provide an interconnected ecosystem of rigorous academic learning, personalized mentorship, and collaborative innovation.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4 reveal-on-scroll">
                <div class="pillar-card card h-100 p-4 border-0 shadow-sm rounded-4 text-center">
                    <div class="pillar-icon mx-auto mb-3">
                        <i class="bi bi-laptop"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Modern Technology & Labs</h5>
                    <p class="text-muted small mb-0">High-speed computing labs, digital robotics suites, and modern science equipment supporting practical learning.</p>
                </div>
            </div>
            <div class="col-md-4 reveal-on-scroll" style="--reveal-delay: 100ms;">
                <div class="pillar-card card h-100 p-4 border-0 shadow-sm rounded-4 text-center">
                    <div class="pillar-icon mx-auto mb-3" style="background: rgba(225, 29, 72, 0.1); color: #be123c;">
                        <i class="bi bi-award"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Renowned Faculty</h5>
                    <p class="text-muted small mb-0">Accomplished educators and mentors who provide 1-on-1 guidance, tutorial clinics, and research supervision.</p>
                </div>
            </div>
            <div class="col-md-4 reveal-on-scroll" style="--reveal-delay: 200ms;">
                <div class="pillar-card card h-100 p-4 border-0 shadow-sm rounded-4 text-center">
                    <div class="pillar-icon mx-auto mb-3" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                        <i class="bi bi-trophy"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Athletics & Character</h5>
                    <p class="text-muted small mb-0">Competitive varsity sports, leadership debates, music, and community service fostering well-rounded graduates.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== ACADEMIC PROGRAMS ===== -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="text-danger fw-bold text-uppercase small tracking-wider">Curriculum & Grades</span>
                <h2 class="section-heading fw-bold mt-1 mb-0">Academic Grade Levels</h2>
            </div>
            <a href="{{ route('public.academics') }}" class="btn btn-outline-dark">
                View All Programs <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse($classes as $class)
                <div class="col-md-6 col-lg-3 reveal-on-scroll">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden program-card">
                        <div class="program-card-header bg-navy text-white p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-crimson">Grade {{ $class->grade_level ?? 'Level' }}</span>
                                <i class="bi bi-book fs-4"></i>
                            </div>
                            <h4 class="fw-bold mb-0 text-white">{{ $class->name }}</h4>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted small mb-3">{{ $class->description ?? 'Comprehensive college preparatory curriculum with elective choices.' }}</p>
                            <div class="small mb-2">
                                <strong>Sections:</strong>
                                @foreach($class->sections as $sec)
                                    <span class="badge bg-light text-dark border">{{ $sec->name }}</span>
                                @endforeach
                            </div>
                            <div class="small text-muted">
                                <strong>Subjects:</strong> {{ $class->subjects->count() }} specialized modules
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0 p-3 pt-0">
                            <a href="{{ route('public.academics') }}" class="btn btn-sm btn-outline-primary w-100">
                                Program Details
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4 text-muted">Academic classes will be listed here.</div>
            @endforelse
        </div>
    </div>
</section>

<!-- ===== LATEST NOTICES & ANNOUNCEMENTS ===== -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="text-danger fw-bold text-uppercase small tracking-wider">Updates & Bulletin</span>
                <h2 class="section-heading fw-bold mt-1 mb-0">Official Noticeboard</h2>
            </div>
            <a href="{{ route('public.notices') }}" class="btn btn-outline-dark">
                View All Notices <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse($notices as $notice)
                <div class="col-lg-4 col-md-6 reveal-on-scroll">
                    <div class="card h-100 border rounded-4 shadow-sm notice-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                {{ $notice->category }}
                            </span>
                            <span class="small text-muted">
                                <i class="bi bi-calendar3 me-1"></i> {{ $notice->published_date->format('M d, Y') }}
                            </span>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">
                            <a href="{{ route('public.notices.single', $notice) }}" class="text-dark text-decoration-none">
                                {{ $notice->title }}
                            </a>
                        </h5>
                        <p class="text-muted small mb-3 flex-grow-1">
                            {{ Str::limit($notice->content, 120) }}
                        </p>
                        <a href="{{ route('public.notices.single', $notice) }}" class="small fw-semibold text-danger text-decoration-none">
                            Read Full Notice <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4 text-muted">No notices published yet.</div>
            @endforelse
        </div>
    </div>
</section>

<!-- ===== FACULTY HIGHLIGHTS ===== -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="text-danger fw-bold text-uppercase small tracking-wider">Meet the Educators</span>
                <h2 class="section-heading fw-bold mt-1 mb-0">Faculty Leadership</h2>
            </div>
            <a href="{{ route('public.faculty') }}" class="btn btn-outline-dark">
                View Full Faculty <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse($teachers as $teacher)
                <div class="col-lg-3 col-md-6 reveal-on-scroll">
                    <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-4 faculty-card">
                        <span class="home-avatar home-faculty-fallback mx-auto mb-3" aria-label="{{ $teacher->user->name }}">{{ strtoupper(substr($teacher->user->name, 0, 1)) }}</span>
                        <h5 class="fw-bold mb-1 text-dark">{{ $teacher->user->name }}</h5>
                        <span class="text-danger small fw-semibold d-block mb-2">{{ $teacher->specialization ?? 'Faculty' }}</span>
                        <p class="text-muted small mb-3">{{ $teacher->qualification ?? 'Educator at Tima-Ade University' }}</p>
                        <a href="{{ route('public.faculty') }}" class="btn btn-sm btn-outline-secondary w-100">
                            Faculty Bio
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-4 text-muted">Faculty profiles will appear here.</div>
            @endforelse
        </div>
    </div>
</section>

<!-- ===== RESEARCH & INNOVATION ===== -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-danger fw-bold text-uppercase small tracking-wider">Research</span>
                <h2 class="section-heading fw-bold mt-1 mb-0">Research & Innovation Highlights</h2>
            </div>
            <a href="{{ route('public.academics') }}" class="btn btn-outline-dark">View Research Programs</a>
        </div>

        <div class="row g-4 home-research-grid">
            <div class="col-lg-7"><article class="home-research-feature rounded-4 overflow-hidden shadow-sm"><div class="home-research-image"><img src="/images/home/lab.jpg" alt="Learning and practical work in a university laboratory" loading="lazy"><span class="home-research-label"><i class="bi bi-stars"></i> Learning in practice</span></div><div class="p-4 p-lg-5 bg-navy text-white"><h5 class="fw-bold">Explore academic learning</h5><p class="small text-white-50 mb-3">Discover the classes, subjects, facilities, and faculty that shape the Tima-Ade University learning experience.</p><a href="{{ route('public.academics') }}" class="btn btn-sm btn-outline-light">Explore academics <i class="bi bi-arrow-up-right ms-1"></i></a></div></article></div>
            <div class="col-lg-5"><article class="card h-100 border-0 rounded-4 shadow-sm overflow-hidden home-research-side"><div class="home-research-side-image"><img src="/images/home/tech.jpg" alt="Technology learning space at Tima-Ade University" loading="lazy"></div><div class="card-body p-4"><span class="home-support-icon home-support-icon-blue"><i class="bi bi-cpu"></i></span><h5 class="fw-bold mt-3">Campus facilities</h5><p class="text-muted small">See the spaces available for study, research, technology, and student life.</p><a href="{{ route('public.facilities') }}" class="btn btn-sm btn-outline-primary">Explore facilities <i class="bi bi-arrow-up-right ms-1"></i></a></div></article></div>
        </div>
    </div>
</section>

<!-- ===== STUDENT LIFE ===== -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-danger fw-bold text-uppercase small tracking-wider">Student Life</span>
                <h2 class="section-heading fw-bold mt-1 mb-0">Student Life & Campus Community</h2>
            </div>
            <a href="{{ route('public.contact') }}" class="btn btn-outline-dark">Contact Student Services</a>
        </div>

        <div class="row g-4 home-image-card-grid">
            <div class="col-md-4">
                <article class="card h-100 border-0 rounded-4 shadow-sm overflow-hidden home-image-card">
                    <div class="home-card-image"><img src="/images/home/community.jpg" alt="Students collaborating together on campus" loading="lazy"><span class="home-card-icon"><i class="bi bi-people-fill"></i></span></div>
                    <div class="card-body p-4"><h5 class="fw-bold">Community & connection</h5><p class="text-muted small">Learn how student activities and campus life can support a connected university experience.</p><a href="{{ route('public.contact') }}" class="stretched-link small fw-bold text-danger">Ask student services <i class="bi bi-arrow-up-right"></i></a></div>
                </article>
            </div>
            <div class="col-md-4">
                <article class="card h-100 border-0 rounded-4 shadow-sm overflow-hidden home-image-card">
                    <div class="home-card-image"><img src="/images/home/event.jpg" alt="Students performing at a campus event" loading="lazy"><span class="home-card-icon home-card-icon-blue"><i class="bi bi-music-note-list"></i></span></div>
                    <div class="card-body p-4"><h5 class="fw-bold">Campus activities</h5><p class="text-muted small">Connect with the university team to learn about activities, events, and opportunities on campus.</p><a href="{{ route('public.contact') }}" class="stretched-link small fw-bold text-danger">Explore campus life <i class="bi bi-arrow-up-right"></i></a></div>
                </article>
            </div>
            <div class="col-md-4">
                <article class="card h-100 border-0 rounded-4 shadow-sm overflow-hidden home-image-card">
                    <div class="home-card-image"><img src="/images/home/campus.jpg" alt="Quiet campus garden and walking path" loading="lazy"><span class="home-card-icon home-card-icon-green"><i class="bi bi-heart-fill"></i></span></div>
                    <div class="card-body p-4"><h5 class="fw-bold">Student support</h5><p class="text-muted small">Find the right contact for questions about student support and the university experience.</p><a href="{{ route('public.contact') }}" class="stretched-link small fw-bold text-danger">Contact the university <i class="bi bi-arrow-up-right"></i></a></div>
                </article>
            </div>
        </div>
    </div>
</section>

<!-- ===== ADMISSIONS PATHWAYS ===== -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-danger fw-bold text-uppercase small tracking-wider">Next steps</span>
                <h2 class="section-heading fw-bold mt-1 mb-0">Plan your Tima-Ade journey</h2>
            </div>
            <a href="{{ route('public.admissions') }}" class="btn btn-outline-dark">Admissions <i class="bi bi-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-4 home-support-grid">
            <div class="col-md-4"><a href="{{ route('public.academics') }}" class="card h-100 border-0 rounded-4 p-4 shadow-sm home-support-card text-decoration-none"><span class="home-support-icon"><i class="bi bi-book-half"></i></span><h5 class="fw-bold text-dark mt-4">Explore academics</h5><p class="text-muted small">Review the current classes, subjects, and academic structure.</p><span class="small fw-bold text-danger mt-auto">View academics <i class="bi bi-arrow-right"></i></span></a></div>
            <div class="col-md-4"><a href="{{ route('public.facilities') }}" class="card h-100 border-0 rounded-4 p-4 shadow-sm home-support-card text-decoration-none"><span class="home-support-icon home-support-icon-blue"><i class="bi bi-buildings"></i></span><h5 class="fw-bold text-dark mt-4">Visit the campus</h5><p class="text-muted small">Explore the university's learning, research, and student spaces.</p><span class="small fw-bold text-danger mt-auto">See facilities <i class="bi bi-arrow-right"></i></span></a></div>
            <div class="col-md-4"><a href="{{ route('public.contact') }}" class="card h-100 border-0 rounded-4 p-4 shadow-sm home-support-card text-decoration-none"><span class="home-support-icon home-support-icon-green"><i class="bi bi-chat-dots"></i></span><h5 class="fw-bold text-dark mt-4">Talk to admissions</h5><p class="text-muted small">Ask the university team about applying, requirements, and next steps.</p><span class="small fw-bold text-danger mt-auto">Contact admissions <i class="bi bi-arrow-right"></i></span></a></div>
        </div>
    </div>
</section>

<!-- ===== UPCOMING EVENTS & NEWS ===== -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-danger fw-bold text-uppercase small tracking-wider">Events</span>
                <h2 class="section-heading fw-bold mt-1 mb-0">Upcoming Events & Latest News</h2>
            </div>
            <a href="{{ route('public.notices') }}" class="btn btn-outline-dark">View All</a>
        </div>

        <div class="row g-4">
            @if(isset($events) && $events->count())
                @foreach($events as $event)
                    <div class="col-md-6 col-lg-4">
                        <article class="card h-100 border-0 rounded-4 shadow-sm overflow-hidden home-event-card">
                            <div class="home-event-image"><img src="/images/home/event.jpg" alt="Tima-Ade University event" loading="lazy">@if($event->starts_at)<span class="home-event-date"><strong>{{ $event->starts_at->format('d') }}</strong><small>{{ $event->starts_at->format('M Y') }}</small></span>@endif</div>
                            <div class="card-body p-4 d-flex flex-column"><div class="small text-danger fw-bold text-uppercase mb-2"><i class="bi bi-calendar-event me-1"></i> Upcoming event</div><h6 class="fw-bold">{{ $event->title }}</h6><p class="text-muted small mb-2">{{ Str::limit($event->content, 120) }}</p>@if($event->location)<span class="small text-muted mb-3"><i class="bi bi-geo-alt me-1"></i>{{ $event->location }}</span>@endif<a href="{{ route('public.contact') }}" class="btn btn-sm btn-outline-primary mt-auto align-self-start">Ask about this event <i class="bi bi-arrow-up-right ms-1"></i></a></div>
                        </article>
                    </div>
                @endforeach
            @else
                <div class="col-12 text-center text-muted">No upcoming events available.</div>
            @endif
        </div>
    </div>
</section>

<!-- ===== TESTIMONIALS & PARTNERS ===== -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <span class="text-danger fw-bold text-uppercase small tracking-wider">Voices</span>
                <h2 class="section-heading fw-bold mt-1 mb-4">What Our Community Says</h2>

                <div class="row g-3 testimonial-carousel">
                    @forelse($testimonials as $t)
                        <div class="col-md-6 col-lg-4"><article class="home-testimonial-card h-100"><div class="home-quote-mark">“</div><blockquote class="mb-0"><p>“{{ $t->quote }}”</p><footer><span class="home-avatar" aria-label="{{ $t->name }}">{{ strtoupper(substr($t->name, 0, 1)) }}</span><span><strong>{{ $t->name }}</strong><small>{{ $t->role }}</small></span></footer></blockquote></article></div>
                    @empty
                        <div class="col-12"><p class="home-empty-state mb-0">Community stories will appear here as they are approved.</p></div>
                    @endforelse
                </div>
            </div>
            <div class="col-lg-5">
                <h6 class="text-uppercase text-muted small">Public information</h6>
                <p class="home-empty-state mt-3 mb-0">Official announcements, academic updates, and campus information are published through the Noticeboard.</p>
                <a href="{{ route('public.notices') }}" class="btn btn-sm btn-outline-dark mt-3">Visit the Noticeboard <i class="bi bi-arrow-up-right ms-1"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- ===== GALLERY & FAQ ===== -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-end gap-3"><div><span class="text-danger fw-bold text-uppercase small tracking-wider">Explore the campus</span><h5 class="fw-bold mt-1 mb-0">Campus Gallery</h5></div><span class="small text-muted"><i class="bi bi-camera me-1"></i> 6 views</span></div>
                <div class="home-gallery-grid mt-3">
                    <button type="button" class="home-gallery-item home-gallery-item-wide" data-gallery-src="/images/home/campus.jpg" data-gallery-alt="Students walking through a university campus"><img src="/images/home/campus.jpg" alt="Students walking through a university campus" loading="lazy"><span>Campus life <i class="bi bi-arrows-fullscreen"></i></span></button>
                    <button type="button" class="home-gallery-item" data-gallery-src="/images/home/classroom.jpg" data-gallery-alt="Students learning in a bright classroom"><img src="/images/home/classroom.jpg" alt="Students learning in a bright classroom" loading="lazy"><span>Classrooms <i class="bi bi-arrows-fullscreen"></i></span></button>
                    <button type="button" class="home-gallery-item" data-gallery-src="/images/home/library.jpg" data-gallery-alt="University library interior with bookshelves"><img src="/images/home/library.jpg" alt="University library interior with bookshelves" loading="lazy"><span>Library <i class="bi bi-arrows-fullscreen"></i></span></button>
                    <button type="button" class="home-gallery-item" data-gallery-src="/images/home/lab.jpg" data-gallery-alt="Students working in a science laboratory"><img src="/images/home/lab.jpg" alt="Students working in a science laboratory" loading="lazy"><span>Laboratories <i class="bi bi-arrows-fullscreen"></i></span></button>
                    <button type="button" class="home-gallery-item" data-gallery-src="/images/home/tech.jpg" data-gallery-alt="Technology researcher working at a computer"><img src="/images/home/tech.jpg" alt="Technology researcher working at a computer" loading="lazy"><span>Technology <i class="bi bi-arrows-fullscreen"></i></span></button>
                    <button type="button" class="home-gallery-item" data-gallery-src="/images/home/campus.jpg" data-gallery-alt="Graduating students celebrating together"><img src="/images/home/campus.jpg" alt="Graduating students celebrating together" loading="lazy"><span>Graduation <i class="bi bi-arrows-fullscreen"></i></span></button>
                </div>
            </div>
            <div class="col-lg-4">
                <h5 class="fw-bold">Frequently Asked Questions</h5>
                <div class="accordion mt-3" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                How do I apply?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="faqOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body small text-muted">Visit the Admissions page, fill the online application form, and upload the required documents. Our admissions team will contact you with the next steps.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Which grades can apply?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="faqTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body small text-muted">Current admissions information and available grade levels are listed on the Admissions page. Contact the university team with questions about eligibility.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<div class="home-lightbox" id="homeLightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Campus gallery image viewer">
    <button type="button" class="home-lightbox-close" data-lightbox-close aria-label="Close image viewer"><i class="bi bi-x-lg"></i></button>
    <img alt="" id="homeLightboxImage">
    <p id="homeLightboxCaption"></p>
</div>
<!-- ===== CTA ADMISSION BANNER ===== -->
<section class="cta-banner py-5 bg-navy text-white text-center position-relative">
    <div class="container py-4">
        <div class="max-w-700 mx-auto">
            <h2 class="display-6 fw-bold mb-3">Begin Your Academic Journey at Tima-Ade University</h2>
            <p class="lead opacity-90 mb-4">
                Applications for the upcoming semester are currently being reviewed. Schedule a campus tour or start your online application today.
            </p>
            <div class="d-flex justify-content-center flex-wrap gap-3">
                <a href="{{ route('public.apply') }}" class="btn btn-crimson btn-lg px-4 shadow">
                    <i class="bi bi-pencil-square me-2"></i> Apply Online Now
                </a>
                <a href="{{ route('public.contact') }}" class="btn btn-outline-light btn-lg px-4">
                    <i class="bi bi-chat-dots me-2"></i> Inquire with Registrar
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
