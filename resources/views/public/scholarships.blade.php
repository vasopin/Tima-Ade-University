@extends('layouts.public')

@section('title', 'Scholarships at Tima-Ade — Financial Aid & Support')
@section('meta_description', 'Explore scholarship opportunities and financial aid programs at Tima-Ade University.')

@section('content')
<section class="scholarships-premium-hero position-relative overflow-hidden text-white">
    <div class="scholarships-premium-hero-overlay"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-end min-vh-75 py-5">
            <div class="col-xl-8 col-lg-10 py-5 scholarships-reveal">
                <p class="scholarships-kicker">FINANCIAL SUPPORT</p>
                <h1 class="scholarships-display">Scholarships &amp; Financial Aid</h1>
                <p class="scholarships-hero-lead">Making quality education accessible through comprehensive financial support and scholarship opportunities.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-6 bg-white">
    <div class="container">
        <div class="row g-5 mb-6">
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">Financial Support at Tima-Ade</h2>
                <p class="lead text-muted mb-4">We are committed to removing financial barriers to education. Tima-Ade University offers a comprehensive range of scholarships, grants, and aid programs to support student success.</p>
            </div>
            <div class="col-lg-6">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="bg-light rounded-3 p-4 border">
                            <div class="h3 fw-bold text-primary mb-2">$10M+</div>
                            <p class="small text-muted mb-0">Aid Distributed</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light rounded-3 p-4 border">
                            <div class="h3 fw-bold text-primary mb-2">75%</div>
                            <p class="small text-muted mb-0">Students Receive Aid</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @foreach ([
                ['Merit Excellence Scholarship', 'Based on academic achievement and test scores', 'Up to 100% tuition', 'Ongoing'],
                ['Regional Impact Scholarship', 'For students from underserved communities', '50-75% tuition', 'Annual'],
                ['Women in STEM Scholarship', 'Supporting women in science and technology fields', 'Full tuition + stipend', 'Annual'],
                ['First Generation Scholarship', 'For students whose parents did not attend university', 'Variable support', 'Annual'],
                ['Athlete Scholarship', 'Supporting student-athletes in NCAA/conference sports', 'Tuition + benefits', 'Annual'],
                ['International Student Scholarship', 'For international students with strong academics', '25-50% tuition', 'Annual'],
            ] as $scholarship)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="h5 fw-bold mb-3">{{ $scholarship[0] }}</h3>
                            <p class="small text-muted mb-3">{{ $scholarship[1] }}</p>
                            <div class="bg-info bg-opacity-10 rounded-2 p-3 mb-3">
                                <p class="small fw-bold text-primary mb-1">Award Amount</p>
                                <p class="small fw-semibold mb-0">{{ $scholarship[2] }}</p>
                            </div>
                            <p class="small text-muted mb-3"><i class="bi bi-calendar me-2"></i>{{ $scholarship[3] }}</p>
                            <a href="{{ route('public.admissions') }}" class="btn btn-sm btn-primary">Learn More</a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-6 bg-light">
    <div class="container">
        <h2 class="h3 fw-bold mb-5 text-center">Types of Financial Aid</h2>
        <div class="row g-4">
            @foreach ([
                ['Scholarships', 'Merit and need-based awards that do not require repayment'],
                ['Grants', 'Need-based funds supporting students from low-income backgrounds'],
                ['Student Loans', 'Flexible borrowing options with favorable repayment terms'],
                ['Work-Study', 'On-campus employment opportunities for students'],
                ['Tuition Waivers', 'Partial or full tuition support based on specific criteria'],
                ['Emergency Funds', 'Support for unexpected financial hardships'],
            ] as $type)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0">
                        <div class="card-body">
                            <i class="bi bi-currency-dollar fs-2 text-primary mb-4"></i>
                            <h3 class="h5 fw-bold mb-2">{{ $type[0] }}</h3>
                            <p class="small text-muted mb-0">{{ $type[1] }}</p>
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
                <h2 class="h3 fw-bold mb-4">Application Process</h2>
                <p class="text-muted mb-4">Applying for scholarships and financial aid at Tima-Ade is straightforward:</p>
                <ol class="list-group list-group-numbered mb-4">
                    <li class="list-group-item border-0 ps-0">
                        <strong>Complete FAFSA:</strong> Submit the Free Application for Federal Student Aid
                    </li>
                    <li class="list-group-item border-0 ps-0">
                        <strong>Institutional Application:</strong> Fill out Tima-Ade&apos;s financial aid form
                    </li>
                    <li class="list-group-item border-0 ps-0">
                        <strong>Submit Documents:</strong> Provide required financial documentation
                    </li>
                    <li class="list-group-item border-0 ps-0">
                        <strong>Review Award Letter:</strong> Receive and evaluate your financial aid package
                    </li>
                    <li class="list-group-item border-0 ps-0">
                        <strong>Accept or Appeal:</strong> Accept awards or request reconsideration
                    </li>
                </ol>
            </div>
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">Scholarship Deadlines</h2>
                <p class="text-muted mb-4">Prioritize your applications by these key dates:</p>
                <div class="bg-light rounded-3 p-4 border">
                    <div class="mb-4">
                        <p class="small text-muted mb-1">Priority Financial Aid Deadline</p>
                        <p class="h6 fw-bold mb-0">March 1, 2024</p>
                    </div>
                    <div class="mb-4">
                        <p class="small text-muted mb-1">General Application Deadline</p>
                        <p class="h6 fw-bold mb-0">June 30, 2024</p>
                    </div>
                    <div class="mb-4">
                        <p class="small text-muted mb-1">Rolling Admission Deadline</p>
                        <p class="h6 fw-bold mb-0">Until seats filled</p>
                    </div>
                    <div>
                        <a href="{{ route('public.contact') }}" class="btn btn-sm btn-primary">Apply Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

<style>
.scholarships-premium-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
}
.scholarships-premium-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.3);
}
.scholarships-kicker {
    color: rgba(255, 255, 255, 0.8);
    font-weight: 600;
    font-size: 0.875rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
}
.scholarships-display {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
}
.scholarships-hero-lead {
    font-size: 1.25rem;
    color: rgba(255, 255, 255, 0.9);
}
</style>
