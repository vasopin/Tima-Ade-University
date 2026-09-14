@extends('layouts.public')

@section('title', $query ? 'Search results for ' . $query : 'Search Tima-Ade University')
@section('meta_description', 'Search public information from Tima-Ade University.')
@section('content')
<div class="global-search-page">
    <section class="global-search-hero" aria-labelledby="search-title">
        <div class="container py-5">
            <p class="global-search-kicker">Tima-Ade University</p>
            <h1 id="search-title">Search the university.</h1>
            <p>Find public information across academics, admissions, campus life, and university services.</p>
            <form action="{{ route('public.search') }}" method="GET" role="search" class="global-search-main-form">
                <label for="search-page-input" class="visually-hidden">Search Tima-Ade University</label>
                <i class="bi bi-search" aria-hidden="true"></i>
                <input id="search-page-input" name="q" type="search" value="{{ $query }}" placeholder="Search Tima-Ade University" autocomplete="off" autofocus>
                <button type="submit" class="btn btn-crimson">Search <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i></button>
            </form>
        </div>
    </section>

    <main class="container py-5" aria-live="polite">
        @if($query !== '')
            <div class="global-search-heading"><div><p class="global-search-kicker">Search results</p><h2>Results for “{{ $query }}”</h2></div><span>{{ $results->count() }} {{ $results->count() === 1 ? 'result' : 'results' }}</span></div>
            @if($results->isNotEmpty())
                <div class="global-search-results" role="list">
                    @foreach($results as $result)
                        <article class="global-search-result" role="listitem"><div class="global-search-result-index">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div><div><p class="global-search-category">{{ $result['category'] }}</p><h3><a href="{{ route($result['route']) }}">{{ $result['title'] }}</a></h3><p class="global-search-excerpt">{{ $result['excerpt'] }}</p><a class="global-search-result-link" href="{{ route($result['route']) }}">View page <i class="bi bi-arrow-up-right ms-1" aria-hidden="true"></i></a></div></article>
                    @endforeach
                </div>
            @else
                <section class="global-search-empty" aria-labelledby="search-empty-title"><i class="bi bi-search" aria-hidden="true"></i><h2 id="search-empty-title">No results found</h2><p>We couldn't find anything matching your search. Try fewer words or search for a broader topic.</p><div class="d-flex flex-wrap justify-content-center gap-2"><a href="{{ route('public.admissions') }}" class="btn btn-outline-dark">Admissions</a><a href="{{ route('public.programs') }}" class="btn btn-outline-dark">Programs</a><a href="{{ route('public.about') }}" class="btn btn-outline-dark">About</a><a href="{{ route('public.contact') }}" class="btn btn-outline-dark">Contact</a></div></section>
            @endif
        @else
            <section class="global-search-intro"><p class="global-search-kicker">Explore by area</p><h2>Where would you like to begin?</h2><p>Search by topic, page, or service. Your results stay within public university content.</p></section>
            <div class="global-search-topic-grid"><a href="{{ route('public.admissions') }}"><span>Admissions</span><i class="bi bi-arrow-up-right" aria-hidden="true"></i></a><a href="{{ route('public.programs') }}"><span>Programs</span><i class="bi bi-arrow-up-right" aria-hidden="true"></i></a><a href="{{ route('public.facilities') }}"><span>E-Campus</span><i class="bi bi-arrow-up-right" aria-hidden="true"></i></a><a href="{{ route('public.student-life') }}"><span>Student Life</span><i class="bi bi-arrow-up-right" aria-hidden="true"></i></a><a href="{{ route('public.about') }}"><span>About</span><i class="bi bi-arrow-up-right" aria-hidden="true"></i></a><a href="{{ route('public.contact') }}"><span>Contact</span><i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></div>
        @endif
    </main>
</div>
@endsection
