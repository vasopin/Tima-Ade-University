@extends('layouts.public')

@section('title', 'Campuses — Tima-Ade University')
@section('meta_description', 'Explore Tima-Ade University campus locations in Gabiley, Somaliland, with visual highlights of the learning environments across the institution.')

@section('content')
@php
    $images = $aboutImages ?? config('about.images');
    $campusGallery = [
        ['image' => $images['hero']['src'], 'alt' => $images['hero']['alt'], 'title' => 'Campus environment', 'caption' => $images['hero']['caption']],
        ['image' => $images['classroom']['src'], 'alt' => $images['classroom']['alt'], 'title' => 'Learning in context', 'caption' => $images['classroom']['caption']],
        ['image' => $images['library']['src'], 'alt' => $images['library']['alt'], 'title' => 'Space for inquiry', 'caption' => $images['library']['caption']],
        ['image' => $images['laboratory']['src'], 'alt' => $images['laboratory']['alt'], 'title' => 'Learning through inquiry', 'caption' => $images['laboratory']['caption']],
        ['image' => $images['community']['src'], 'alt' => $images['community']['alt'], 'title' => 'University community', 'caption' => $images['community']['caption']],
        ['image' => $images['event']['src'], 'alt' => $images['event']['alt'], 'title' => 'Community in motion', 'caption' => $images['event']['caption']],
    ];
@endphp
<section class="public-page-header bg-navy text-white py-5">
    <div class="container py-5">
        <span class="badge bg-crimson mb-3">Places to learn and belong</span>
        <h1 class="display-4 fw-bold mb-3">Tima-Ade Campuses</h1>
        <p class="lead opacity-90 mb-0 col-lg-8">Explore the places where Tima-Ade University’s academic community learns, connects, and grows.</p>
    </div>
</section>
<section class="py-5 bg-white"><div class="container py-4"><div class="row g-5 align-items-center"><div class="col-lg-6"><span class="text-danger fw-bold text-uppercase small tracking-wider">Campus overview</span><h2 class="section-heading fw-bold mt-2 mb-3">A university rooted in Gabiley.</h2><p class="text-muted mb-0">Tima-Ade University identifies Gabiley, Somaliland as its campus location. Official campus profiles and facility details will be added as they are confirmed by the university.</p></div><div class="col-lg-6"><div class="position-relative about-story-visual"><img src="{{ asset($images['hero']['src']) }}" alt="{{ $images['hero']['alt'] }}" class="img-fluid rounded-4"><div class="about-story-caption"><span class="text-danger fw-bold">Campus location</span><small>Gabiley, Somaliland</small></div></div></div></div></div></section>
<section class="py-5 bg-light"><div class="container py-4"><div class="text-center max-w-700 mx-auto mb-5"><span class="text-danger fw-bold text-uppercase small tracking-wider">Campus locations</span><h2 class="section-heading fw-bold mt-2">Official campus information</h2><p class="text-muted">The details below are intentionally kept accurate and replaceable while the university confirms its complete campus directory.</p></div><div class="row g-4"><div class="col-lg-6"><article class="card h-100 border-0 shadow-sm about-card"><img src="{{ asset($images['hero']['src']) }}" class="card-img-top" alt="{{ $images['hero']['alt'] }}"><div class="card-body p-4"><span class="text-danger fw-bold text-uppercase small"><i class="bi bi-geo-alt me-1"></i> Confirmed location</span><h3 class="h4 fw-bold mt-2">Tima-Ade University — Gabiley</h3><p class="text-muted">The university’s identified location is Gabiley, Somaliland.</p><div class="border-top pt-3 small text-muted"><strong>Facilities:</strong> Official academic buildings, library, laboratory, student, and administrative facility details pending confirmation.</div></div></article></div><div class="col-lg-6"><article class="card h-100 border-0 shadow-sm about-card"><div class="card-body p-4 d-flex flex-column"><span class="text-danger fw-bold text-uppercase small"><i class="bi bi-info-circle me-1"></i> Directory update</span><h3 class="h4 fw-bold mt-2">Additional locations</h3><p class="text-muted">No additional campus location is listed here until officially confirmed by Tima-Ade University.</p><div class="mt-auto border-top pt-3 small text-muted">Contact the university for current campus and visit information.</div></div></article></div></div></div></section>
<section class="about-campus-highlights py-5 bg-white"><div class="container py-3"><div class="row g-4"><div class="col-md-3 col-6"><i class="bi bi-building fs-2 text-danger"></i><p class="fw-bold mt-3 mb-0">Academic buildings</p></div><div class="col-md-3 col-6"><i class="bi bi-book fs-2 text-danger"></i><p class="fw-bold mt-3 mb-0">Library</p></div><div class="col-md-3 col-6"><i class="bi bi-flask fs-2 text-danger"></i><p class="fw-bold mt-3 mb-0">Laboratories</p></div><div class="col-md-3 col-6"><i class="bi bi-people fs-2 text-danger"></i><p class="fw-bold mt-3 mb-0">Student facilities</p></div></div></div></section>
<section class="py-5 bg-light" aria-labelledby="campus-gallery-title">
    <div class="container py-4">
        <div class="row align-items-end g-4 mb-4">
            <div class="col-lg-8"><span class="text-danger fw-bold text-uppercase small tracking-wider">Visual story</span><h2 id="campus-gallery-title" class="section-heading fw-bold mt-2 mb-0">See the environments that shape learning.</h2></div>
            <div class="col-lg-4"><p class="text-muted mb-0">A curated view of approved university imagery. Additional campus details will be added when officially confirmed.</p></div>
        </div>
        <x-media-gallery :items="$campusGallery" class="gallery-3col" modal-id="campus-gallery" aria-label="Tima-Ade University campus imagery" />
    </div>
</section>
<section class="about-campus-visit py-5 bg-navy text-white"><div class="container py-4 d-flex flex-column flex-md-row justify-content-between gap-4 align-items-md-center"><div><span class="text-danger fw-bold text-uppercase small">Plan a visit</span><h2 class="h2 fw-bold mt-2 mb-0">Connect with Tima-Ade University</h2></div><div class="text-light small"><div><i class="bi bi-geo-alt-fill text-danger me-2"></i>Gabiley, Somaliland</div><div class="mt-2"><i class="bi bi-envelope-fill text-danger me-2"></i>Contact details to be confirmed</div></div></div></section>
@endsection
