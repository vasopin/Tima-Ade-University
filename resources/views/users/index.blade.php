@extends('layouts.app')

@section('title', 'User Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Users</li>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- Page Header & Stats -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: #1F2937;">User Accounts</h3>
            <p class="text-muted small mb-0">Manage system users, access credentials, account approval, and role assignments.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('users.create') }}" class="btn btn-primary shadow-sm" style="background-color: #016ED5; border-color: #016ED5;">
                <i class="bi bi-person-plus-fill me-1"></i> Add User
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 d-flex align-items-center justify-content-center text-white" style="background-color: #016ED5; width: 48px; height: 48px;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">TOTAL USERS</div>
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
                        <div class="text-muted small fw-semibold">ACTIVE ACCOUNTS</div>
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
                        <div class="text-muted small fw-semibold">PENDING APPROVAL</div>
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

    <!-- Filters & Table Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <!-- Filter Bar -->
            <form method="GET" action="{{ route('users.index') }}" class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-secondary"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search name, email, or phone..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role_id" class="form-select">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
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
                    @if(request()->hasAny(['search', 'role_id', 'status']))
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                    @endif
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $u->name }}</div>
                                            <div class="small text-muted">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-dark px-3 py-1 fw-semibold border">
                                        {{ $u->role->name ?? 'User' }}
                                    </span>
                                </td>
                                <td>{{ $u->phone ?? '—' }}</td>
                                <td>{!! $u->status_badge !!}</td>
                                <td class="small text-muted">{{ $u->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li><a class="dropdown-item" href="{{ route('users.show', $u) }}"><i class="bi bi-eye text-primary me-2"></i>View Details</a></li>
                                            <li><a class="dropdown-item" href="{{ route('users.edit', $u) }}"><i class="bi bi-pencil text-warning me-2"></i>Edit Account</a></li>
                                            <li><hr class="dropdown-divider"></li>

                                            @if($u->status === 'pending')
                                                <li>
                                                    <form method="POST" action="{{ route('users.approve', $u) }}">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-success"><i class="bi bi-check-circle me-2"></i>Approve Account</button>
                                                    </form>
                                                </li>
                                            @endif

                                            @if($u->status !== 'active')
                                                <li>
                                                    <form method="POST" action="{{ route('users.activate', $u) }}">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-success"><i class="bi bi-check2-circle me-2"></i>Set Active</button>
                                                    </form>
                                                </li>
                                            @endif

                                            @if($u->status === 'active')
                                                <li>
                                                    <form method="POST" action="{{ route('users.deactivate', $u) }}">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-secondary"><i class="bi bi-pause-circle me-2"></i>Deactivate</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form method="POST" action="{{ route('users.suspend', $u) }}">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-warning"><i class="bi bi-slash-circle me-2"></i>Suspend</button>
                                                    </form>
                                                </li>
                                            @endif

                                            @if($u->id !== auth()->id())
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('users.destroy', $u) }}" data-confirm="Are you sure you want to delete user '{{ $u->name }}'?">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete User</button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-people display-6 d-block mb-2"></i>
                                    No users found matching the criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
