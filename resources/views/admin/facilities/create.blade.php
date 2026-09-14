@extends('layouts.app')
@section('title', 'Add Facility')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('admin.facilities.index') }}">Facilities</a></li><li class="breadcrumb-item active" aria-current="page">Add</li>@endsection
@section('content')<div class="container-fluid px-0"><h1 class="h3 fw-bold mb-4">Add Facility</h1>@include('admin.facilities.form', ['facility' => null, 'action' => route('admin.facilities.store'), 'method' => 'POST'])</div>@endsection