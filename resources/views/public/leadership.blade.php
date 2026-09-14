@extends('layouts.public')

@section('title', 'University Leadership — Tima-Ade')
@section('meta_description', 'Meet the visionary leaders guiding Tima-Ade University toward excellence and innovation.')

@section('content')
<section class="leadership-premium-hero position-relative overflow-hidden text-white">
    <div class="leadership-premium-hero-overlay"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-end min-vh-75 py-5">
            <div class="col-xl-8 col-lg-10 py-5 leadership-reveal">
                <p class="leadership-kicker">LEADERSHIP</p>
                <h1 class="leadership-display">University Leadership</h1>
                <p class="leadership-hero-lead">Visionary leaders dedicated to advancing academic excellence and institutional growth.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-5">
                <div class="bg-light rounded-3 p-5 border">
                    <div class="text-6xl font-bold text-primary-400 mb-4">HM</div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="badge bg-primary mb-3">
                    <i class="bi bi-award me-2"></i>President &amp; Vice Chancellor
                </div>
                <h2 class="h2 fw-bold mb-4">Dr. Hassan Mohamed</h2>
                <p class="lead text-muted mb-4">Welcome to Tima-Ade University, where we nurture tomorrow&apos;s leaders and innovators. Our commitment to academic excellence, research advancement, and student success drives everything we do.</p>
                <p class="lead text-muted mb-4">As we continue to grow and evolve, we remain dedicated to our core mission: transforming lives through quality education and preparing our graduates to make meaningful contributions to society and the world.</p>
                <a href="{{ route('public.contact') }}" class="btn btn-primary">
                    Contact President <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-6 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h2 fw-bold mb-3">Executive Leadership Team</h2>
            <p class="lead text-muted">Distinguished professionals committed to institutional excellence</p>
        </div>

        <div class="row g-4">
            @foreach ([
                ['Dr. Hassan Mohamed', 'President &amp; Vice Chancellor', 'Leading vision for academic excellence with over 30 years in higher education.', 'HM'],
                ['Prof. Amina Ahmed', 'Provost &amp; Academic Affairs', 'Overseeing curriculum development and academic standards across all programs.', 'AA'],
                ['Dr. Mohamed Ibrahim', 'Vice Chancellor, Research &amp; Innovation', 'Driving research initiatives and fostering innovation across the university.', 'MI'],
                ['Ms. Fatima Hassan', 'Vice Chancellor, Student Affairs', 'Supporting student success and creating a vibrant campus community.', 'FH'],
                ['Dr. Abdi Mohamed', 'Vice Chancellor, Administration', 'Managing operational excellence and institutional resources.', 'AM'],
                ['Prof. Zainab Hassan', 'Dean, School of Technology', 'Leading innovation in technology education and digital transformation.', 'ZH'],
            ] as $leader)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="bg-gradient rounded-2 p-4 mb-4 text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="text-white fs-3 fw-bold">{{ $leader[3] }}</div>
                            </div>
                            <h3 class="h5 fw-bold mb-2">{{ $leader[0] }}</h3>
                            <p class="small text-primary fw-semibold mb-3">{{ $leader[1] }}</p>
                            <p class="small text-muted mb-0">{{ $leader[2] }}</p>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-6 bg-white">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">Our Governance Structure</h2>
                <p class="text-muted mb-3">Tima-Ade University operates through a collaborative governance structure designed to support academic excellence, innovation, and institutional integrity.</p>
                <ul class="list-unstyled mb-4">
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>Board of Trustees:</strong> Provides institutional oversight and strategic direction</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>University Senate:</strong> Guides academic policy and curriculum decisions</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>Executive Cabinet:</strong> Coordinates strategic initiatives across divisions</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>Deans Council:</strong> Aligns academic schools and administrative departments</span>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6">
                <div class="bg-light rounded-3 p-5 border">
                    <h3 class="h5 fw-bold mb-4">Quick Contact Directory</h3>
                    <div class="mb-4">
                        <p class="small text-muted mb-1">Office of the President</p>
                        <p class="fw-semibold mb-0">president@tima-ade.edu</p>
                    </div>
                    <div class="mb-4">
                        <p class="small text-muted mb-1">Provost Office</p>
                        <p class="fw-semibold mb-0">provost@tima-ade.edu</p>
                    </div>
                    <div class="mb-4">
                        <p class="small text-muted mb-1">Administration Office</p>
                        <p class="fw-semibold mb-0">admin@tima-ade.edu</p>
                    </div>
                    <div>
                        <p class="small text-muted mb-1">General Inquiries</p>
                        <p class="fw-semibold mb-0">inquiries@tima-ade.edu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

<style>
.leadership-premium-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
}
.leadership-premium-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.3);
}
.leadership-kicker {
    color: rgba(255, 255, 255, 0.8);
    font-weight: 600;
    font-size: 0.875rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
}
.leadership-display {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
}
.leadership-hero-lead {
    font-size: 1.25rem;
    color: rgba(255, 255, 255, 0.9);
}
</style>
