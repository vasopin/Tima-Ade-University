@extends('layouts.public')

@section('title', 'Library at Tima-Ade — Research Resources & Services')
@section('meta_description', 'Explore Tima-Ade University Library resources, collections, databases, and research support services.')

@section('content')
<section class="library-premium-hero position-relative overflow-hidden text-white">
    <div class="library-premium-hero-overlay"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-end min-vh-75 py-5">
            <div class="col-xl-8 col-lg-10 py-5 library-reveal">
                <p class="library-kicker">LEARNING &amp; RESEARCH</p>
                <h1 class="library-display">Tima-Ade University Library</h1>
                <p class="library-hero-lead">Your gateway to comprehensive collections, research databases, and expert support for academic and personal growth.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-6 bg-white">
    <div class="container">
        <div class="row g-5 mb-6">
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">Library Services &amp; Resources</h2>
                <p class="lead text-muted mb-4">Tima-Ade University Library supports student learning, faculty research, and institutional scholarship through extensive collections and innovative services.</p>
            </div>
            <div class="col-lg-6">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="bg-light rounded-3 p-4 border">
                            <div class="h3 fw-bold text-primary mb-2">200K+</div>
                            <p class="small text-muted mb-0">Books &amp; Items</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light rounded-3 p-4 border">
                            <div class="h3 fw-bold text-primary mb-2">80+</div>
                            <p class="small text-muted mb-0">Databases</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @foreach ([
                ['Physical Collections', 'Books, journals, reference materials, and multimedia resources', 'Main Library'],
                ['Digital Collections', 'E-books, e-journals, and online databases accessible 24/7', 'Online Access'],
                ['Research Databases', 'JSTOR, ProQuest, Academic Search Complete, and specialized resources', 'Subscription Access'],
                ['Interlibrary Loan', 'Access materials from partner institutions worldwide', 'Request Service'],
                ['Rare Collections', 'Special collections and archival materials of historical significance', 'By Appointment'],
                ['Study Spaces', 'Individual and group study areas, computer labs, and quiet zones', 'Open to Community'],
            ] as $service)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="h5 fw-bold mb-3">{{ $service[0] }}</h3>
                            <p class="small text-muted mb-3">{{ $service[1] }}</p>
                            <p class="small text-primary fw-semibold mb-3">
                                <i class="bi bi-geo-alt me-1"></i>{{ $service[2] }}
                            </p>
                            <a href="{{ route('public.contact') }}" class="btn btn-sm btn-primary">Learn More</a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-6 bg-light">
    <div class="container">
        <h2 class="h3 fw-bold mb-5 text-center">Research Support Services</h2>
        <div class="row g-4">
            @foreach ([
                ['Research Consultation', 'Expert librarians help develop research strategies and find resources'],
                ['Database Training', 'Learn to navigate and maximize research databases and tools'],
                ['Citation Support', 'Guidance in APA, MLA, Chicago, and other citation styles'],
                ['Literature Reviews', 'Systematic searching and organization of research literature'],
                ['Thesis Support', 'Specialized assistance for thesis and dissertation research'],
                ['Open Access Resources', 'Access to free, publicly available scholarly materials'],
            ] as $support)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0">
                        <div class="card-body">
                            <i class="bi bi-book fs-2 text-primary mb-3"></i>
                            <h3 class="h5 fw-bold mb-2">{{ $support[0] }}</h3>
                            <p class="small text-muted mb-0">{{ $support[1] }}</p>
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
                <h2 class="h3 fw-bold mb-4">Access Information</h2>
                <p class="text-muted mb-4">Library access and services:</p>
                <div class="bg-light rounded-3 p-4 border mb-4">
                    <div class="mb-4">
                        <p class="small text-muted mb-1">Hours of Operation</p>
                        <p class="h6 fw-bold mb-0">Monday – Friday: 8:00 AM – 8:00 PM</p>
                        <p class="small text-muted mb-0">Saturday: 10:00 AM – 4:00 PM</p>
                        <p class="small text-muted mb-0">Sunday: Closed</p>
                    </div>
                    <div>
                        <p class="small text-muted mb-1">Location</p>
                        <p class="h6 fw-bold mb-0">Central Library Building</p>
                        <p class="small text-muted mb-0">Tima-Ade University Campus</p>
                    </div>
                </div>
                <a href="{{ route('public.contact') }}" class="btn btn-primary">Contact the Library</a>
            </div>
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">Online Catalog &amp; Resources</h2>
                <p class="text-muted mb-4">Search our collections and access digital resources:</p>
                <ul class="list-unstyled mb-4">
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>Library Catalog:</strong> Search books and materials in our collection</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>Database Access:</strong> Connect to research databases from campus or remotely</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>Digital Repository:</strong> Browse institutional scholarship and research</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>Research Guides:</strong> Subject-specific guides to collections and tools</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span><strong>Ask a Librarian:</strong> Chat, email, or phone support during library hours</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection

<style>
.library-premium-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
}
.library-premium-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.3);
}
.library-kicker {
    color: rgba(255, 255, 255, 0.8);
    font-weight: 600;
    font-size: 0.875rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
}
.library-display {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
}
.library-hero-lead {
    font-size: 1.25rem;
    color: rgba(255, 255, 255, 0.9);
}
</style>
