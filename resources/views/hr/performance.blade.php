@extends('layouts.app')

@section('title', 'Performance Reviews')

@section('content')
<div class="container-fluid px-4 py-4 bg-light min-vh-100"><div class="mb-4"><p class="text-uppercase text-muted small fw-semibold mb-1">HR workspace</p><h1 class="h2 mb-1">Performance Reviews</h1><p class="text-muted mb-0">Track annual reviews, probation assessments, and appraisal schedules.</p></div><div class="card border-0 shadow-sm"><div class="card-body text-center p-5"><i class="bi bi-graph-up fs-1 text-muted" aria-hidden="true"></i><h2 class="h5 mt-3">Performance records are not configured</h2><p class="text-muted mb-0">Performance review, probation, and appraisal records are not present in the current HR schema.</p><a href="{{ route('hr.employees.index') }}" class="btn btn-outline-primary mt-3">Open employee records</a></div></div></div>
@endsection
