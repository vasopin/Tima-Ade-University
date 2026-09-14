@extends('layouts.public')

@section('title', 'Research at Tima-Ade — Innovation & Inquiry')
@section('meta_description', 'Discover Tima-Ade University research centers, publications, and opportunities for academic innovation.')

@section('content')
<section class="research-premium-hero position-relative overflow-hidden text-white">
    <div class="research-premium-hero-overlay"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-end min-vh-75 py-5">
            <div class="col-xl-8 col-lg-10 py-5 research-reveal">
                <p class="research-kicker">RESEARCH &amp; INNOVATION</p>
                <h1 class="research-display">Advancing Knowledge Through Research</h1>
                <p class="research-hero-lead">Tima-Ade University is committed to advancing knowledge and innovation through rigorous research that addresses real-world challenges.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-6 bg-white">
    <div class="container">
        <div class="row g-5 align-items-center mb-6">
            <div class="col-lg-6">
                <h2 class="h2 fw-bold mb-4">Research Excellence</h2>
                <p class="lead text-muted mb-4">Our research initiatives span multiple disciplines, bringing together faculty, students, and partners to tackle complex problems and generate new knowledge.</p>
                <p class="text-muted mb-4">From applied sciences to social innovation, our research community is dedicated to producing work that is academically rigorous, practically relevant, and transformative for our region and beyond.</p>
            </div>
            <div class="col-lg-6">
                <div class="bg-light rounded-3 p-5 border">
                    <div class="row text-center g-4">
                        <div class="col-6">
                            <div class="h3 fw-bold text-primary mb-2">50+</div>
                            <p class="small text-muted mb-0">Active Research Projects</p>
                        </div>
                        <div class="col-6">
                            <div class="h3 fw-bold text-primary mb-2">12</div>
                            <p class="small text-muted mb-0">Research Centers</p>
                        </div>
                        <div class="col-6">
                            <div class="h3 fw-bold text-primary mb-2">200+</div>
                            <p class="small text-muted mb-0">Annual Publications</p>
                        </div>
                        <div class="col-6">
                            <div class="h3 fw-bold text-primary mb-2">$5M+</div>
                            <p class="small text-muted mb-0">Research Funding</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-6 bg-light">
    <div class="container">
        <h2 class="h3 fw-bold mb-5 text-center">Research Centers &amp; Institutes</h2>
        <div class="row g-4">
            @foreach ([
                ['Center for Applied Technology', 'Developing practical technology solutions for regional challenges', 'Technology', '12 Faculty'],
                ['Institute for Development Research', 'Studying sustainable development and policy solutions', 'Social Sciences', '8 Faculty'],
                ['Laboratory for Environmental Studies', 'Researching climate, ecology, and environmental sustainability', 'Environmental', '10 Faculty'],
                ['Education Innovation Institute', 'Advancing pedagogy and educational practice research', 'Education', '7 Faculty'],
                ['Health Systems Research Center', 'Improving healthcare delivery and public health outcomes', 'Health', '9 Faculty'],
                ['Cultural Heritage Institute', 'Preserving and studying Somaliland cultural heritage', 'Humanities', '6 Faculty'],
            ] as $center)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="badge bg-info text-dark mb-3">{{ $center[2] }}</div>
                            <h3 class="h5 fw-bold mb-3">{{ $center[0] }}</h3>
                            <p class="small text-muted mb-3">{{ $center[1] }}</p>
                            <p class="small text-primary fw-semibold mb-0"><i class="bi bi-people me-2"></i>{{ $center[3] }}</p>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-6 bg-white">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">Funding &amp; Support</h2>
                <p class="text-muted mb-4">Tima-Ade University provides multiple pathways for research funding and support:</p>
                <ul class="list-unstyled mb-4">
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>Internal Grants:</strong> Competitive funding for faculty-led research initiatives</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>External Partnerships:</strong> Collaboration with international research institutions</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>Graduate Research Support:</strong> Thesis and dissertation grants</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>Conference Travel:</strong> Support for presenting research at academic conferences</span>
                    </li>
                </ul>
                <a href="{{ route('public.contact') }}" class="btn btn-primary">Learn About Research Opportunities</a>
            </div>
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">Publication &amp; Dissemination</h2>
                <p class="text-muted mb-4">We support the publication and sharing of research findings through:</p>
                <ul class="list-unstyled mb-4">
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>Peer-Reviewed Journals:</strong> Publication in regional and international journals</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>University Research Repository:</strong> Open-access institutional archive</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>Research Seminars:</strong> Regular showcases of faculty and student research</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>Policy Briefs:</strong> Applied research translated for policymakers</span>
                    </li>
                </ul>
                <a href="{{ route('public.contact') }}" class="btn btn-primary">Explore Publications</a>
            </div>
        </div>
    </div>
</section>
@endsection

<style>
.research-premium-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
}
.research-premium-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.3);
}
.research-kicker {
    color: rgba(255, 255, 255, 0.8);
    font-weight: 600;
    font-size: 0.875rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
}
.research-display {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
}
.research-hero-lead {
    font-size: 1.25rem;
    color: rgba(255, 255, 255, 0.9);
}
</style>
