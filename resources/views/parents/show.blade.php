@extends('layouts.app')

@section('title', 'Parent Profile')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('parents.index') }}">Parents</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $parent->user->name }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="row g-4">
        <!-- Guardian Profile Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header border-0 text-center py-4" style="background: linear-gradient(135deg, #016ED5, #014B92); color: white;">
                    <img src="{{ $parent->user->avatar_url }}" alt="{{ $parent->user->name }}" class="rounded-circle shadow border border-3 border-white mb-2" style="width: 90px; height: 90px; object-fit: cover;">
                    <h4 class="fw-bold mb-1">{{ $parent->user->name }}</h4>
                    <span class="badge rounded-pill bg-light text-dark px-3 py-1 fw-semibold">
                        {{ ucfirst($parent->relationship ?? 'Parent') }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted"><i class="bi bi-envelope me-2 text-primary"></i>Email</span>
                            <span class="fw-semibold">{{ $parent->user->email }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted"><i class="bi bi-telephone me-2 text-success"></i>Phone</span>
                            <span class="fw-semibold">{{ $parent->user->phone ?? 'Not Set' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted"><i class="bi bi-briefcase me-2 text-warning"></i>Occupation</span>
                            <span class="fw-semibold">{{ $parent->occupation ?? '—' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted"><i class="bi bi-card-text me-2 text-info"></i>National ID</span>
                            <span class="fw-semibold">{{ $parent->national_id ?? '—' }}</span>
                        </li>
                    </ul>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="{{ route('parents.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
                        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                            <a href="{{ route('parents.edit', $parent) }}" class="btn btn-warning btn-sm text-dark fw-semibold" style="background-color: #FB8B01; border-color: #FB8B01;">Edit Details</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Linked Children Cards -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-mortarboard-fill me-2 text-primary"></i>Linked Scholars ({{ $parent->students->count() }})</h5>
                </div>
                <div class="card-body p-4">
                    @forelse($parent->students as $student)
                        <div class="card border rounded-3 p-3 mb-3 bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $student->user->avatar_url }}" alt="{{ $student->user->name }}" class="rounded-circle shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">{{ $student->user->name }}</h6>
                                            <div class="small text-muted">
                                                Roll: <strong>{{ $student->roll_number }}</strong> | Class: <strong>{{ $student->schoolClass->name ?? 'N/A' }}</strong> ({{ $student->section->name ?? 'N/A' }})
                                            </div>
                                            <div class="small text-muted">Adm No: {{ $student->admission_number }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 text-md-end mt-2 mt-md-0">
                                    <span class="badge bg-success me-2">{!! $student->status_badge !!}</span>
                                    <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-primary" style="background-color: #016ED5; border-color: #016ED5;">
                                        <i class="bi bi-file-person me-1"></i> Scholar Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-mortarboard display-6 d-block mb-2"></i>
                            No students are linked to this parent profile yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
