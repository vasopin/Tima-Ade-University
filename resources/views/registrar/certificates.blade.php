@extends('layouts.app')

@section('title', 'Certificates')

@section('content')
<div class="container-fluid px-4 py-4 bg-light min-vh-100">
    <div class="mb-4">
        <p class="text-uppercase text-muted small fw-semibold mb-1">Registrar workspace</p>
        <h1 class="h2 mb-1">Certificates &amp; Verification</h1>
        <p class="text-muted mb-0">Issue diplomas and enrollment verification letters from approved academic records.</p>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center p-5">
            <i class="bi bi-award fs-1 text-muted" aria-hidden="true"></i>
            <h2 class="h5 mt-3">Certificate issuance is not configured</h2>
            <p class="text-muted mb-0">Official transcripts are available now. Diploma and verification-letter records are not present in the current schema.</p>
            <a href="{{ route('registrar.transcripts.index') }}" class="btn btn-outline-primary mt-3">Open transcripts</a>
        </div>
    </div>
</div>
@endsection
