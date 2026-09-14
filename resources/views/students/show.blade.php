@extends('layouts.app')

@section('title', $student->user->name . ' — Profile')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">{{ $student->user->name }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Header with quick actions -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <img src="{{ $student->user->avatar_url }}" class="rounded-circle border shadow-sm me-3" width="64" height="64" alt="">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h3 class="page-header-title mb-0">{{ $student->user->name }}</h3>
                    {!! $student->status_badge !!}
                </div>
                <div class="text-muted small mt-1">
                    <span><i class="bi bi-credit-card-2-front me-1"></i>Official Student ID: <strong>{{ $student->student_id }}</strong></span> •
                    <span><i class="bi bi-person-badge me-1"></i>{{ $student->roll_number }}</span> • 
                    <span><i class="bi bi-hash me-1"></i>{{ $student->admission_number }}</span> • 
                    <span><i class="bi bi-building me-1"></i>{{ $student->schoolClass->name }} ({{ $student->section->name }})</span>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                <a href="{{ route('students.edit', $student) }}" class="btn btn-crimson btn-sm">
                    <i class="bi bi-pencil me-1"></i> Edit Profile
                </a>
            @endif
            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card custom-card text-center p-3">
                <span class="text-muted small">Attendance Rate</span>
                <h4 class="fw-bold text-success mt-1 mb-0">{{ $attendancePct }}%</h4>
                <small class="text-muted">{{ $attendanceSummary['present'] }} Present Days</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card custom-card text-center p-3">
                <span class="text-muted small">Days Absent</span>
                <h4 class="fw-bold text-danger mt-1 mb-0">{{ $attendanceSummary['absent'] }}</h4>
                <small class="text-muted">Unexcused Absences</small>
            </div>
        </div>
        @if($canViewFinancials)
        <div class="col-md-3 col-sm-6">
            <div class="card custom-card text-center p-3">
                <span class="text-muted small">Total Fees Paid</span>
                <h4 class="fw-bold text-primary mt-1 mb-0">${{ number_format($feesSummary['total_paid'], 2) }}</h4>
                <small class="text-muted">Cleared Transactions</small>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card custom-card text-center p-3">
                <span class="text-muted small">Pending Invoices</span>
                <h4 class="fw-bold text-warning mt-1 mb-0">{{ $feesSummary['total_pending'] }}</h4>
                <small class="text-muted">Requires Payment</small>
            </div>
        </div>
        @endif
    </div>

    <div class="row g-4">
        <!-- Student Details -->
        <div class="col-lg-4">
            <div class="card custom-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-person-lines-fill me-2 text-danger"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Email</span>
                            <span class="fw-semibold">{{ $student->user->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Phone</span>
                            <span class="fw-semibold">{{ $student->user->phone ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Gender</span>
                            <span class="fw-semibold text-capitalize">{{ $student->gender ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Date of Birth</span>
                            <span class="fw-semibold">{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Blood Group</span>
                            <span class="fw-semibold">{{ $student->blood_group ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Admission Date</span>
                            <span class="fw-semibold">{{ $student->admission_date ? $student->admission_date->format('M d, Y') : 'N/A' }}</span>
                        </li>
                        <li class="list-group-item px-0">
                            <span class="text-muted d-block mb-1">Address</span>
                            <span class="fw-semibold">{{ $student->address ?? 'N/A' }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Parent Info -->
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-people-fill me-2 text-success"></i>Guardian Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Guardian Name</span>
                            <span class="fw-semibold">{{ $student->parent_name ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Phone</span>
                            <span class="fw-semibold">{{ $student->parent_phone ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Email</span>
                            <span class="fw-semibold">{{ $student->parent_email ?? 'N/A' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Attendance & Fee History -->
        <div class="col-lg-8">
            @if($canViewFinancials)
            <!-- Fee Records -->
            <div class="card custom-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-cash-stack me-2 text-primary"></i>Fee Billing & Payment History</h5>
                    <a href="{{ route('fees.create') }}" class="btn btn-sm btn-outline-primary">Record Payment</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Receipt #</th>
                                <th>Fee Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->feePayments as $fee)
                                <tr>
                                    <td><code>{{ $fee->receipt_number }}</code></td>
                                    <td>{{ $fee->feeStructure->fee_type ?? 'N/A' }}</td>
                                    <td class="fw-bold">${{ number_format($fee->amount_paid, 2) }}</td>
                                    <td>{!! $fee->status_badge !!}</td>
                                    <td>{{ $fee->payment_date ? $fee->payment_date->format('M d, Y') : '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No billing records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Attendance Records -->
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-calendar-check me-2 text-warning"></i>Recent Attendance History</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->attendances->take(10) as $att)
                                <tr>
                                    <td>{{ $att->attendance_date ? $att->attendance_date->format('l, M d, Y') : '—' }}</td>
                                    <td>{!! $att->status_badge !!}</td>
                                    <td>{{ $att->remarks ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No attendance records found.</td>
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
