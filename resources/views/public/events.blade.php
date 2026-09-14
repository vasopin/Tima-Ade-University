@extends('layouts.public')

@section('title', 'Events at Tima-Ade — Campus Life & Community')
@section('meta_description', 'Discover upcoming events at Tima-Ade University, from academic lectures to cultural celebrations.')

@section('content')
<section class="events-premium-hero position-relative overflow-hidden text-white">
    <div class="events-premium-hero-overlay"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-end min-vh-75 py-5">
            <div class="col-xl-8 col-lg-10 py-5 events-reveal">
                <p class="events-kicker">EVENTS &amp; ACTIVITIES</p>
                <h1 class="events-display">Campus Events &amp; Community</h1>
                <p class="events-hero-lead">Join us for lectures, conferences, cultural celebrations, and community events that define campus life at Tima-Ade.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-6 bg-light">
    <div class="container">
        <div class="row g-5 mb-6">
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">Upcoming Events</h2>
                <p class="text-muted mb-5">Tima-Ade University hosts a dynamic calendar of events designed to enrich student life, foster community connection, and advance knowledge.</p>
            </div>
            <div class="col-lg-6">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="bg-white rounded-3 p-4 border">
                            <div class="h3 fw-bold text-primary mb-2">60+</div>
                            <p class="small text-muted mb-0">Events Per Year</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white rounded-3 p-4 border">
                            <div class="h3 fw-bold text-primary mb-2">2,500+</div>
                            <p class="small text-muted mb-0">Attendees</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @foreach ([
                ['Academic Excellence Symposium', 'September 15-17, 2024', 'Academic', 'A multi-day conference celebrating research and academic achievement', 'Multiple Venues'],
                ['Welcome Week 2024', 'September 1-7, 2024', 'Student Life', 'Orientation activities, cultural showcases, and community building for new students', 'Campus Wide'],
                ['Technology Innovation Expo', 'October 8, 2024', 'Innovation', 'Showcase of student and faculty technology projects and innovations', 'Innovation Center'],
                ['Cultural Celebration Festival', 'October 22, 2024', 'Cultural', 'Music, dance, food, and traditions from our diverse campus community', 'Main Quad'],
                ['Research & Discovery Day', 'November 5, 2024', 'Academic', 'Students and faculty present research findings to the public', 'Convention Center'],
                ['Annual Gala & Awards Ceremony', 'December 10, 2024', 'Celebration', 'Recognition of student achievement and community contribution', 'Grand Ballroom'],
            ] as $event)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="badge bg-info text-dark mb-3">{{ $event[2] }}</div>
                            <h3 class="h5 fw-bold mb-2">{{ $event[0] }}</h3>
                            <p class="small text-muted mb-3">{{ $event[1] }}</p>
                            <p class="small text-muted mb-3">{{ $event[3] }}</p>
                            <p class="small text-primary fw-semibold mb-3">
                                <i class="bi bi-geo-alt me-1"></i>{{ $event[4] }}
                            </p>
                            <a href="{{ route('public.contact') }}" class="btn btn-sm btn-primary">Learn More</a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-6 bg-white">
    <div class="container">
        <h2 class="h3 fw-bold mb-5 text-center">Event Categories</h2>
        <div class="row g-4">
            @foreach ([
                ['bi-book', 'Academic Lectures', 'Faculty seminars, research talks, and scholarly discussions'],
                ['bi-people-fill', 'Social & Cultural', 'Celebrations, performances, and community gatherings'],
                ['bi-mic', 'Conferences & Workshops', 'Professional development and advanced training opportunities'],
                ['bi-basketball', 'Sports & Recreation', 'Athletic competitions and wellness activities'],
                ['bi-globe', 'International Events', 'Global perspectives and cross-cultural exchanges'],
                ['bi-briefcase', 'Career & Professional', 'Networking events and career development workshops'],
            ] as $category)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 text-center">
                        <div class="card-body">
                            <i class="bi {{ $category[0] }} fs-1 text-primary mb-4"></i>
                            <h3 class="h5 fw-bold mb-2">{{ $category[1] }}</h3>
                            <p class="small text-muted mb-0">{{ $category[2] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-6 bg-light">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">Event Registration</h2>
                <p class="text-muted mb-4">Most campus events are open to students, faculty, staff, and the public. Registration details are available on individual event pages.</p>
                <ul class="list-unstyled mb-4">
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span>Online registration through the student portal</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span>Walk-in registration at event entrance</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span>Email registration for specific events</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span>Calendar invitations available</span>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">Host an Event</h2>
                <p class="text-muted mb-4">Student organizations, departments, and external partners can request campus venues and support for events.</p>
                <p class="text-muted mb-4">Contact the Office of Events &amp; Student Life to:</p>
                <ul class="list-unstyled mb-4">
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span>Reserve campus venues</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span>Request technical support</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span>Obtain event planning resources</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span>Coordinate with campus partners</span>
                    </li>
                </ul>
                <a href="{{ route('public.contact') }}" class="btn btn-primary">Host an Event</a>
            </div>
        </div>
    </div>
</section>
@endsection

<style>
.events-premium-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
}
.events-premium-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.3);
}
.events-kicker {
    color: rgba(255, 255, 255, 0.8);
    font-weight: 600;
    font-size: 0.875rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
}
.events-display {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
}
.events-hero-lead {
    font-size: 1.25rem;
    color: rgba(255, 255, 255, 0.9);
}
</style>
