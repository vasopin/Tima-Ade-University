@extends('layouts.public')

@section('title', 'Admissions — Tima-Ade University')
@section('meta_description', 'Begin your Tima-Ade University application journey — explore admissions guidance, submit an inquiry, and connect with the university for current requirements.')

@section('content')
<div class="admissions-page">
    <section class="admissions-hero text-white" aria-labelledby="admissions-title">
        <div class="container py-5">
            <div class="row align-items-end g-5">
                <div class="col-lg-8 admissions-reveal">
                    <span class="admissions-eyebrow"><span class="admissions-eyebrow-dot"></span> Admissions</span>
                    <h1 id="admissions-title">Begin your academic journey.</h1>
                    <p class="lead">Explore the information available for applicants, prepare your questions, and connect with Tima-Ade University for current guidance.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#admissions-resources" class="btn btn-crimson btn-lg admissions-hero-button">Explore admissions <i class="bi bi-arrow-down-right ms-2" aria-hidden="true"></i></a>
                        <a href="{{ route('public.apply') }}" class="btn btn-outline-light btn-lg admissions-hero-button">Apply now <i class="bi bi-arrow-up-right ms-2" aria-hidden="true"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 admissions-reveal">
                    <figure class="admissions-hero-visual mb-0"><img src="{{ asset('images/home/campus.jpg') }}" alt="Tima-Ade University campus environment"></figure>
                </div>
            </div>
            <nav class="mt-5" aria-label="Breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('public.home') }}" class="text-light text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active text-danger" aria-current="page">Admissions</li>
                </ol>
            </nav>
        </div>
    </section>

    <nav class="admissions-section-nav" aria-label="Admissions pages">
        <div class="container"><div class="admissions-section-nav-scroll">
            <a class="is-current" aria-current="page" href="{{ route('public.admissions') }}">Overview</a>
            <a href="{{ route('public.admissions.how-to-apply') }}">How to Apply</a>
            <a href="{{ route('public.admissions.requirements') }}">Requirements</a>
            <a href="{{ route('public.admissions.process') }}">Application Process</a>
            <a href="{{ route('public.admissions.dates') }}">Important Dates</a>
            <a href="{{ route('public.admissions.fees') }}">Fees &amp; Funding</a>
            <a href="{{ route('public.admissions.international') }}">International</a>
            <a href="{{ route('public.admissions.faq') }}">FAQ</a>
        </div></div>
    </nav>

    <section id="admissions-resources" class="admissions-intro py-5 bg-white" aria-labelledby="process-title">
        <div class="container py-4">
            <div class="row g-5 align-items-start">
                <div class="col-lg-6 admissions-reveal">
                    <span class="admissions-section-kicker">A navigation guide</span>
                    <h2 id="process-title" class="admissions-section-title">Find the information that matters to your next decision.</h2>
                    <p class="admissions-section-lead">Use these pages to understand what has been published, identify what still needs confirmation, and contact the university when you need current guidance.</p>
                    <div class="admissions-steps" aria-label="General admissions navigation guide">
                        <article class="admissions-step"><div class="admissions-step-number">01</div><div><h3>Explore your options</h3><p>Review the current academic offering and decide which information you need next.</p></div></article>
                        <article class="admissions-step"><div class="admissions-step-number">02</div><div><h3>Review requirements</h3><p>Check the requirements page and confirm any details that are not yet published.</p></div></article>
                        <article class="admissions-step"><div class="admissions-step-number">03</div><div><h3>Prepare and connect</h3><p>Prepare your questions, then use the official inquiry or contact channels for guidance.</p></div></article>
                    </div>
                </div>
                <div class="col-lg-6 admissions-reveal">
                    <aside class="admissions-documents" aria-labelledby="documents-title">
                        <div class="admissions-documents-top"><span class="admissions-section-kicker">Information status</span></div>
                        <h2 id="documents-title">A reliable place to begin.</h2>
                        <p>Official requirements, dates, fees, and funding details should be confirmed before an application is submitted.</p>
                        <ul>
                            <li><i class="bi bi-arrow-right" aria-hidden="true"></i><a href="{{ route('public.programs') }}">Explore academic programs</a></li>
                            <li><i class="bi bi-arrow-right" aria-hidden="true"></i><a href="{{ route('public.admissions.requirements') }}">Review admission requirements</a></li>
                            <li><i class="bi bi-arrow-right" aria-hidden="true"></i><a href="{{ route('public.admissions.dates') }}">Check important dates</a></li>
                            <li><i class="bi bi-arrow-right" aria-hidden="true"></i><a href="{{ route('public.contact') }}">Contact the university</a></li>
                        </ul>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    <section id="application-form" class="admissions-application py-5" aria-labelledby="application-title">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="admissions-form-shell admissions-reveal">
                        <div class="admissions-form-intro">
                            <span class="admissions-section-kicker">Begin here</span>
                            <h2 id="application-title">Online admission form</h2>
                            <p>Tell us about the applicant. Our admissions registrar will contact you shortly after your inquiry is received.</p>
                            <div class="admissions-form-note"><i class="bi bi-lock-fill" aria-hidden="true"></i><span>Your details are submitted securely.</span></div>
                        </div>
                        <div class="admissions-form-body">
                            @if($errors->any())
                                <div class="alert alert-danger admissions-form-alert" role="alert"><strong>Please review the highlighted fields.</strong></div>
                            @endif
                            <form method="POST" action="{{ route('public.admissions.apply') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="admissions-name" class="form-label required fw-semibold">Applicant's Full Name</label>
                                    <input id="admissions-name" type="text" name="name" autocomplete="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Jonathan Smith">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="admissions-email" class="form-label required fw-semibold">Parent / Guardian Email</label>
                                        <input id="admissions-email" type="email" name="email" autocomplete="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="parent@email.com">
                                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="admissions-phone" class="form-label required fw-semibold">Parent Phone</label>
                                        <input id="admissions-phone" type="text" name="phone" autocomplete="tel" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required placeholder="+1 (555) 000-0000">
                                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="admissions-grade" class="form-label required fw-semibold">Grade Applying For</label>
                                    <select id="admissions-grade" name="grade_interested" class="form-select @error('grade_interested') is-invalid @enderror" required>
                                        <option value="">Select Grade Level</option>
                                        @foreach($classes as $c)
                                            <option value="{{ $c->name }}" {{ old('grade_interested') == $c->name ? 'selected' : '' }}>{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('grade_interested')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-4">
                                    <label for="admissions-message" class="form-label fw-semibold">Additional Notes / Previous School</label>
                                    <textarea id="admissions-message" name="message" class="form-control" rows="4" placeholder="Tell us about the applicant's interests, hobbies, or previous school...">{{ old('message') }}</textarea>
                                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <button type="submit" class="btn btn-crimson w-100 py-3 fw-bold admissions-submit-button"><i class="bi bi-send-fill me-2" aria-hidden="true"></i> Submit Admission Application</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection