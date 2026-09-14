@extends('layouts.app')

@section('title', 'Admission Decisions')

@section('breadcrumb')
    <li class="breadcrumb-item active">Admission Decisions</li>
@endsection

@section('content')
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="page-title mb-1">Admission Decisions</h3>
                <p class="text-muted small mb-0">Manage outcomes for existing admissions applications.</p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            @foreach([
                'total' => 'Total Decisions',
                'pending' => 'Pending Decisions',
                'approved' => 'Approved',
                'declined' => 'Rejected',
                'requiring_action' => 'Decisions Requiring Action',
            ] as $key => $label)
                <div class="col-6 col-lg-3">
                    <div class="card custom-card h-100">
                        <div class="card-body">
                            <span class="text-muted small d-block">{{ $label }}</span>
                            <strong class="fs-3 text-dark">{{ $decisionStats[$key] ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card custom-card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.decisions') }}" class="row g-3">
                    <div class="col-lg-4">
                        <label class="form-label" for="decision-search">Search decisions</label>
                        <input id="decision-search" type="search" name="search" class="form-control"
                               value="{{ request('search') }}" placeholder="Applicant, application, program, or decision maker">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" for="decision-status">Decision status</label>
                        <select id="decision-status" name="status" class="form-select">
                            <option value="">All statuses</option>
                            @foreach(['under_review' => 'Pending', 'approved' => 'Approved', 'declined' => 'Rejected'] as $value => $label)
                                <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" for="decision-from">Decision date from</label>
                        <input id="decision-from" type="date" name="date_from" class="form-control"
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" for="decision-to">Decision date to</label>
                        <input id="decision-to" type="date" name="date_to" class="form-control"
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3 col-lg-2 d-flex align-items-end">
                        <button class="btn btn-dark w-100" type="submit">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card custom-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Application number</th>
                            <th>Program</th>
                            <th>Application status</th>
                            <th>Decision date</th>
                            <th>Decision maker</th>
                            <th>Decision reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $application->name }}</span>
                                    <small class="d-block text-muted">{{ $application->email }}</small>
                                </td>
                                <td>{{ $application->reference }}</td>
                                <td>{{ $application->grade_interested }}</td>
                                <td>
                                    <span class="badge text-bg-{{ $application->status === 'approved' ? 'success' : ($application->status === 'declined' ? 'danger' : 'warning') }}">
                                        {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                    </span>
                                </td>
                                <td>{{ $application->decided_at?->format('d M Y') ?? 'Pending' }}</td>
                                <td>{{ $application->decider?->name ?? 'Not assigned' }}</td>
                                <td>{{ $application->decision_reason ?: 'None recorded' }}</td>
                                <td>
                                    <a href="{{ route('admin.applications.show', $application) }}" class="btn btn-sm btn-outline-primary">Open decision</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    No admission decisions match the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $applications->links() }}</div>
        </div>
    </div>
@endsection
