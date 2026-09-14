@extends('layouts.public')

@section('title', 'Official Noticeboard & Bulletins')
@section('meta_description', 'Read official notices, bulletins, and announcements published by Tima-Ade University for students, families, and the wider community.')

@section('content')
<div class="noticeboard-page">
    <section class="noticeboard-hero text-white" aria-labelledby="noticeboard-title">
        <div class="container py-5">
            <div class="row align-items-end g-5">
                <div class="col-lg-8 noticeboard-reveal">
                    <span class="noticeboard-eyebrow"><span class="noticeboard-eyebrow-dot"></span> Campus bulletins</span>
                    <h1 id="noticeboard-title">Official Noticeboard</h1>
                    <p class="lead">Stay informed with the latest announcements, exam dates, academic calendars, and campus events.</p>
                    <div class="noticeboard-hero-meta"><span><i class="bi bi-broadcast-pin" aria-hidden="true"></i> Institutional updates</span><span><i class="bi bi-clock-history" aria-hidden="true"></i> Published as notices arrive</span></div>
                </div>
                <div class="col-lg-4 noticeboard-reveal">
                    <div class="noticeboard-hero-panel"><span class="small text-uppercase fw-bold text-danger">Find what matters</span><strong>Clear information, right when you need it.</strong><p>Search the official Tima-Ade University archive or filter by notice category.</p></div>
                </div>
            </div>
            <nav class="mt-5" aria-label="Breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('public.home') }}" class="text-light text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active text-danger" aria-current="page">Notices</li>
                </ol>
            </nav>
        </div>
    </section>

    @if($featuredNotice)
        <section class="container py-5" aria-labelledby="featured-media-title">
            <div class="card border-0 shadow-sm overflow-hidden bg-dark text-white">
                <div class="row g-0 align-items-center">
                    <div class="col-lg-7">
                        @if($featuredNotice->thumbnail_url)<img src="{{ $featuredNotice->thumbnail_url }}" alt="{{ $featuredNotice->title }}" class="w-100" style="aspect-ratio:16/9;object-fit:cover">@else<div class="d-flex align-items-center justify-content-center bg-secondary" style="aspect-ratio:16/9"><i class="bi bi-play-circle display-1"></i></div>@endif
                    </div>
                    <div class="col-lg-5 p-4 p-lg-5"><span class="badge bg-danger mb-3">Featured media</span><h2 id="featured-media-title">{{ $featuredNotice->title }}</h2><p class="text-white-50">{{ Str::limit($featuredNotice->content, 180) }}</p><a href="{{ route('public.notices.single', $featuredNotice) }}" class="btn btn-light"><i class="bi bi-play-fill me-1"></i> Watch video</a></div>
                </div>
            </div>
        </section>
    @endif

    <section class="noticeboard-controls py-4" aria-label="Noticeboard search and filters">
        <div class="container">
            <form method="GET" action="{{ route('public.notices') }}" class="noticeboard-filter-shell">
                <div class="noticeboard-search">
                    <label for="notice-search" class="visually-hidden">Search notices and circulars</label>
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input id="notice-search" type="search" name="search" placeholder="Search notices and circulars..." value="{{ request('search') }}">
                </div>
                <div class="noticeboard-category">
                    <label for="notice-category" class="visually-hidden">Filter by notice category</label>
                    <select id="notice-category" name="category">
                        <option value="">All notice categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-navy noticeboard-filter-button"><i class="bi bi-sliders2 me-2" aria-hidden="true"></i>Filter notices</button>
                @if(request()->hasAny(['search', 'category']))
                    <a href="{{ route('public.notices') }}" class="noticeboard-reset" aria-label="Reset notice filters"><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i><span>Reset</span></a>
                @endif
            </form>
        </div>
    </section>

    <section class="noticeboard-list py-5 bg-white" aria-labelledby="latest-notices-title">
        <div class="container py-4">
            <div class="noticeboard-section-heading">
                <div><span class="noticeboard-section-kicker">Official updates</span><h2 id="latest-notices-title">Latest notices</h2></div>
                @if($notices->total() > 0)<p aria-live="polite">Showing {{ $notices->firstItem() }} - {{ $notices->lastItem() }} of {{ $notices->total() }} notices</p>@endif
            </div>
            <div class="noticeboard-items">
                @forelse($notices as $notice)
                    <article class="noticeboard-item {{ $notice->is_pinned ? 'is-pinned' : '' }} noticeboard-reveal">
                        <div class="noticeboard-date" aria-label="Published {{ $notice->published_date->format('F j, Y') }}"><strong>{{ $notice->published_date->format('d') }}</strong><span>{{ $notice->published_date->format('M') }}</span><small>{{ $notice->published_date->format('Y') }}</small></div>
                        <div class="noticeboard-item-content">
                            <div class="noticeboard-item-meta"><span class="noticeboard-category-badge">{{ $notice->category }}</span>@if($notice->is_pinned)<span class="noticeboard-pinned"><i class="bi bi-pin-angle-fill" aria-hidden="true"></i> Pinned</span>@endif<span class="noticeboard-meta-type"><i class="bi {{ $notice->video_url ? 'bi-camera-video' : 'bi-megaphone' }}" aria-hidden="true"></i> {{ $notice->video_url ? 'Video bulletin' : 'Official notice' }}</span></div>
                            <h3><a href="{{ route('public.notices.single', $notice) }}">{{ $notice->title }}</a></h3>
                            <p>{{ Str::limit($notice->content, 180) }}</p>
                            <a href="{{ route('public.notices.single', $notice) }}" class="noticeboard-read-link">Read announcement <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                        </div>
                    </article>
                @empty
                    <div class="noticeboard-empty" role="status"><i class="bi bi-search" aria-hidden="true"></i><h3>No notices matching your criteria</h3><p>Try a different search or return to the complete noticeboard.</p>@if(request()->hasAny(['search', 'category']))<a href="{{ route('public.notices') }}" class="btn btn-outline-danger">View all notices</a>@endif</div>
                @endforelse
            </div>
            @if($notices->hasPages())
                <nav class="noticeboard-pagination mt-5" aria-label="Noticeboard pages">{{ $notices->links() }}</nav>
            @endif
        </div>
    </section>
</div>
@endsection