@extends('layouts.public')

@section('title', $notice->title)
@section('meta_description', Illuminate\Support\Str::limit(strip_tags($notice->content), 155))

@section('content')
<div class="notice-detail-page">
    <section class="notice-detail-hero text-white" aria-labelledby="notice-title">
        <div class="container py-5">
            <div class="row align-items-end g-5">
                <div class="col-lg-8">
                    <div class="noticeboard-item-meta"><span class="noticeboard-category-badge">{{ $notice->category }}</span>@if($notice->is_pinned)<span class="noticeboard-pinned"><i class="bi bi-pin-angle-fill" aria-hidden="true"></i> Pinned notice</span>@endif</div>
                    <h1 id="notice-title">{{ $notice->title }}</h1>
                    <p class="notice-detail-date"><i class="bi bi-calendar3" aria-hidden="true"></i> Published on <time datetime="{{ $notice->published_date->format('Y-m-d') }}">{{ $notice->published_date->format('l, F j, Y') }}</time></p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <nav aria-label="Breadcrumb"><ol class="breadcrumb justify-content-lg-end mb-0"><li class="breadcrumb-item"><a href="{{ route('public.home') }}" class="text-light text-decoration-none">Home</a></li><li class="breadcrumb-item"><a href="{{ route('public.notices') }}" class="text-light text-decoration-none">Notices</a></li><li class="breadcrumb-item active text-danger" aria-current="page">Bulletin</li></ol></nav>
                </div>
            </div>
        </div>
    </section>

    <section class="notice-detail-content py-5 bg-white">
        <div class="container py-4">
            <div class="row g-5 align-items-start">
                <main class="col-lg-8">
                    <article class="notice-detail-article">
                        <div class="notice-detail-article-top"><span class="noticeboard-section-kicker">Official announcement</span><span class="notice-detail-type"><i class="bi bi-file-text" aria-hidden="true"></i> Circular</span></div>
                        @if($notice->video_url)
                            <div class="ratio ratio-16x9 mb-4 bg-dark rounded overflow-hidden"><video controls preload="metadata" @if($notice->thumbnail_url) poster="{{ $notice->thumbnail_url }}" @endif><source src="{{ $notice->video_url }}" type="{{ $notice->video_mime_type ?: 'video/mp4' }}">Your browser does not support video playback.</video></div>
                        @endif
                        <div class="notice-body"><p>{{ $notice->content }}</p></div>
                        <div class="notice-detail-actions"><a href="{{ route('public.notices') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Back to Noticeboard</a><button type="button" onclick="window.print()" class="btn btn-navy"><i class="bi bi-printer me-1" aria-hidden="true"></i> Print Notice</button></div>
                    </article>
                </main>
                <aside class="col-lg-4" aria-labelledby="recent-notices-title">
                    <div class="notice-related-panel"><span class="noticeboard-section-kicker">Keep reading</span><h2 id="recent-notices-title">Recent circulars</h2><div class="notice-related-list">
                        @forelse($recentNotices as $rn)
                            <a href="{{ route('public.notices.single', $rn) }}" class="notice-related-item"><span class="notice-related-date">{{ $rn->published_date->format('M d, Y') }}</span><strong>{{ $rn->title }}</strong><span class="noticeboard-category-badge">{{ $rn->category }}</span></a>
                        @empty
                            <p class="text-muted mb-0">No other notices.</p>
                        @endforelse
                    </div></div>
                </aside>
            </div>
        </div>
    </section>
</div>
@endsection