@extends('layouts.public')

@section('title', 'Board of Directors — Tima-Ade University')
@section('meta_description', 'Meet the governance structure of Tima-Ade University — the Board of Directors provides stewardship, accountability, and strategic direction for the institution.')

@section('content')
@php($images = $aboutImages ?? config('about.images'))
<section class="public-page-header bg-navy text-white py-5">
    <div class="container py-5">
        <span class="badge bg-crimson mb-3">University Governance</span>
        <h1 class="display-4 fw-bold mb-3">Board of Directors</h1>
        <p class="lead opacity-90 mb-0 col-lg-8">The Board of Directors provides stewardship, accountability, and strategic guidance for Tima-Ade University.</p>
    </div>
</section>
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row g-5 align-items-end mb-5">
            <div class="col-lg-7"><span class="text-danger fw-bold text-uppercase small tracking-wider">Institutional leadership</span><h2 class="section-heading fw-bold mt-2 mb-3">Governance grounded in purpose.</h2><p class="text-muted mb-0">The Board safeguards the university mission and supports responsible academic, financial, and institutional development.</p></div>
            <div class="col-lg-4 ms-auto"><div class="border-start border-4 border-danger ps-4 text-muted small">Official member names, portraits, biographies, and portfolios will be published here after verification by the university.</div></div>
        </div>
        <div class="row g-4">
            @foreach ([['Chair, Board of Directors', 'Board member profile to be confirmed'], ['Deputy Chair', 'Board member profile to be confirmed'], ['Academic governance representative', 'Board member profile to be confirmed'], ['Community representative', 'Board member profile to be confirmed']] as $member)
                <div class="col-md-6">
                    <article class="card h-100 border-0 shadow-sm overflow-hidden about-card">
                        <div class="about-image-slot bg-light d-flex align-items-center justify-content-center" style="height: 220px"><img src="{{ asset($images['community']['src']) }}" alt="{{ $images['community']['alt'] }}" class="w-100 h-100 object-fit-cover opacity-50"><span class="about-slot-label">Portrait pending verification</span></div>
                        <div class="card-body p-4"><span class="text-danger fw-bold text-uppercase small">{{ $member[0] }}</span><h3 class="h4 fw-bold mt-2">{{ $member[1] }}</h3><p class="text-muted small mb-0">Verified professional biography and governance responsibilities pending official confirmation.</p></div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
<section class="py-5 bg-light"><div class="container py-4"><div class="row g-4"><div class="col-md-4"><i class="bi bi-shield-check fs-2 text-danger"></i><h3 class="h5 fw-bold mt-3">Accountability</h3><p class="small text-muted">Responsible oversight of institutional purpose and standards.</p></div><div class="col-md-4"><i class="bi bi-mortarboard fs-2 text-danger"></i><h3 class="h5 fw-bold mt-3">Academic quality</h3><p class="small text-muted">Support for a strong and student-centered learning environment.</p></div><div class="col-md-4"><i class="bi bi-compass fs-2 text-danger"></i><h3 class="h5 fw-bold mt-3">Stewardship</h3><p class="small text-muted">Long-term guidance for a resilient university.</p></div></div></div></section>
@endsection
