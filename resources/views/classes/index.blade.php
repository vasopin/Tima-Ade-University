@extends('layouts.app')

@section('title', 'Classes & Sections')

@section('breadcrumb')
    <li class="breadcrumb-item active">Classes</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="page-header-title mb-1">Academic Classes & Sections</h3>
            <p class="text-muted small mb-0">Manage grade levels, sections, assigned class teachers, and subject allocations.</p>
        </div>
        <a href="{{ route('classes.create') }}" class="btn btn-crimson shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Add New Class
        </a>
    </div>

    <div class="row g-4">
        @forelse($classes as $class)
            <div class="col-lg-4 col-md-6">
                <div class="card custom-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="class-badge me-2">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0 fw-bold">{{ $class->name }}</h5>
                                <span class="small text-muted">Grade {{ $class->grade_level ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <span class="badge {{ $class->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $class->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">{{ $class->description ?? 'Standard academic curriculum batch.' }}</p>

                        <div class="d-flex justify-content-between py-2 border-bottom small">
                            <span class="text-muted"><i class="bi bi-person-badge me-1"></i>Class Teacher:</span>
                            <span class="fw-semibold">{{ $class->classTeacher?->user->name ?? 'Not Assigned' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom small">
                            <span class="text-muted"><i class="bi bi-people me-1"></i>Enrolled Students:</span>
                            <span class="fw-bold text-dark">{{ $class->students_count }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 small">
                            <span class="text-muted"><i class="bi bi-grid me-1"></i>Sections:</span>
                            <span>
                                @foreach($class->sections as $sec)
                                    <span class="badge bg-light text-dark border">{{ $sec->name }}</span>
                                @endforeach
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-light d-flex justify-content-between p-3">
                        <a href="{{ route('classes.show', $class) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye me-1"></i> View Details
                        </a>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('classes.edit', $class) }}" class="btn btn-outline-primary" title="Edit Class">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('classes.destroy', $class) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this class? All associated students and records will be affected.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Delete Class">
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
                    <i class="bi bi-building fs-1 text-secondary mb-2"></i>
                    <p class="text-muted mb-0">No classes found. Click "Add New Class" to create one.</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($classes->hasPages())
        <div class="mt-4">
            {{ $classes->links() }}
        </div>
    @endif
</div>
@endsection
