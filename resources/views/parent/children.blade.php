@extends('layouts.app')

@section('title', 'My Children')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Parent Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">My Children</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-dark">My Children</h2>
            <p class="text-muted mb-0">View and manage the students linked to your parent account.</p>
        </div>
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
            <i class="bi bi-people-fill me-1"></i> {{ $children->count() }} linked child{{ $children->count() === 1 ? '' : 'ren' }}
        </span>
    </div>

    @if($children->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4">
            <i class="bi bi-mortarboard text-muted display-4 mb-3"></i>
            <h4 class="fw-bold text-dark mb-2">No Linked Students Found</h4>
            <p class="text-muted mb-3">Your parent account is active, but no student records have been linked yet.</p>
            <p class="small text-muted mb-0">Please contact the school administration to link your child.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($children as $child)
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $child->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($child->user->name) . '&background=016ED5&color=fff' }}" alt="{{ $child->user->name }}" class="rounded-circle shadow-sm border border-2 border-primary" style="width: 56px; height: 56px; object-fit: cover;">
                                    <div>
                                        <h5 class="fw-bold mb-1 text-dark">{{ $child->user->name }}</h5>
                                        <div class="small text-muted">
                                            {{ $child->schoolClass->name ?? 'Class' }} • {{ $child->section->name ?? 'Section' }}
                                        </div>
                                    </div>
                                </div>
                                <span class="badge {{ $child->status === 'active' ? 'bg-success' : 'bg-secondary' }} px-2 py-2 rounded-pill">{{ ucfirst($child->status ?? 'active') }}</span>
                            </div>

                            <div class="row small text-muted mb-3">
                                <div class="col-sm-6"><strong>Roll No:</strong> {{ $child->roll_number ?? 'N/A' }}</div>
                                <div class="col-sm-6"><strong>Admission:</strong> {{ $child->admission_number ?? 'N/A' }}</div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <a href="{{ route('parent.child', ['student' => $child->id]) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-arrow-right-circle me-1"></i> View Child
                                </a>
                                <a href="{{ route('attendance.index', ['student_id' => $child->id]) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-calendar-check me-1"></i> Attendance
                                </a>
                                <a href="{{ route('exams.results', ['student_id' => $child->id]) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-award me-1"></i> Results
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
