@extends('layouts.app')

@section('title', 'Audit Logs')
@section('breadcrumb')
    <li class="breadcrumb-item">Administration</li>
    <li class="breadcrumb-item active" aria-current="page">Audit Logs</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div><h1 class="h3 fw-bold mb-1">System Audit Logs</h1><p class="text-muted mb-0">Review recorded administrative activity and classroom changes.</p></div>
        <a href="{{ route('reports.academic') }}" class="btn btn-outline-primary"><i class="bi bi-bar-chart me-1"></i> Academic report</a>
    </div>
    <div class="card custom-card mb-4"><div class="card-body"><form method="GET" class="row g-2 align-items-end"><div class="col-md-5"><label class="form-label" for="action">Activity type</label><select class="form-select" id="action" name="action"><option value="">All recorded activity</option>@foreach($actions as $action)<option value="{{ $action }}" @selected(request('action') === $action)>{{ ucfirst(str_replace('_', ' ', $action)) }}</option>@endforeach</select></div><div class="col-md-2"><button class="btn btn-navy w-100" type="submit"><i class="bi bi-filter me-1"></i>Filter</button></div></form></div></div>
    <div class="card custom-card"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th scope="col">When</th><th scope="col">Actor</th><th scope="col">Action</th><th scope="col">Details</th></tr></thead><tbody>@forelse($activities as $activity)<tr><td class="text-nowrap">{{ $activity->created_at->format('M j, Y g:i A') }}</td><td>{{ $activity->actor?->name ?? 'System' }}</td><td><span class="badge bg-light text-dark text-capitalize">{{ str_replace('_', ' ', $activity->action) }}</span></td><td>{{ $activity->details ?: 'No details recorded' }}</td></tr>@empty<tr><td colspan="4" class="text-center py-4 text-muted">No audit activity has been recorded yet.</td></tr>@endforelse</tbody></table></div>@if($activities->hasPages())<div class="card-footer bg-white">{{ $activities->links() }}</div>@endif</div>
</div>
@endsection