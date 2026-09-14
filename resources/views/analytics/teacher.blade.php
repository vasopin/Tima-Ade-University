@extends('layouts.app')
@section('title', 'Teaching Analytics')
@section('content')
<div class="container-fluid py-4"><span class="eyebrow-label">Teacher LMS</span><h2 class="dashboard-section-header">Teaching Analytics</h2>
<div class="row g-3 mb-4">@foreach(['Sections' => $report['sections']->count(), 'Active students' => $report['students'], 'Assignments' => $report['assignments']['total'], 'Quiz average' => $report['quizzes']['average']] as $label => $value)<div class="col-md-3"><div class="card custom-card"><div class="card-body"><small class="text-muted">{{ $label }}</small><h3>{{ $value }}</h3></div></div></div>@endforeach</div>
<div class="card custom-card"><div class="card-body"><h5>Assessment activity</h5><p>Submitted assignments: {{ $report['assignments']['submitted'] }} · Graded: {{ $report['assignments']['graded'] }} · Late: {{ $report['assignments']['late'] }}</p><p>Quiz attempts: {{ $report['quizzes']['attempts'] }} · Graded attempts: {{ $report['quizzes']['graded'] }}</p></div></div></div>
@endsection
