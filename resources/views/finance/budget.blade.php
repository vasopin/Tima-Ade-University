@extends('layouts.app')

@section('title', 'Budget Tracking')

@section('content')
<div class="container-fluid px-4 py-4 bg-light min-vh-100">
    <div class="mb-4"><p class="text-uppercase text-muted small fw-semibold mb-1">Finance workspace</p><h1 class="h2 mb-1">Budget Tracking</h1><p class="text-muted mb-0">Departmental allocation and spending oversight.</p></div>
    <div class="card border-0 shadow-sm"><div class="card-body text-center p-5"><i class="bi bi-pie-chart fs-1 text-muted" aria-hidden="true"></i><h2 class="h5 mt-3">No budget records available</h2><p class="text-muted mb-0">Budget allocations and spending will appear here when the finance budget records are configured.</p></div></div>
</div>
@endsection
