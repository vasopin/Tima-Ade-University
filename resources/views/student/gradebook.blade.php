@extends('layouts.app')
@section('title', 'My Gradebook')
@section('content')
<div class="container-fluid py-4">
    <div class="mb-4"><span class="eyebrow-label">Academic Progress</span><h2 class="dashboard-section-header mb-1">My Gradebook</h2><p class="text-muted mb-0">Your coursework results, feedback, and submission status.</p></div>
    <div class="row g-3">@forelse($rows as $row) @php($assignment = $row['assignment']) @php($submission = $row['submission'])<div class="col-lg-6"><div class="card custom-card h-100"><div class="card-body">
        <div class="d-flex justify-content-between gap-3"><div><span class="badge bg-light text-dark mb-2">{{ $assignment->subject->name ?? 'Course' }}</span><h5>{{ $assignment->title }}</h5><small class="text-muted">{{ $assignment->teacher->user->name ?? 'Instructor' }}</small></div>
        @if(!$submission)<span class="badge bg-light text-muted align-self-start">Missing</span>@elseif($submission->status === 'draft')<span class="badge bg-secondary align-self-start">Draft</span>@elseif($submission->status === 'submitted')<span class="badge bg-warning-subtle text-warning align-self-start">Awaiting grade</span>@else<span class="badge bg-success-subtle text-success align-self-start">Graded</span>@endif</div>
        @if($submission?->score !== null)<div class="display-6 mt-3">{{ $submission->score }} <small class="fs-6 text-muted">/ {{ $assignment->max_points }} ({{ $row['percentage'] }}%)</small></div>@endif
        @if($submission?->is_late)<span class="badge bg-warning-subtle text-warning">Late submission</span>@endif
        @if($submission?->feedback)<div class="border-top mt-3 pt-3"><strong>Feedback</strong><p class="mb-0">{{ $submission->feedback }}</p>@if($submission->grader)<small class="text-muted">— {{ $submission->grader->name }}</small>@endif</div>@endif
        @if($submission?->status === 'graded' && $submission->rubricScores->isNotEmpty())<div class="border-top mt-3 pt-3"><strong>Rubric results</strong><div class="d-flex flex-wrap gap-1 mt-2">@foreach($submission->rubricScores->unique('rubric_criterion_id') as $score)<span class="badge bg-light text-dark">{{ $score->criterion_name }}: {{ $score->awarded_points }}/{{ $score->criterion_max_points }}</span>@endforeach</div></div>@endif
    </div></div></div>@empty<div class="col-12"><div class="empty-lms-state">No published coursework is available for your class.</div></div>@endforelse</div>
</div>
@endsection
