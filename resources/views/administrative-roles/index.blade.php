@extends('layouts.app')

@section('title', $roleModel->name . ' Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $roleModel->name }} Management</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color:#1F2937;">{{ $roleModel->name }} Management</h3>
            <p class="text-muted small mb-0">Manage {{ strtolower($roleModel->name) }} accounts, status, and access.</p>
        </div>
        <a href="{{ route($role . '.create') }}" class="btn btn-primary" style="background-color:#016ED5;border-color:#016ED5;"><i class="bi bi-person-plus-fill me-1"></i> Add {{ $roleModel->name }}</a>
    </div>

    <div class="row g-3 mb-4">
        @foreach(['total' => 'TOTAL', 'active' => 'ACTIVE', 'pending' => 'PENDING', 'suspended' => 'SUSPENDED'] as $key => $label)
            <div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm rounded-4 p-3 bg-white"><div class="text-muted small fw-semibold">{{ $label }}</div><div class="fs-4 fw-bold text-dark">{{ $stats[$key] }}</div></div></div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm rounded-4"><div class="card-body p-4">
        <form method="GET" action="{{ route($role . '.index') }}" class="row g-3 mb-4">
            <div class="col-md-6"><input type="search" name="search" class="form-control" placeholder="Search name, email, or phone..." value="{{ request('search') }}"></div>
            <div class="col-md-3"><select name="status" class="form-select"><option value="">All Statuses</option>@foreach(['active','pending','inactive','suspended'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
            <div class="col-md-3"><button class="btn btn-dark w-100"><i class="bi bi-funnel me-1"></i> Filter</button></div>
        </form>
        <div class="table-responsive"><table class="table align-middle"><thead><tr><th>User</th><th>Role</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        @forelse($users as $user)
            <tr><td><strong>{{ $user->name }}</strong><br><small class="text-muted">{{ $user->email }}</small></td><td>{{ $roleModel->name }}</td><td>{{ $user->phone ?: '—' }}</td><td>{{ ucfirst($user->status ?: 'active') }}</td><td><a href="{{ route($role . '.show', [$role => $role, 'user' => $user]) }}" class="btn btn-sm btn-outline-secondary">View</a> <a href="{{ route($role . '.edit', [$role => $role, 'user' => $user]) }}" class="btn btn-sm btn-outline-primary">Edit</a></td></tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No {{ strtolower($roleModel->name) }} accounts found.</td></tr>
        @endforelse
        </tbody></table></div>
        {{ $users->links() }}
    </div></div>
</div>
@endsection