@extends('layouts.app')
@section('title', 'Admissions Reports')
@section('breadcrumb')<li class="breadcrumb-item active">Reports</li>@endsection
@section('content')
<div class="container-fluid px-0">
    <div class="mb-4"><h3 class="page-title mb-1">Admissions Reports</h3><p class="text-muted small mb-0">Real-time reporting from the admissions register.</p></div>
    <div class="row g-3 mb-4">
        @foreach(['total'=>'Total Applications','applicants'=>'Total Applicants','pending_reviews'=>'Pending Reviews','completed_decisions'=>'Completed Decisions','documents_pending'=>'Documents Pending'] as $key => $label)
            <div class="col-6 col-xl"><div class="card custom-card h-100"><div class="card-body"><div class="text-muted small">{{ $label }}</div><div class="fs-3 fw-bold">{{ $stats[$key] }}</div></div></div></div>
        @endforeach
    </div>
    <div class="card custom-card mb-4"><div class="card-body"><form method="GET" action="{{ route('admin.reports') }}" class="row g-3">
        <div class="col-md-3"><label class="form-label" for="report-status">Application status</label><select id="report-status" name="status" class="form-select"><option value="">All statuses</option>@foreach(['submitted','under_review','approved','declined'] as $value)<option value="{{ $value }}" @selected($status === $value)>{{ ucfirst(str_replace('_', ' ', $value)) }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label" for="report-grade">Program / grade</label><input id="report-grade" type="search" name="grade" class="form-control" value="{{ $grade }}" placeholder="Any program or grade"></div>
        <div class="col-md-2"><label class="form-label" for="report-from">From</label><input id="report-from" type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"></div>
        <div class="col-md-2"><label class="form-label" for="report-to">To</label><input id="report-to" type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"></div>
        <div class="col-md-2 d-flex align-items-end"><button class="btn btn-dark w-100" type="submit"><i class="bi bi-funnel me-1"></i> Filter</button></div>
    </form></div></div>
    <div class="row g-4">
        <div class="col-lg-4"><div class="card custom-card h-100"><div class="card-body"><h5>Applications by status</h5>@forelse($byStatus as $row)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ ucfirst(str_replace('_', ' ', $row->status)) }}</span><strong>{{ $row->total }}</strong></div>@empty<p class="text-muted mb-0">No report data available.</p>@endforelse</div></div></div>
        <div class="col-lg-4"><div class="card custom-card h-100"><div class="card-body"><h5>Decisions by outcome</h5>@forelse($byDecision as $row)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ ucfirst($row->status) }}</span><strong>{{ $row->total }}</strong></div>@empty<p class="text-muted mb-0">No decisions recorded for the selected filters.</p>@endforelse</div></div></div>
        <div class="col-lg-4"><div class="card custom-card h-100"><div class="card-body"><h5>Applications by program / grade</h5>@forelse($byGrade as $row)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $row->grade_interested ?: 'Not provided' }}</span><strong>{{ $row->total }}</strong></div>@empty<p class="text-muted mb-0">No application data available.</p>@endforelse</div></div></div>
    </div>
    <div class="card custom-card mt-4"><div class="card-body"><h5>Detailed application report</h5><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Application</th><th>Applicant</th><th>Program / grade</th><th>Status</th><th>Submitted</th><th>Decision date</th></tr></thead><tbody>@forelse($applications as $application)<tr><td class="fw-semibold">{{ $application->reference }}</td><td>{{ $application->name }}<small class="d-block text-muted">{{ $application->email }}</small></td><td>{{ $application->grade_interested ?: 'Not provided' }}</td><td>{{ ucfirst(str_replace('_', ' ', $application->status)) }}</td><td>{{ $application->created_at->format('d M Y') }}</td><td>{{ $application->decided_at?->format('d M Y') ?? 'Pending' }}</td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-4">No applications match the selected report filters.</td></tr>@endforelse</tbody></table></div><div class="mt-3">{{ $applications->links() }}</div></div></div>
</div>
@endsection