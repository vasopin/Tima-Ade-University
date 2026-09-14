@extends('layouts.app')

@section('title', 'RBAC & User Roles')

@section('breadcrumb')
    <li class="breadcrumb-item">Super Admin</li>
    <li class="breadcrumb-item active" aria-current="page">RBAC &amp; User Roles</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">RBAC &amp; User Roles</h1>
            <p class="text-muted mb-0">Review the authoritative roles used by server-side dashboard and feature authorization.</p>
        </div>
        <a class="btn btn-outline-primary" href="{{ route('users.index') }}">
            <i class="bi bi-people me-1"></i>Manage User Accounts
        </a>
    </div>

    <div class="alert alert-info border-0 shadow-sm">
        <strong>Authorization source:</strong>
        User accounts are managed separately in User Accounts and reference one authoritative Role.
        This installation does not contain a separate permission table or permission package; authorization is enforced by the existing role checks and scoped controller rules.
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3">
            <h2 class="h5 fw-bold mb-0">Role definitions</h2>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Role</th>
                        <th>Description</th>
                        <th>Assigned users</th>
                        <th class="text-end">Account view</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $role->name }}</div>
                                <code>{{ $role->slug }}</code>
                            </td>
                            <td class="text-muted">{{ $role->description ?: 'No description recorded.' }}</td>
                            <td>{{ $role->users_count }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('users.index', ['role_id' => $role->id]) }}">
                                    View accounts
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
