@extends('layouts.app')

@section('title', 'Fee Collection Reports')

@section('breadcrumb')
    <li class="breadcrumb-item">Reports</li>
    <li class="breadcrumb-item active">Fee Report</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title mb-1">Fee Collections & Financial Reports</h3>
            <p class="text-muted small mb-0">Track fee collections, pending dues, payment modes, and revenue breakdown.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.fees.export', request()->all()) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-1"></i> Print Financial Summary
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card custom-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.fees') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClass == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Paid Only</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending Only</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">From Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">To Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-navy w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary KPI cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card custom-card p-3 bg-success-subtle border-success text-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small fw-semibold text-uppercase">Total Collected</span>
                        <h3 class="fw-bold mb-0">${{ number_format($totalAmount, 2) }}</h3>
                    </div>
                    <i class="bi bi-cash-coin fs-1"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card custom-card p-3 bg-danger-subtle border-danger text-danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small fw-semibold text-uppercase">Pending Invoices</span>
                        <h3 class="fw-bold mb-0">{{ $pendingCount }} Invoices</h3>
                    </div>
                    <i class="bi bi-hourglass-split fs-1"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card custom-card">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0"><i class="bi bi-receipt me-2 text-primary"></i>Fee Transactions ({{ count($payments) }} Records)</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Receipt #</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Fee Type</th>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                        <tr>
                            <td><code>{{ $p->receipt_number }}</code></td>
                            <td>
                                <div class="fw-semibold">{{ $p->student->user->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $p->student->admission_number ?? '' }}</small>
                            </td>
                            <td>{{ $p->student->schoolClass->name ?? 'N/A' }}</td>
                            <td>{{ $p->feeStructure->fee_type ?? 'N/A' }}</td>
                            <td>{{ $p->payment_date ? $p->payment_date->format('M d, Y') : '—' }}</td>
                            <td class="fw-bold text-dark">${{ number_format($p->amount_paid, 2) }}</td>
                            <td><span class="badge bg-light text-dark text-capitalize">{{ $p->payment_method ?? 'Cash' }}</span></td>
                            <td>{!! $p->status_badge !!}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No fee transactions found for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
