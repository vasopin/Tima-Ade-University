@extends('layouts.app')
@section('title', 'Edit Facility')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('admin.facilities.index') }}">Facilities</a></li><li class="breadcrumb-item active" aria-current="page">Edit</li>@endsection
@section('content')<div class="container-fluid px-0"><h1 class="h3 fw-bold mb-4">Edit Facility</h1>@include('admin.facilities.form', ['action' => route('admin.facilities.update', $facility), 'method' => 'PUT'])</div>@endsection