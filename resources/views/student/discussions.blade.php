@extends('layouts.app')
@section('title', 'Course Discussions')
@section('content')
<div class="container-fluid py-4"><div class="mb-4"><span class="eyebrow-label">Student LMS</span><h2 class="dashboard-section-header">Course Discussions</h2></div><div class="row g-3">@forelse($discussions as $discussion)<div class="col-lg-6"><div class="card custom-card h-100"><div class="card-body"><h5>{{ $discussion->title }}</h5><p class="text-muted">{{ $discussion->description }}</p><small class="text-muted">{{ $discussion->courseSection?->course?->name ?? $discussion->subject?->name ?? 'Class discussion' }}</small><div class="mt-3"><a href="{{ route('student.discussions.show', $discussion) }}" class="btn btn-sm btn-primary">Join discussion</a></div></div></div></div>@empty<div class="col-12"><div class="empty-lms-state">No discussions are currently available for your courses.</div></div>@endforelse</div></div>
@endsection
