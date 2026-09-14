@extends('layouts.public')

@section('title', 'Contact Us & Campus Location')
@section('meta_description', 'Contact Tima-Ade University — phone, email, office hours, campus location, and a secure message form for admissions, academics, and general inquiries.')

@section('content')
<div class="contact-page">
<section class="contact-hero text-white" aria-labelledby="contact-title">
    <div class="container py-5">
        <div class="row align-items-end g-5">
            <div class="col-lg-8 contact-reveal">
                <span class="contact-eyebrow"><span class="contact-eyebrow-dot"></span> Get in touch</span>
                <h1 id="contact-title">A direct line to Tima-Ade University.</h1>
                <p class="lead">We are here to help answer your questions regarding admissions, academics, and campus visits.</p>
                <div class="d-flex flex-wrap gap-3"><a href="#contact-form" class="btn btn-crimson btn-lg">Send a message <i class="bi bi-arrow-down-right ms-2" aria-hidden="true"></i></a><a href="tel:+15552345678" class="btn btn-outline-light btn-lg"><i class="bi bi-telephone me-2" aria-hidden="true"></i> Call the main office</a></div>
            </div>
            <div class="col-lg-4 contact-reveal"><div class="contact-hero-panel"><span class="small text-uppercase fw-bold text-danger">Visitor support</span><strong>Questions are welcome.</strong><p>Reach the right team by phone, email, or the secure message form below.</p><div><i class="bi bi-shield-check" aria-hidden="true"></i> Official Tima-Ade University contact channels</div></div></div>
        </div>
        <nav class="mt-5" aria-label="Breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('public.home') }}" class="text-light text-decoration-none">Home</a></li><li class="breadcrumb-item active text-danger" aria-current="page">Contact</li></ol></nav>
    </div>
</section>

<section class="contact-main py-5 bg-white" aria-labelledby="contact-details-title">
    <div class="container py-4"><div class="row g-5">
        <div class="col-lg-5 contact-reveal">
            <span class="contact-section-kicker">Campus details</span><h2 id="contact-details-title" class="contact-section-title">Connect with our team.</h2><p class="contact-intro">Choose the channel that suits your question. Our existing office information is below for quick reference.</p>
            <div class="contact-detail-card"><div class="contact-icon-box"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i></div><div><span class="contact-detail-label">Campus address</span><p>Campus Location</p></div></div>
            <div class="contact-detail-card"><div class="contact-icon-box"><i class="bi bi-telephone-fill" aria-hidden="true"></i></div><div><span class="contact-detail-label">Telephone &amp; fax</span><p>Main Office: <a href="tel:+15552345678">+1 (555) 234-5678</a><br>Admissions Desk: <a href="tel:+15552345679">+1 (555) 234-5679</a></p></div></div>
            <div class="contact-detail-card"><div class="contact-icon-box"><i class="bi bi-envelope-fill" aria-hidden="true"></i></div><div><span class="contact-detail-label">Email support</span><p>General: <a href="mailto:info@timaade.edu">info@timaade.edu</a><br>Registrar: <a href="mailto:registrar@timaade.edu">registrar@timaade.edu</a></p></div></div>
            <div class="contact-detail-card"><div class="contact-icon-box"><i class="bi bi-clock-fill" aria-hidden="true"></i></div><div><span class="contact-detail-label">Office hours</span><p>Monday - Friday: 8:00 AM – 5:00 PM<br>Saturday: 9:00 AM – 1:00 PM (By Appointment)</p></div></div>
        </div>
        <div class="col-lg-7 contact-reveal"><div id="contact-form" class="contact-form-shell">
            <div class="contact-form-heading"><span class="contact-section-kicker">Send a message</span><h2>Send an official message</h2><p>Please fill in the form below and our administration desk will reply promptly.</p><span><i class="bi bi-lock-fill" aria-hidden="true"></i> Your message is submitted securely.</span></div>
            <div class="contact-form-body">
                @if($errors->any())<div class="alert alert-danger" role="alert"><strong>Please review the highlighted fields.</strong></div>@endif
                <form method="POST" action="{{ route('public.contact.send') }}">
                    @csrf
                    <div class="row g-3 mb-3"><div class="col-md-6"><label for="contact-name" class="form-label required fw-semibold">Your Full Name</label><input id="contact-name" type="text" name="name" autocomplete="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. John Doe">@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="col-md-6"><label for="contact-email" class="form-label required fw-semibold">Email Address</label><input id="contact-email" type="email" name="email" autocomplete="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="john@example.com">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                    <div class="row g-3 mb-3"><div class="col-md-6"><label for="contact-phone" class="form-label fw-semibold">Phone Number</label><input id="contact-phone" type="text" name="phone" autocomplete="tel" class="form-control" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000"></div><div class="col-md-6"><label for="contact-subject" class="form-label required fw-semibold">Subject / Purpose</label><input id="contact-subject" type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}" required placeholder="e.g. General Inquiry or Campus Visit">@error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                    <div class="mb-4"><label for="contact-message" class="form-label required fw-semibold">Message</label><textarea id="contact-message" name="message" class="form-control @error('message') is-invalid @enderror" rows="4" required placeholder="Write your message here...">{{ old('message') }}</textarea>@error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                    <button type="submit" class="btn btn-crimson w-100 py-3 fw-bold contact-submit-button"><i class="bi bi-send-fill me-2" aria-hidden="true"></i> Send Message to Administration</button>
                </form>
            </div>
        </div></div>
    </div></div>
</section>
</div>
@endsection