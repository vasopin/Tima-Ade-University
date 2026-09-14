@extends('layouts.app')
@section('title', 'Assignment Submissions')
@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-1">{{ $assignment->title }}</h2>
    <p class="text-muted">Submissions and grading</p>
    <div class="card"><div class="table-responsive"><table class="table align-middle mb-0">
        <thead><tr><th>Student</th><th>Attempt</th><th>Status</th><th>Submitted</th><th>Score</th><th>Grade</th></tr></thead>
        <tbody>@forelse($submissions as $submission)<tr>
            <td>{{ $submission->student->user->name }}</td><td>{{ $submission->attempt_number }}</td><td>{{ ucfirst($submission->status) }}{{ $submission->is_late ? ' (Late)' : '' }}</td><td>{{ $submission->submitted_at?->format('Y-m-d H:i') ?? 'Draft' }}</td>
            <td>@if($submission->file_path)<a href="{{ route('learning.submission.file', $submission) }}">Download</a>@endif</td>
            <td>
                <form method="POST" action="{{ route('teacher.learning.submission.grade', $submission) }}" class="d-flex flex-column gap-2">@csrf
                    @if($assignment->rubric)
                        @foreach($assignment->rubric->criteria as $criterion)
                            <label class="small">{{ $criterion->name }} ({{ $criterion->max_points }} pts)
                                <input name="rubric_scores[{{ $loop->index }}][criterion_id]" type="hidden" value="{{ $criterion->id }}">
                                <input name="rubric_scores[{{ $loop->index }}][awarded_points]" type="number" min="0" max="{{ $criterion->max_points }}" step="0.01" value="{{ $submission->rubricScores->firstWhere('rubric_criterion_id', $criterion->id)?->awarded_points }}" class="form-control form-control-sm" required>
                            </label>
                        @endforeach
                    @else
                        <input name="score" type="number" min="0" max="{{ $assignment->max_points }}" step="0.01" value="{{ $submission->score }}" class="form-control form-control-sm" required>
                    @endif
                    <input name="feedback" value="{{ $submission->feedback }}" placeholder="Feedback" class="form-control form-control-sm">
                    <button class="btn btn-sm btn-primary">Save</button>
                </form>
            </td>
        </tr>@empty<tr><td colspan="6" class="text-center py-4 text-muted">No submissions yet.</td></tr>@endforelse</tbody>
    </table></div><div class="card-footer">{{ $submissions->links() }}</div></div>
</div>
@endsection
