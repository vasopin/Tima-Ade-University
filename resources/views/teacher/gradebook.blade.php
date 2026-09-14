@extends('layouts.app')
@section('title', 'LMS Gradebook')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div><span class="eyebrow-label">Learning Management</span><h2 class="dashboard-section-header mb-1">Gradebook</h2><p class="text-muted mb-0">Coursework grades for your assigned classes only.</p></div>
        <div class="d-flex gap-2"><span class="badge bg-primary-subtle text-primary p-2">{{ $summary['students'] }} students</span><span class="badge bg-light text-dark p-2">{{ $summary['assignments'] }} activities</span></div>
    </div>
    <form method="GET" class="card custom-card mb-4"><div class="card-body row g-2 align-items-end">
        <div class="col-md-4"><label class="form-label" for="student">Student</label><input id="student" name="student" value="{{ request('student') }}" class="form-control" placeholder="Name or admission number"></div>
        <div class="col-md-3"><label class="form-label" for="assignment">Assignment</label><select id="assignment" name="assignment" class="form-select"><option value="">All activities</option>@foreach($assignments as $assignment)<option value="{{ $assignment->id }}" @selected((int) request('assignment') === $assignment->id)>{{ $assignment->title }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label" for="state">Show students with</label><select id="state" name="state" class="form-select"><option value="">Any status</option>@foreach(['graded'=>'Graded','ungraded'=>'Submitted / ungraded','missing'=>'Missing','draft'=>'Draft','late'=>'Late','rubric'=>'Rubric activity'] as $value => $label)<option value="{{ $value }}" @selected(request('state') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-grow-1">Filter</button><a href="{{ route('teacher.gradebook') }}" class="btn btn-outline-secondary">Clear</a></div>
    </div></form>
    <div class="card custom-card"><div class="table-responsive"><table class="table table-hover align-middle mb-0" style="min-width: 900px">
        <thead><tr><th>Student</th>@foreach($assignments as $assignment)<th><div>{{ $assignment->title }}</div><small class="text-muted">{{ $assignment->max_points }} pts{{ $assignment->rubric ? ' · Rubric' : '' }}</small></th>@endforeach</tr></thead>
        <tbody>@forelse($rows as $row)<tr><th><div>{{ $row['student']->user->name }}</div><small class="text-muted">{{ $row['student']->admission_number }}</small></th>
            @foreach($row['cells'] as $cell) @php($submission = $cell['submission'])<td>
                @if(!$submission)<span class="badge bg-light text-muted">Missing</span>
                @elseif($submission->status === 'draft')<span class="badge bg-secondary">Draft</span>
                @elseif($submission->status === 'submitted')<span class="badge bg-warning-subtle text-warning">Ungraded{{ $submission->is_late ? ' · Late' : '' }}</span>
                @else<div><strong>{{ $submission->score }}/{{ $cell['assignment']->max_points }}</strong> <small class="text-muted">({{ $cell['percentage'] }}%)</small></div><span class="badge bg-success-subtle text-success">Graded{{ $submission->is_late ? ' · Late' : '' }}</span>@endif
                <div><a class="small" href="{{ route('teacher.learning.assignment.submissions', $cell['assignment']) }}">Review</a></div>
            </td>@endforeach
        </tr>@empty<tr><td colspan="{{ $assignments->count() + 1 }}" class="text-center py-5 text-muted">No students or activities match these filters.</td></tr>@endforelse</tbody>
    </table></div></div>
</div>
@endsection
