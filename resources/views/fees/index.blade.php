@extends('layouts.app')

@section('title', 'Fees & Billing')

@section('breadcrumb')
    <li class="breadcrumb-item active">Fees</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="page-header-title mb-1">Fee Management & Billing</h3>
            <p class="text-muted small mb-0">Record payments, track invoices, configure fee tiers, and print transaction receipts.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('fees.create') }}" class="btn btn-crimson shadow-sm">
                <i class="bi bi-cash-stack me-1"></i> Record Payment
            </a>
            <a href="{{ route('fees.structures') }}" class="btn btn-navy shadow-sm">
                <i class="bi bi-gear me-1"></i> Fee Structures
            </a>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card custom-card p-3">
                <div class="d-flex align-items-center">
                    <div class="stat-icon-wrapper icon-fees me-3">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div>
                        <span class="text-muted small">Total Fees Collected</span>
                        <h4 class="fw-bold text-success mb-0">${{ number_format($summary['total_collected'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card custom-card p-3">
                <div class="d-flex align-items-center">
                    <div class="stat-icon-wrapper icon-students me-3" style="background: rgba(234, 179, 8, 0.15); color: #ca8a04;">
                        <i class="bi bi-hourglass-top"></i>
                    </div>
                    <div>
                        <span class="text-muted small">Pending Invoices</span>
                        <h4 class="fw-bold text-warning mb-0">{{ $summary['total_pending'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card custom-card p-3">
                <div class="d-flex align-items-center">
                    <div class="stat-icon-wrapper icon-teachers me-3" style="background: rgba(239, 68, 68, 0.15); color: #dc2626;">
                        <i class="bi bi-exclamation-octagon"></i>
                    </div>
                    <div>
                        <span class="text-muted small">Overdue Invoices</span>
                        <h4 class="fw-bold text-danger mb-0">{{ $summary['total_overdue'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card custom-card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('fees.index') }}" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by receipt number, student name..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Payment Statuses</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid (Full)</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-navy flex-grow-1">Filter</button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('fees.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card custom-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Receipt #</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Fee Type</th>
                        <th>Amount Paid</th>
                        <th>Payment Method</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td><code>{{ $payment->receipt_number }}</code></td>
                            <td>
                                <a href="{{ route('students.show', $payment->student) }}" class="fw-semibold text-dark text-decoration-none">
                                    {{ $payment->student->user->name ?? 'N/A' }}
                                </a>
                                <div class="small text-muted">{{ $payment->student->admission_number ?? '' }}</div>
                            </td>
                            <td>{{ $payment->student->schoolClass->name ?? 'N/A' }}</td>
                            <td>{{ $payment->feeStructure->fee_type ?? 'N/A' }}</td>
                            <td class="fw-bold text-success">${{ number_format($payment->amount_paid, 2) }}</td>
                            <td class="text-capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                            <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : '—' }}</td>
                            <td>{!! $payment->status_badge !!}</td>
                            <td class="text-end">
                                <a href="{{ route('fees.show', $payment) }}" class="btn btn-sm btn-outline-secondary" title="View & Print Receipt">
                                    <i class="bi bi-receipt"></i> Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-cash-stack fs-1 text-secondary d-block mb-2"></i>
                                No fee payments recorded. Click "Record Payment" to process one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
