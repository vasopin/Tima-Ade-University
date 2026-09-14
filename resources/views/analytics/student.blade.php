@extends('layouts.app')
@section('title', 'Learning Analytics')
@section('content')
<div class="container-fluid py-4"><span class="eyebrow-label">Student LMS</span><h2 class="dashboard-section-header">Learning Analytics</h2>
<div class="row g-3 mb-4">@foreach(['Enrolled courses' => $enrollments->count(), 'Assignments submitted' => $assignments['submitted'], 'Assignments graded' => $assignments['graded'], 'Quiz average' => $quizzes['average']] as $label => $value)<div class="col-md-3"><div class="card custom-card"><div class="card-body"><small class="text-muted">{{ $label }}</small><h3>{{ $value }}</h3></div></div></div>@endforeach</div>
<div class="card custom-card"><div class="card-body"><h5>Course progress</h5><div class="table-responsive"><table class="table"><thead><tr><th>Course</th><th>Status</th><th>Progress</th><th>Missing</th></tr></thead><tbody>@foreach($courses as $course)<tr><td>{{ $course['section']->course->name ?? 'Course' }}</td><td>{{ ucfirst($course['status']) }}</td><td>{{ $course['progress'] }}%</td><td>{{ $course['missing_count'] }}</td></tr>@endforeach</tbody></table></div></div></div></div>
@endsection
