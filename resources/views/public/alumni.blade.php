@extends('layouts.public')

@section('title', 'Alumni at Tima-Ade — Our Growing Community')
@section('meta_description', 'Join the Tima-Ade University alumni network and stay connected with graduates worldwide.')

@section('content')
<section class="alumni-premium-hero position-relative overflow-hidden text-white">
    <div class="alumni-premium-hero-overlay"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-end min-vh-75 py-5">
            <div class="col-xl-8 col-lg-10 py-5 alumni-reveal">
                <p class="alumni-kicker">ALUMNI COMMUNITY</p>
                <h1 class="alumni-display">Tima-Ade Alumni</h1>
                <p class="alumni-hero-lead">Join a vibrant global community of graduates making an impact in their professions and communities.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-6 bg-white">
    <div class="container">
        <div class="row g-5 mb-6">
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">Tima-Ade Alumni Network</h2>
                <p class="lead text-muted mb-4">Our alumni are leaders, innovators, and changemakers across the globe. The Tima-Ade Alumni Association connects graduates, fosters lifelong relationships, and amplifies our collective impact.</p>
            </div>
            <div class="col-lg-6">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="bg-light rounded-3 p-4 border">
                            <div class="h3 fw-bold text-primary mb-2">10,000+</div>
                            <p class="small text-muted mb-0">Registered Alumni</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light rounded-3 p-4 border">
                            <div class="h3 fw-bold text-primary mb-2">85+</div>
                            <p class="small text-muted mb-0">Countries</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="h4 fw-bold mb-4">Featured Alumni Stories</h3>
        <div class="row g-4">
            @foreach ([
                ['Dr. Abdi Hassan', 'Founder, TechInovate Solutions', 'Leading digital transformation in East Africa', 'Technology'],
                ['Amina Mohamed', 'Chief Education Officer', 'Advancing educational policy in the region', 'Education'],
                ['Dr. Mohammed Ahmed', 'Research Director, Health Institute', 'Pioneering public health research', 'Healthcare'],
                ['Zainab Ibrahim', 'Environmental Policy Advocate', 'Leading sustainability initiatives regionally', 'Environment'],
                ['Omar Yusuf', 'Entrepreneur &amp; Social Impact Leader', 'Building social enterprises for community development', 'Social Impact'],
                ['Dr. Fatima Saleh', 'University Professor', 'Teaching and mentoring the next generation', 'Academia'],
            ] as $story)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="badge bg-success mb-3">{{ $story[3] }}</div>
                            <h3 class="h5 fw-bold mb-2">{{ $story[0] }}</h3>
                            <p class="small text-primary fw-semibold mb-2">{{ $story[1] }}</p>
                            <p class="small text-muted mb-3">{{ $story[2] }}</p>
                            <a href="{{ route('public.contact') }}" class="btn btn-sm btn-outline-primary">Learn Their Story</a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-6 bg-light">
    <div class="container">
        <h2 class="h3 fw-bold mb-5 text-center">Alumni Benefits &amp; Engagement</h2>
        <div class="row g-4">
            @foreach ([
                ['Networking Events', 'Regional and international alumni meetups and professional gatherings'],
                ['Career Services', 'Job board and career development resources for alumni'],
                ['Alumni Directory', 'Connect with classmates and build your professional network'],
                ['Continuing Education', 'Discounted access to professional development and certification programs'],
                ['Publications', 'Alumni magazine, updates, and success stories'],
                ['Volunteer Opportunities', 'Give back through mentoring and university service'],
            ] as $benefit)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0">
                        <div class="card-body">
                            <i class="bi bi-star-fill fs-2 text-warning mb-3"></i>
                            <h3 class="h5 fw-bold mb-2">{{ $benefit[0] }}</h3>
                            <p class="small text-muted mb-0">{{ $benefit[1] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-6 bg-white">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">Join the Alumni Network</h2>
                <p class="text-muted mb-4">Stay connected with Tima-Ade University and your classmates:</p>
                <ol class="list-group list-group-numbered mb-4">
                    <li class="list-group-item border-0 ps-0">
                        <strong>Update Your Profile:</strong> Register with the alumni association
                    </li>
                    <li class="list-group-item border-0 ps-0">
                        <strong>Connect with Classmates:</strong> Search the alumni directory
                    </li>
                    <li class="list-group-item border-0 ps-0">
                        <strong>Attend Events:</strong> Join alumni reunions and networking gatherings
                    </li>
                    <li class="list-group-item border-0 ps-0">
                        <strong>Get Involved:</strong> Mentor students or volunteer with the university
                    </li>
                </ol>
                <a href="{{ route('public.contact') }}" class="btn btn-primary">Register as Alumni</a>
            </div>
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">Give Back to Tima-Ade</h2>
                <p class="text-muted mb-4">Your support strengthens Tima-Ade&apos;s mission and student success:</p>
                <ul class="list-unstyled mb-4">
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-heart-fill text-danger flex-shrink-0 mt-1"></i>
                        <span><strong>Scholarships:</strong> Support talented students through financial assistance</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-heart-fill text-danger flex-shrink-0 mt-1"></i>
                        <span><strong>Programs:</strong> Fund academic programs and student initiatives</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-heart-fill text-danger flex-shrink-0 mt-1"></i>
                        <span><strong>Facilities:</strong> Help improve campus infrastructure</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-heart-fill text-danger flex-shrink-0 mt-1"></i>
                        <span><strong>Research:</strong> Enable faculty and student research initiatives</span>
                    </li>
                </ul>
                <a href="{{ route('public.contact') }}" class="btn btn-primary">Make a Donation</a>
            </div>
        </div>
    </div>
</section>
@endsection

<style>
.alumni-premium-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
}
.alumni-premium-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.3);
}
.alumni-kicker {
    color: rgba(255, 255, 255, 0.8);
    font-weight: 600;
    font-size: 0.875rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
}
.alumni-display {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
}
.alumni-hero-lead {
    font-size: 1.25rem;
    color: rgba(255, 255, 255, 0.9);
}
</style>
