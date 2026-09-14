@extends('layouts.app')

@section('title', 'Subjects')

@section('breadcrumb')
    <li class="breadcrumb-item active">Subjects</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="page-header-title mb-1">Academic Curriculum Subjects</h3>
            <p class="text-muted small mb-0">Define curriculum courses, subject codes, and class associations.</p>
        </div>
        <a href="{{ route('subjects.create') }}" class="btn btn-crimson shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Add New Subject
        </a>
    </div>

    <div class="card custom-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Subject Name</th>
                        <th>Subject Code</th>
                        <th>Description</th>
                        <th>Classes Mapped</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="subject-icon me-3">
                                        <i class="bi bi-book-half"></i>
                                    </div>
                                    <div class="fw-bold text-dark">{{ $subject->name }}</div>
                                </div>
                            </td>
                            <td><span class="badge bg-secondary-subtle text-dark border">{{ $subject->code }}</span></td>
                            <td class="text-muted small">{{ $subject->description ?? '—' }}</td>
                            <td><span class="badge bg-primary-subtle text-primary border">{{ $subject->classes_count }} Classes</span></td>
                            <td>
                                <span class="badge {{ $subject->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('subjects.destroy', $subject) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this subject?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No subjects found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subjects->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $subjects->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
