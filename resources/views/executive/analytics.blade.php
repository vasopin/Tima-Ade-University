@extends('layouts.app')

@section('title', 'Executive Analytics')
@section('breadcrumb') <li class="breadcrumb-item active">Analytics</li> @endsection

@section('content')
<div class="container-fluid px-0">
    <h1 class="h3 mb-1">Executive Analytics</h1>
    <p class="text-muted mb-4">Current university-wide indicators derived from authoritative records.</p>
    <div class="row g-3">
        @foreach([['Faculties', $faculties], ['Departments', $departments], ['Active programs', $programs], ['Students', $students], ['Active students', $activeStudents], ['Recorded assessments', $marks], ['Average mark', $averageMark . '%']] as [$label, $value])
            <div class="col-6 col-xl-3"><div class="card custom-card h-100"><div class="card-body"><div class="small text-muted">{{ $label }}</div><div class="h4 fw-bold mb-0">{{ $value }}</div></div></div></div>
        @endforeach
    </div>
</div>
@endsection
