@extends('layouts.public')

@section('title', 'Careers at Tima-Ade — Join Our Team')
@section('meta_description', 'Explore career opportunities and join Tima-Ade University as faculty, staff, or administrator.')

@section('content')
<section class="careers-premium-hero position-relative overflow-hidden text-white">
    <div class="careers-premium-hero-overlay"></div>
    <div class="container position-relative py-5">
        <div class="row align-items-end min-vh-75 py-5">
            <div class="col-xl-8 col-lg-10 py-5 careers-reveal">
                <p class="careers-kicker">JOIN OUR TEAM</p>
                <h1 class="careers-display">Careers at Tima-Ade University</h1>
                <p class="careers-hero-lead">Become part of a dynamic institution dedicated to academic excellence, innovation, and meaningful impact.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-6 bg-white">
    <div class="container">
        <div class="row g-5 mb-6">
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">Why Join Tima-Ade?</h2>
                <p class="text-muted mb-4">Tima-Ade University offers a vibrant workplace where passionate educators and professionals make a real difference in students&apos; lives and contribute to knowledge advancement.</p>
            </div>
            <div class="col-lg-6">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="bg-light rounded-3 p-4 border">
                            <div class="h3 fw-bold text-primary mb-2">200+</div>
                            <p class="small text-muted mb-0">Team Members</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light rounded-3 p-4 border">
                            <div class="h3 fw-bold text-primary mb-2">15+</div>
                            <p class="small text-muted mb-0">Departments</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @foreach ([
                ['Faculty Positions', 'Teaching, research, and mentoring roles across disciplines', 'Apply via Academic Portal'],
                ['Administrative Roles', 'Management, operations, and support functions', 'Human Resources Portal'],
                ['Technical Staff', 'IT, facilities, and infrastructure specialists', 'Operations Department'],
                ['Student Services', 'Advising, counseling, and student support roles', 'Student Affairs Office'],
                ['Finance & Administration', 'Accounting, human resources, and business roles', 'Finance Department'],
                ['Research & Innovation', 'Research centers, labs, and innovation initiatives', 'Research Office'],
            ] as $career)
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="h5 fw-bold mb-3">{{ $career[0] }}</h3>
                            <p class="small text-muted mb-4">{{ $career[1] }}</p>
                            <a href="{{ route('public.contact') }}" class="btn btn-sm btn-primary">{{ $career[2] }}</a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-6 bg-light">
    <div class="container">
        <h2 class="h3 fw-bold mb-5 text-center">Benefits &amp; Support</h2>
        <div class="row g-4">
            @foreach ([
                ['Competitive Compensation', 'Competitive salaries aligned with regional standards and experience'],
                ['Health &amp; Wellness', 'Comprehensive health insurance and wellness programs'],
                ['Professional Development', 'Tuition support and professional development opportunities'],
                ['Collaborative Environment', 'Work with colleagues dedicated to educational excellence'],
                ['Modern Facilities', 'State-of-the-art campus facilities and technology'],
                ['Work-Life Balance', 'Flexible arrangements supporting personal and professional growth'],
            ] as $benefit)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 text-center">
                        <div class="card-body">
                            <i class="bi bi-check-circle-fill fs-2 text-success mb-3"></i>
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
                <h2 class="h3 fw-bold mb-4">Application Requirements</h2>
                <p class="text-muted mb-4">Most positions require:</p>
                <ul class="list-unstyled mb-4">
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span>Bachelor&apos;s degree (advanced degree for faculty positions)</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span>Relevant professional experience</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span>Strong communication skills</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span>Commitment to institutional mission</span>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
                        <span>References and background check</span>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">How to Apply</h2>
                <p class="text-muted mb-4">Apply online through our career portal:</p>
                <ol class="list-group list-group-numbered mb-4">
                    <li class="list-group-item border-0 ps-0">
                        Visit the careers portal and browse open positions
                    </li>
                    <li class="list-group-item border-0 ps-0">
                        Create an account and upload your resume
                    </li>
                    <li class="list-group-item border-0 ps-0">
                        Complete the application form
                    </li>
                    <li class="list-group-item border-0 ps-0">
                        Submit cover letter and references
                    </li>
                    <li class="list-group-item border-0 ps-0">
                        Monitor your email for interview invitations
                    </li>
                </ol>
                <a href="{{ route('public.contact') }}" class="btn btn-primary">View Open Positions</a>
            </div>
        </div>
    </div>
</section>
@endsection

<style>
.careers-premium-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
}
.careers-premium-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.3);
}
.careers-kicker {
    color: rgba(255, 255, 255, 0.8);
    font-weight: 600;
    font-size: 0.875rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
}
.careers-display {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
}
.careers-hero-lead {
    font-size: 1.25rem;
    color: rgba(255, 255, 255, 0.9);
}
</style>
