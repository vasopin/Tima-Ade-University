@extends('layouts.app')

@section('title', 'Admissions Officer Dashboard')

@section('content')
<div class="container-fluid px-4 py-6 bg-light min-vh-100">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="h2 mb-2">Admissions Officer Dashboard</h1>
            <p class="text-muted">Manage and review university admission applications</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4 g-3">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Applications</p>
                            <h3 class="mb-0">{{ $totalApplications }}</h3>
                        </div>
                        <span class="badge bg-primary">📋</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">New Applications</p>
                            <h3 class="mb-0 text-warning">{{ $newApplications }}</h3>
                        </div>
                        <span class="badge bg-warning">⏳</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Pending Reviews</p>
                            <h3 class="mb-0 text-success">{{ $pendingApplications }}</h3>
                        </div>
                        <span class="badge bg-success">✓</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Documents Pending</p>
                            <h3 class="mb-0 text-danger">{{ $documentsPending }}</h3>
                        </div>
                        <span class="badge bg-danger">✕</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><h5 class="mb-0">My Work</h5></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between border-bottom py-2"><span>Applications needing review</span><strong>{{ $myWork['needing_review']->count() }}</strong></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span>Documents awaiting verification</span><strong>{{ $myWork['documents_pending']->count() }}</strong></div>
                    <div class="d-flex justify-content-between py-2"><span>Decisions awaiting action</span><strong>{{ $myWork['decisions_pending']->count() }}</strong></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom"><h5 class="mb-0">Application Pipeline</h5></div>
                <div class="card-body">
                    <div class="row g-2 text-center">
                        @foreach(['submitted' => 'Submitted', 'under_review' => 'Under Review', 'approved' => 'Approved', 'declined' => 'Declined'] as $status => $label)
                            <div class="col-6"><a class="d-block border rounded p-2 text-decoration-none" href="{{ route('admin.applications', ['status' => $status]) }}"><strong class="d-block fs-4">{{ $statuses[$status] ?? 0 }}</strong><span class="small text-muted">{{ $label }}</span></a></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Status Breakdown -->
    <div class="row mb-4 g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Application Status Overview</h5>
                </div>
                <div class="card-body">
                    @if($statuses)
                        <table class="table table-sm table-borderless">
                            <tbody>
                                @foreach($statuses as $status => $count)
                                    <tr>
                                        <td>
                                            <span class="text-capitalize">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <strong>{{ $count }}</strong>
                                        </td>
                                        <td class="text-end" style="width: 40%;">
                                            <div class="progress" style="height: 20px;">
                                                @php
                                                    $percentage = $totalApplications > 0 ? ($count / $totalApplications) * 100 : 0;
                                                    $statusColor = match($status) {
                                                        'approved' => 'bg-success',
                                                        'declined' => 'bg-danger',
                                                        'submitted' => 'bg-warning',
                                                        'under_review' => 'bg-info',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <div class="progress-bar {{ $statusColor }}" role="progressbar"
                                                     style="width: {{ $percentage }}%;"
                                                     aria-valuenow="{{ $count }}" aria-valuemin="0"
                                                     aria-valuemax="{{ $totalApplications }}">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted mb-0">No application data available.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Applicants by Grade Interest</h5>
                </div>
                <div class="card-body">
                    @if($applicantsByGrade->isNotEmpty())
                        <table class="table table-sm table-borderless">
                            <tbody>
                                @foreach($applicantsByGrade as $item)
                                    <tr>
                                        <td>
                                            <span class="text-capitalize">{{ $item->grade_interested }}</span>
                                        </td>
                                        <td class="text-end">
                                            <strong>{{ $item->count }}</strong> applicant{{ $item->count !== 1 ? 's' : '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted mb-0">No grade data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Applications Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Applications</h5>
                    <a href="{{ route('admin.applications') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Reference</th>
                                <th>Applicant Name</th>
                                <th>Email</th>
                                <th>Grade Interested</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentApplications as $application)
                                <tr>
                                    <td class="px-4">
                                        <code class="text-primary">{{ $application->reference }}</code>
                                    </td>
                                    <td>{{ $application->name }}</td>
                                    <td>{{ $application->email }}</td>
                                    <td>{{ $application->grade_interested }}</td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'pending' => 'bg-secondary',
                                                'submitted' => 'bg-info',
                                                'under_review' => 'bg-warning text-dark',
                                                'approved' => 'bg-success',
                                                'declined' => 'bg-danger'
                                            ];
                                            $statusClass = $statusClasses[$application->status] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.applications.show', $application) }}"
                                           class="btn btn-sm btn-outline-primary">Review</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No applications found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
