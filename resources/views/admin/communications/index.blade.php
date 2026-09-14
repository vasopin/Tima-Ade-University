@extends('layouts.app')
@section('title', 'Applicant Communications')
@section('breadcrumb')<li class="breadcrumb-item">Admissions</li><li class="breadcrumb-item active" aria-current="page">Communications</li>@endsection
@section('content')
<div class="container-fluid px-0">
    <div class="mb-4"><h1 class="h3 fw-bold mb-1">Applicant Communications</h1><p class="text-muted mb-0">Send targeted enrollment guidance and missing-document reminders to applicants.</p></div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
    <div class="card custom-card mb-4"><div class="card-body"><form method="GET" class="row g-2 align-items-end"><div class="col-md-6"><label class="form-label" for="search">Find applicant</label><input class="form-control" id="search" type="search" name="search" value="{{ request('search') }}" placeholder="Name, email, or reference"></div><div class="col-md-2"><button class="btn btn-dark w-100" type="submit"><i class="bi bi-search me-1"></i>Search</button></div></form></div></div>
    <div class="row g-4">
        @forelse($applications as $application)
            <div class="col-lg-6"><div class="card custom-card h-100"><div class="card-body"><div class="d-flex justify-content-between gap-3 mb-3"><div><h2 class="h5 fw-bold mb-1">{{ $application->name }}</h2><div class="small text-muted">{{ $application->email }} · {{ $application->reference }}</div></div><span class="badge text-bg-light text-capitalize">{{ str_replace('_', ' ', $application->status) }}</span></div><form method="POST" action="{{ route('admin.applications.communicate', $application) }}" class="vstack gap-3">@csrf<label class="form-label mb-0" for="subject-{{ $application->id }}">Subject</label><input class="form-control" id="subject-{{ $application->id }}" name="subject" value="Tima-Ade University Admissions Update" required maxlength="255"><label class="form-label mb-0" for="message-{{ $application->id }}">Message</label><textarea class="form-control" id="message-{{ $application->id }}" name="message" rows="4" required maxlength="5000">Dear {{ $application->name }},

Please contact the Admissions Office if you need assistance with your application.</textarea><button class="btn btn-primary align-self-start" type="submit"><i class="bi bi-send me-1"></i>Send email</button></form></div></div></div>
        @empty
            <div class="col-12"><div class="card custom-card"><div class="card-body text-muted">No applicants match the current search.</div></div></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $applications->links() }}</div>
</div>
@endsection
