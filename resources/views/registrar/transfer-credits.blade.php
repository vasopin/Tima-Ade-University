@extends('layouts.app')

@section('title', 'Transfer Credits')

@section('content')
<div class="container-fluid px-4 py-4 bg-light min-vh-100">
    <div class="mb-4">
        <p class="text-uppercase text-muted small fw-semibold mb-1">Registrar workspace</p>
        <h1 class="h2 mb-1">Transfer Credits &amp; Equivalencies</h1>
        <p class="text-muted mb-0">Evaluate external coursework and record approved equivalencies.</p>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center p-5">
            <i class="bi bi-arrow-left-right fs-1 text-muted" aria-hidden="true"></i>
            <h2 class="h5 mt-3">No transfer-credit records available</h2>
            <p class="text-muted mb-0">Transfer-credit and equivalency records are not present in the current academic schema.</p>
        </div>
    </div>
</div>
@endsection
