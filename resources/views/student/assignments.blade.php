@extends('layouts.app')

@section('title', 'Student Assignments')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Student Dashboard</a></li>
    <li class="breadcrumb-item active">Assignments</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <span class="eyebrow-label">Academic Tasks</span>
            <h2 class="dashboard-section-header mb-0">Assignments</h2>
        </div>
        @if($student && $student->schoolClass)
            <span class="badge bg-warning-subtle text-warning px-3 py-2">{{ $student->schoolClass->name }}</span>
        @endif
    </div>

    @if($assignments->isNotEmpty())
        <div class="row g-3">
            @foreach($assignments as $assignment)
                <div class="col-lg-6">
                    <div class="card custom-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <span class="badge bg-light text-dark mb-2">{{ $assignment->subject->name ?? 'Course' }}</span>
                                    <h5 class="mb-1">{{ $assignment->title }}</h5>
                                    <small class="text-muted">Due {{ $assignment->due_date?->format('M d, Y') ?? 'Soon' }} · {{ $assignment->schoolClass->name ?? 'Class' }}</small>
                                </div>
                                <span class="badge bg-primary-subtle text-primary">{{ $assignment->status ?? 'Open' }}</span>
                            </div>

                            <p class="text-muted mb-3">
                                {{ $assignment->description ?: 'No description added for this assignment yet.' }}
                            </p>

                            <div class="small text-muted d-flex flex-wrap gap-3 mb-3">
                                <span><i class="bi bi-person me-1"></i>{{ $assignment->teacher->user->name ?? 'Instructor' }}</span>
                                <span><i class="bi bi-calendar3 me-1"></i>{{ $assignment->created_at?->format('M d, Y') }}</span>
                                @if($assignment->file_path)
                                    <span><i class="bi bi-download me-1"></i>{{ $assignment->readable_size ?? 'Attached file' }}</span>
                                @endif
                            </div>

                            @if($assignment->file_path)
                                <a href="{{ $assignment->file_url }}" class="btn btn-sm btn-primary" target="_blank">Open attachment</a>
                            @else
                                <button class="btn btn-sm btn-outline-secondary" type="button" disabled>No attachment</button>
                            @endif
                            @php($latestSubmission = $assignment->submissions->first())
                            <div class="mt-3 border-top pt-3">
                                @forelse($assignment->submissions as $submission)
                                    <div class="small text-muted mb-1">Attempt {{ $submission->attempt_number }} · {{ ucfirst($submission->status) }} · {{ $submission->submitted_at?->format('M d, Y H:i') ?? 'Not submitted' }}{{ $submission->is_late ? ' · Late' : '' }}{{ $submission->score !== null ? ' · '.$submission->score.'/'.$assignment->max_points : '' }}</div>
                                    @if($submission->feedback)<div class="small mb-2"><strong>Feedback:</strong> {{ $submission->feedback }} @if($submission->grader)<span class="text-muted">({{ $submission->grader->name }})</span>@endif</div>@endif
                                    @if($submission->status === 'graded' && $submission->rubricScores->isNotEmpty())
                                        <div class="small mb-2">
                                            <strong>Rubric:</strong>
                                            @foreach($submission->rubricScores->unique('rubric_criterion_id') as $rubricScore)
                                                <span class="badge bg-light text-dark me-1">{{ $rubricScore->criterion_name }}: {{ $rubricScore->awarded_points }}/{{ $rubricScore->criterion_max_points }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                @empty
                                    <div class="small text-muted mb-2">No submissions yet.</div>
                                @endforelse
                                @if($latestSubmission?->status === 'draft')
                                    <form method="POST" action="{{ route('student.assignments.submissions.update', $latestSubmission) }}" enctype="multipart/form-data" class="d-flex gap-2 align-items-center mb-2">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="submitted">
                                        <input type="file" name="submission_file" class="form-control form-control-sm">
                                        <button class="btn btn-sm btn-success">Submit draft</button>
                                    </form>
                                @elseif($assignment->status === 'published' && (!$latestSubmission || $latestSubmission->attempt_number < $assignment->max_attempts))
                                    <form method="POST" action="{{ route('student.assignments.submit', $assignment) }}" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
                                        @csrf
                                        <input type="file" name="submission_file" class="form-control form-control-sm" required>
                                        <button class="btn btn-sm btn-success">Submit</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-lms-state">
            <i class="bi bi-list-task me-2"></i>
            No assignments are currently assigned to your enrolled courses.
        </div>
    @endif
</div>
@endsection
