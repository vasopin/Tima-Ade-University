@extends('layouts.app')

@section('title', 'Teachers')

@section('breadcrumb')
    <li class="breadcrumb-item active">Teachers</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="page-header-title mb-1">Faculty & Teachers</h3>
            <p class="text-muted small mb-0">Manage teaching staff records, specializations, and class leadership assignments.</p>
        </div>
        <a href="{{ route('teachers.create') }}" class="btn btn-crimson shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Faculty Member
        </a>
    </div>

    <!-- Search Card -->
    <div class="card custom-card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('teachers.index') }}" class="row g-2 align-items-center">
                <div class="col-md-9">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by faculty name, employee ID, specialization..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-navy flex-grow-1">Search</button>
                    @if(request()->has('search'))
                        <a href="{{ route('teachers.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Search"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Teachers Grid -->
    <div class="row g-4">
        @forelse($teachers as $teacher)
            <div class="col-lg-4 col-md-6">
                <div class="card custom-card teacher-card h-100">
                    <div class="card-body p-4 text-center">
                        <img src="{{ $teacher->user->avatar_url }}" class="rounded-circle border shadow-sm mb-3" width="72" height="72" alt="">
                        <h5 class="fw-bold mb-1 text-dark">{{ $teacher->user->name }}</h5>
                        <div class="text-muted small mb-2"><i class="bi bi-envelope me-1"></i>{{ $teacher->user->email }}</div>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle mb-3">
                            {{ $teacher->employee_id }}
                        </span>

                        <div class="border-top pt-3 text-start small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Qualification:</span>
                                <span class="fw-semibold text-end">{{ $teacher->qualification ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Specialization:</span>
                                <span class="fw-semibold text-end">{{ $teacher->specialization ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Class Teacher:</span>
                                <span class="fw-semibold text-end text-success">
                                    {{ $teacher->classTeacherOf ? $teacher->classTeacherOf->name : 'None' }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Phone:</span>
                                <span class="fw-semibold">{{ $teacher->user->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top-0 d-flex justify-content-between p-3">
                        <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-sm btn-outline-secondary">View Profile</a>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline" data-confirm="Delete this teacher?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card custom-card text-center py-5">
                    <i class="bi bi-mortarboard fs-1 text-secondary mb-2"></i>
                    <p class="text-muted mb-0">No faculty members found. Click "Add Faculty Member" to add one.</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($teachers->hasPages())
        <div class="mt-4">
            {{ $teachers->links() }}
        </div>
    @endif
</div>
@endsection
