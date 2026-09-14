@extends('layouts.app')

@section('title', 'Parents & Guardians')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Parents</li>
@endsection

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: #1F2937;">Parents & Guardians</h3>
            <p class="text-muted small mb-0">Directory of student parents and legal guardians linked to scholars.</p>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
            <a href="{{ route('parents.create') }}" class="btn btn-primary shadow-sm" style="background-color: #016ED5; border-color: #016ED5;">
                <i class="bi bi-person-plus-fill me-1"></i> Add Parent/Guardian
            </a>
        @endif
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('parents.index') }}" class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-secondary"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search parent name, email, phone, occupation..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                </div>
                @if(request('search'))
                    <div class="col-md-2">
                        <a href="{{ route('parents.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Guardian</th>
                            <th>Relationship</th>
                            <th>Contact</th>
                            <th>Occupation</th>
                            <th>Linked Scholars</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parents as $p)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $p->user->avatar_url }}" alt="{{ $p->user->name }}" class="rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $p->user->name }}</div>
                                            <div class="small text-muted">{{ $p->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border px-2 py-1">{{ ucfirst($p->relationship ?? 'Parent') }}</span></td>
                                <td>{{ $p->user->phone ?? '—' }}</td>
                                <td>{{ $p->occupation ?? '—' }}</td>
                                <td>
                                    @forelse($p->students as $student)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1 mb-1">
                                            {{ $student->user->name ?? 'Student' }} ({{ $student->schoolClass->name ?? '' }})
                                        </span>
                                    @empty
                                        <span class="text-muted small">None linked</span>
                                    @endforelse
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('parents.show', $p) }}" class="btn btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                                        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                                            <a href="{{ route('parents.edit', $p) }}" class="btn btn-outline-warning text-dark" title="Edit"><i class="bi bi-pencil"></i></a>
                                        @endif
                                        @if(auth()->user()->isAdmin())
                                            <form method="POST" action="{{ route('parents.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Delete this parent record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-people display-6 d-block mb-2"></i>
                                    No parents or guardians found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $parents->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
