@extends('layouts.public')

@section('title', 'Faculty & Educators Directory')
@section('meta_description', 'Browse the Tima-Ade University faculty directory — meet the educators supporting classes, subjects, and academic guidance across the institution.')

@section('content')
<!-- Page Header -->
<section class="public-page-header bg-navy text-white py-5">
    <div class="container py-3">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-crimson mb-2">Faculty & Mentors</span>
                <h1 class="display-5 fw-bold mb-2">Faculty Directory</h1>
                <p class="lead opacity-90 mb-0">Meet the dedicated scholars and teachers guiding students toward academic distinction.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-lg-end mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('public.home') }}" class="text-light text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item active text-danger" aria-current="page">Faculty</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Search Filter -->
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <form method="GET" action="{{ route('public.faculty') }}" class="row g-2 align-items-center">
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by faculty name, subject specialization, or qualification..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-navy flex-grow-1">Search Faculty</button>
                @if(request()->has('search'))
                    <a href="{{ route('public.faculty') }}" class="btn btn-outline-secondary" title="Reset Search"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>
</section>

<!-- Faculty Cards Grid -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row g-4">
            @forelse($teachers as $teacher)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border rounded-4 shadow-sm text-center p-4 faculty-card">
                        <img src="{{ $teacher->user->avatar_url }}" class="rounded-circle border shadow-sm mx-auto mb-3" width="96" height="96" alt="">
                        <h4 class="fw-bold mb-1 text-dark">{{ $teacher->user->name }}</h4>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle mb-3">
                            {{ $teacher->specialization ?? 'Faculty Member' }}
                        </span>

                        <div class="text-start small p-3 bg-light rounded-3 mb-3">
                            <div class="mb-1">
                                <span class="text-muted">Qualification:</span>
                                <span class="fw-semibold text-dark">{{ $teacher->qualification ?? 'Educator' }}</span>
                            </div>
                            <div class="mb-1">
                                <span class="text-muted">Class Lead:</span>
                                <span class="fw-semibold text-success">
                                    {{ $teacher->classTeacherOf ? $teacher->classTeacherOf->name : 'General Faculty' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-muted">Employee ID:</span>
                                <span class="fw-semibold">{{ $teacher->employee_id }}</span>
                            </div>
                        </div>

                        <div class="mt-auto">
                            <a href="mailto:{{ $teacher->user->email }}" class="btn btn-sm btn-outline-dark w-100">
                                <i class="bi bi-envelope me-1 text-danger"></i> {{ $teacher->user->email }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-person-x fs-1 text-secondary d-block mb-2"></i>
                    <h5 class="text-muted">No faculty members found matching your search.</h5>
                    <a href="{{ route('public.faculty') }}" class="btn btn-outline-secondary btn-sm mt-2">Clear Filter</a>
                </div>
            @endforelse
        </div>

        @if($teachers->hasPages())
            <div class="mt-5">
                {{ $teachers->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
