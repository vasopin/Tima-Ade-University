@extends('layouts.app')

@section('title', 'HR Officer Dashboard')

@section('content')
<div class="container-fluid px-4 py-6 bg-light min-vh-100">
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="h2 mb-2">HR Officer Dashboard</h1>
            <p class="text-muted">Manage employees, leave, and payroll</p>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Employees</p>
                            <h3 class="mb-0">{{ $stats['total_employees'] }}</h3>
                        </div>
                        <span class="badge bg-primary">👔</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Pending Leave Requests</p>
                            <h3 class="mb-0 text-warning">{{ $stats['pending_leave_requests'] }}</h3>
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
                            <p class="text-muted small mb-1">Approved Leave Requests</p>
                            <h3 class="mb-0 text-success">{{ $stats['approved_leave_requests'] }}</h3>
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
                            <p class="text-muted small mb-1">Leave Types</p>
                            <h3 class="mb-0">{{ $stats['leave_types'] }}</h3>
                        </div>
                        <span class="badge bg-info">📋</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Recent Leave Requests</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Employee</th>
                                <th>Leave Type</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Days</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLeaveRequests as $request)
                                <tr>
                                    <td class="px-4">{{ $request->employee->user->name ?? 'N/A' }}</td>
                                    <td>{{ $request->leaveType->name ?? 'N/A' }}</td>
                                    <td>{{ $request->from_date->format('M d, Y') }}</td>
                                    <td>{{ $request->to_date->format('M d, Y') }}</td>
                                    <td>{{ $request->number_of_days }}</td>
                                    <td>
                                        <span class="badge
                                            @if($request->status === 'approved') bg-success
                                            @elseif($request->status === 'rejected') bg-danger
                                            @elseif($request->status === 'pending') bg-warning text-dark
                                            @else bg-secondary
                                            @endif">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No leave requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12 col-md-6 col-xl-3"><a href="{{ route('hr.employees.index') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-body"><div class="card-body"><i class="bi bi-people text-primary fs-4" aria-hidden="true"></i><h2 class="h6 mt-3">Employee records</h2><p class="small text-muted mb-0">Review staff profiles, contracts, departments, and payroll links.</p></div></a></div>
        <div class="col-12 col-md-6 col-xl-3"><a href="{{ route('hr.leave-requests.index') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-body"><div class="card-body"><i class="bi bi-calendar2-check text-warning fs-4" aria-hidden="true"></i><h2 class="h6 mt-3">Leave management</h2><p class="small text-muted mb-0">Approve or reject pending staff leave requests.</p></div></a></div>
        <div class="col-12 col-md-6 col-xl-3"><a href="{{ route('hr.attendance') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-body"><div class="card-body"><i class="bi bi-clock-history text-info fs-4" aria-hidden="true"></i><h2 class="h6 mt-3">Attendance</h2><p class="small text-muted mb-0">Review staff attendance and availability.</p></div></a></div>
        <div class="col-12 col-md-6 col-xl-3"><a href="{{ route('hr.performance') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-body"><div class="card-body"><i class="bi bi-graph-up text-success fs-4" aria-hidden="true"></i><h2 class="h6 mt-3">Performance reviews</h2><p class="small text-muted mb-0">Open the staff appraisal workspace.</p></div></a></div>
    </div>
</div>
@endsection
