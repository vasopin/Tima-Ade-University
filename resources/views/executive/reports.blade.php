@extends('layouts.app')

@section('title', 'Executive Reports')
@section('breadcrumb') <li class="breadcrumb-item active">Reports</li> @endsection

@section('content')
<div class="container-fluid px-0">
    <h1 class="h3 mb-1">Executive Reports</h1>
    <p class="text-muted mb-4">Read-only entry points into university-wide executive information.</p>
    <div class="row g-3">
        @foreach([
            ['Faculties', 'executive.faculties'], ['Departments', 'executive.departments'], ['Programs', 'executive.programs'],
            ['Students', 'executive.students'], ['Academic Performance', 'executive.academic-performance'],
            ['Analytics', 'executive.analytics'], ['Executive Dashboard', 'dashboard'],
        ] as [$label, $route])
            <div class="col-md-6 col-xl-4"><a class="card custom-card h-100 text-decoration-none" href="{{ route($route) }}"><div class="card-body"><h2 class="h5 text-dark mb-1">{{ $label }}</h2><span class="text-muted small">Open read-only view <i class="bi bi-arrow-right"></i></span></div></a></div>
        @endforeach
    </div>
</div>
@endsection
