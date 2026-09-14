@extends('layouts.app')

@section('title', 'Leave Types')

@section('content')
<div class="container-fluid px-4 py-4 bg-light min-vh-100"><div class="mb-4"><p class="text-uppercase text-muted small fw-semibold mb-1">HR workspace</p><h1 class="h2 mb-1">Leave Types</h1><p class="text-muted mb-0">Configure annual, sick, study, and other leave allowances.</p></div><section class="card border-0 shadow-sm"><div class="card-header bg-white border-bottom"><h2 class="h5 mb-0">Configured leave types</h2></div><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Name</th><th>Days allowed</th><th>Requests</th><th>Status</th></tr></thead><tbody>@forelse($leaveTypes as $type)<tr><td><strong>{{ $type->name }}</strong><span class="d-block small text-muted">{{ $type->description }}</span></td><td>{{ $type->days_allowed }}</td><td>{{ $type->leave_requests_count }}</td><td><span class="badge {{ $type->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $type->is_active ? 'Active' : 'Inactive' }}</span></td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-5">No leave types configured.</td></tr>@endforelse</tbody></table></div><div class="p-3">{{ $leaveTypes->links() }}</div></section></div>
@endsection
