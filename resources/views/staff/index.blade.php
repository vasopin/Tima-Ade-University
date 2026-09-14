@extends('layouts.app')

@section('title', 'Staff Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Staff Management</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: #1F2937;">Staff Management</h3>
            <p class="text-muted small mb-0">Manage university staff accounts, working status, and access assignments.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('staff.create') }}" class="btn btn-primary shadow-sm" style="background-color: #016ED5; border-color: #016ED5;">
                <i class="bi bi-person-plus-fill me-1"></i> Add Staff Member
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 d-flex align-items-center justify-content-center text-white" style="background-color: #016ED5; width: 48px; height: 48px;">
                        <i class="bi bi-briefcase-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">TOTAL STAFF</div>
                        <div class="fs-4 fw-bold text-dark">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 d-flex align-items-center justify-content-center bg-success text-white" style="width: 48px; height: 48px;">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">ACTIVE</div>
                        <div class="fs-4 fw-bold text-dark">{{ $stats['active'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 d-flex align-items-center justify-content-center text-dark" style="background-color: #FB8B01; width: 48px; height: 48px;">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">PENDING</div>
                        <div class="fs-4 fw-bold text-dark">{{ $stats['pending'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 d-flex align-items-center justify-content-center bg-danger text-white" style="width: 48px; height: 48px;">
                        <i class="bi bi-slash-circle fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">SUSPENDED</div>
                        <div class="fs-4 fw-bold text-dark">{{ $stats['suspended'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('staff.index') }}" class="row g-3 mb-4">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-secondary"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search staff by name, email, or phone..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Staff Member</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            <div class="small text-muted">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->phone ?? '—' }}</td>
                                <td>{!! $user->status_badge !!}</td>
                                <td class="small text-muted">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('staff.show', $user) }}" class="btn btn-sm btn-outline-primary" title="View staff profile"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('staff.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="Edit staff profile"><i class="bi bi-pencil-square"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No staff members found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="mt-4 d-flex justify-content-end">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
