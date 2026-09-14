@extends('layouts.app')
@section('title', 'Department Reports')
@section('breadcrumb') <li class="breadcrumb-item active">Reports</li> @endsection
@section('content')
<div class="container-fluid px-0"><h1 class="h3 mb-1">Department Reports</h1><p class="text-muted mb-4">Read-only reports restricted to {{ $department->name }}.</p><div class="row g-3">
@foreach([['Programs','department-head.programs'],['Courses','department-head.courses'],['Course Sections','department-head.sections'],['Students','department-head.students'],['Instructors','department-head.instructors'],['Academic Performance','department-head.academic-performance'],['Advising','department-head.advising'],['Analytics','department-head.analytics']] as [$label,$route])
<div class="col-md-6 col-xl-4"><a class="card custom-card h-100 text-decoration-none" href="{{ route($route) }}"><div class="card-body"><h2 class="h5 text-dark mb-1">{{ $label }}</h2><span class="text-muted small">Open read-only view <i class="bi bi-arrow-right"></i></span></div></a></div>
@endforeach</div></div>
@endsection
