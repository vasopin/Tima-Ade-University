@extends('layouts.public')

@section('title', 'News — Tima-Ade University')
@section('meta_description', 'Read the latest news, updates, and announcements from Tima-Ade University.')

@section('content')
<section class="news-premium-hero position-relative overflow-hidden text-white">
    <div class="news-premium-hero-overlay"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-end min-vh-75 py-5">
            <div class="col-xl-8 col-lg-10 py-5 news-reveal">
                <p class="news-kicker">NEWS &amp; UPDATES</p>
                <h1 class="news-display">University News</h1>
                <p class="news-hero-lead">Stay informed with the latest announcements, achievements, and community updates from Tima-Ade University.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-6 bg-light">
    <div class="container">
        <div class="row g-5 mb-6">
            <div class="col-lg-8">
                <h2 class="h3 fw-bold mb-4">Latest News &amp; Announcements</h2>
                @forelse($notices as $notice)
                    <article class="card mb-4 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h3 class="h5 fw-bold mb-0">{{ $notice->title }}</h3>
                                <span class="badge bg-info">{{ $notice->category ?? 'Announcement' }}</span>
                            </div>
                            <p class="small text-muted mb-3">
                                <i class="bi bi-calendar-event me-2"></i>{{ $notice->published_date->format('F d, Y') }}
                            </p>
                            <p class="text-muted mb-3">{{ \Illuminate\Support\Str::limit($notice->description, 200) }}</p>
                            <a href="{{ route('public.notices.single', $notice) }}" class="btn btn-sm btn-primary">Read More</a>
                        </div>
                    </article>
                @empty
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle me-2"></i>No news items available at this time. Please check back soon.
                    </div>
                @endforelse
            </div>
            <div class="col-lg-4">
                <div class="bg-white rounded-3 border p-4 mb-4 sticky-top" style="top: 2rem;">
                    <h3 class="h5 fw-bold mb-4">News Categories</h3>
                    <ul class="list-unstyled">
                        @foreach ([
                            ['Academics', 'bi-book-half'],
                            ['Student Life', 'bi-people-fill'],
                            ['Research', 'bi-microscope'],
                            ['Announcements', 'bi-megaphone'],
                            ['Events', 'bi-calendar'],
                            ['Achievements', 'bi-award'],
                        ] as $cat)
                            <li class="mb-3">
                                <a href="{{ route('public.notices') }}" class="text-decoration-none text-primary">
                                    <i class="bi {{ $cat[1] }} me-2"></i>{{ $cat[0] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-light rounded-3 border p-4">
                    <h3 class="h5 fw-bold mb-3">Subscribe to Updates</h3>
                    <p class="small text-muted mb-3">Get the latest news delivered to your inbox.</p>
                    <form action="{{ route('public.contact.send') }}" method="POST" class="needs-validation">
                        @csrf
                        <input type="hidden" name="subject" value="Newsletter Subscription">
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Your email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-sm">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-6 bg-white">
    <div class="container">
        <h2 class="h3 fw-bold mb-5 text-center">Explore by Topic</h2>
        <div class="row g-4">
            @foreach ([
                ['bi-book', 'Academic Updates', 'Faculty achievements, program launches, and academic excellence'],
                ['bi-people-fill', 'Student Stories', 'Student achievements, club activities, and campus life'],
                ['bi-microscope', 'Research Breakthroughs', 'Faculty research findings and innovation initiatives'],
                ['bi-megaphone', 'Important Announcements', 'Institutional news and administrative updates'],
                ['bi-calendar', 'Event Announcements', 'Upcoming events and community gatherings'],
                ['bi-award', 'Recognition &amp; Awards', 'Student and faculty honors and achievements'],
            ] as $topic)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0">
                        <div class="card-body">
                            <i class="bi {{ $topic[0] }} fs-2 text-primary mb-3"></i>
                            <h3 class="h5 fw-bold mb-2">{{ $topic[1] }}</h3>
                            <p class="small text-muted mb-0">{{ $topic[2] }}</p>
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
                <h2 class="h3 fw-bold mb-4">Media &amp; Press</h2>
                <p class="text-muted mb-4">For media inquiries, press releases, and official university communications:</p>
                <div class="bg-white rounded-3 p-4 border">
                    <div class="mb-4">
                        <p class="small text-muted mb-1">Public Relations Office</p>
                        <p class="fw-semibold mb-0">press@tima-ade.edu</p>
                    </div>
                    <div class="mb-4">
                        <p class="small text-muted mb-1">Media Contact</p>
                        <p class="fw-semibold mb-0">+1 (XXX) XXX-XXXX</p>
                    </div>
                    <div>
                        <p class="small text-muted mb-1">Office Hours</p>
                        <p class="small fw-semibold mb-0">Monday–Friday, 9 AM–5 PM</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">Connect with Us</h2>
                <p class="text-muted mb-4">Follow Tima-Ade University on social media for real-time updates:</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-facebook me-2"></i>Facebook
                    </a>
                    <a href="#" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-twitter me-2"></i>Twitter
                    </a>
                    <a href="#" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-instagram me-2"></i>Instagram
                    </a>
                    <a href="#" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-linkedin me-2"></i>LinkedIn
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

<style>
.news-premium-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
}
.news-premium-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.3);
}
.news-kicker {
    color: rgba(255, 255, 255, 0.8);
    font-weight: 600;
    font-size: 0.875rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
}
.news-display {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
}
.news-hero-lead {
    font-size: 1.25rem;
    color: rgba(255, 255, 255, 0.9);
}
</style>
