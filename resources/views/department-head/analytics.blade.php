@extends('layouts.app')
@section('title', 'Department Analytics')
@section('breadcrumb') <li class="breadcrumb-item active">Analytics</li> @endsection
@section('content')
<div class="container-fluid px-0"><h1 class="h3 mb-1">Department Analytics</h1><p class="text-muted mb-4">Current indicators for {{ $department->name }} only.</p><div class="row g-3">
@foreach([['Programs',$programs],['Courses',$courses],['Sections',$sections],['Students',$students],['Active enrollments',$enrollments],['Average mark',$averageMark.'%'],['Active advising assignments',$advising]] as [$label,$value])
<div class="col-6 col-xl-3"><div class="card custom-card h-100"><div class="card-body"><div class="small text-muted">{{ $label }}</div><div class="h4 fw-bold mb-0">{{ $value }}</div></div></div></div>
@endforeach</div></div>
@endsection
