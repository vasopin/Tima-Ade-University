@extends('layouts.app')
@section('title', 'Course Completion')
@section('content')
<div class="container-fluid py-4">
    <div class="mb-4"><span class="eyebrow-label">Teacher LMS</span><h2 class="dashboard-section-header mb-1">{{ $section->course->name ?? 'Course' }} completion</h2><p class="text-muted mb-0">{{ $section->code }} · {{ $section->term->name ?? 'Term' }}</p></div>
    <div class="card custom-card"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Student</th><th>Status</th><th>Progress</th><th>Missing</th><th>Final grade</th></tr></thead><tbody>@forelse($enrollments as $enrollment) @php($completion = $completions[$enrollment->id])<tr><td>{{ $enrollment->student->user->name }}</td><td class="text-capitalize">{{ $completion['status'] }}</td><td>{{ $completion['progress'] }}% ({{ $completion['graded_count'] }}/{{ $completion['assignments']->count() }})</td><td>{{ $completion['missing_count'] }}</td><td>{{ $completion['final_grade'] ?? '—' }}</td></tr>@empty<tr><td colspan="5" class="text-center py-5 text-muted">No active students are enrolled in this section.</td></tr>@endforelse</tbody></table></div><div class="card-footer">{{ $enrollments->links() }}</div></div>
</div>
@endsection
