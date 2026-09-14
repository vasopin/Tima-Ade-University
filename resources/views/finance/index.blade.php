@extends('layouts.app')

@section('title', 'Finance Overview')

@section('content')
<div class="container-fluid px-4 py-4 bg-light min-vh-100">
    <div class="mb-4">
        <p class="text-uppercase text-muted small fw-semibold mb-1">Finance workspace</p>
        <h1 class="h2 mb-1">Finance Overview</h1>
        <p class="text-muted mb-0">Manage current fee, payment, payroll, and scholarship records.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><span class="small text-muted">Fees collected</span><h2 class="h4 mt-2 mb-0">₦{{ number_format($stats['collected'], 2) }}</h2></div></div></div>
        <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><span class="small text-muted">Outstanding balances</span><h2 class="h4 mt-2 mb-0">₦{{ number_format($stats['outstanding'], 2) }}</h2></div></div></div>
        <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><span class="small text-muted">Active fee structures</span><h2 class="h4 mt-2 mb-0">{{ $stats['structures'] }}</h2></div></div></div>
        <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><span class="small text-muted">Payroll awaiting approval</span><h2 class="h4 mt-2 mb-0">{{ $stats['payroll_pending'] }}</h2></div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <section class="card border-0 shadow-sm" aria-labelledby="finance-actions-heading">
                <div class="card-header bg-white border-bottom"><h2 id="finance-actions-heading" class="h5 mb-0">Finance operations</h2></div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('fees.structures') }}" class="list-group-item list-group-item-action p-3"><i class="bi bi-receipt text-primary me-2" aria-hidden="true"></i><strong>Fee management</strong><span class="d-block small text-muted ms-4">Tuition structures, course fees, and due dates</span></a>
                    <a href="{{ route('fees.index') }}" class="list-group-item list-group-item-action p-3"><i class="bi bi-cash-stack text-success me-2" aria-hidden="true"></i><strong>Student billing and payments</strong><span class="d-block small text-muted ms-4">Balances, payment status, receipts, and transaction records</span></a>
                    <a href="{{ route('hr.payroll-records.index') }}" class="list-group-item list-group-item-action p-3"><i class="bi bi-wallet2 text-warning me-2" aria-hidden="true"></i><strong>Staff payroll</strong><span class="d-block small text-muted ms-4">Generate, process, and mark payroll records paid</span></a>
                    <a href="{{ route('reports.fees') }}" class="list-group-item list-group-item-action p-3"><i class="bi bi-bar-chart text-info me-2" aria-hidden="true"></i><strong>Financial reports</strong><span class="d-block small text-muted ms-4">Review fee revenue and export existing payment reports</span></a>
                    <a href="{{ route('admin.scholarships.index') }}" class="list-group-item list-group-item-action p-3"><i class="bi bi-award text-danger me-2" aria-hidden="true"></i><strong>Scholarships</strong><span class="d-block small text-muted ms-4">Manage active scholarship allocations in the existing system</span></a>
                    <a href="{{ route('finance.audit') }}" class="list-group-item list-group-item-action p-3"><i class="bi bi-journal-text text-secondary me-2" aria-hidden="true"></i><strong>Audit history</strong><span class="d-block small text-muted ms-4">Review recorded payment transactions and operators</span></a>
                </div>
            </section>
        </div>
        <div class="col-12 col-xl-4">
            <section class="card border-0 shadow-sm mb-4" aria-labelledby="recent-payments-heading">
                <div class="card-header bg-white border-bottom"><h2 id="recent-payments-heading" class="h5 mb-0">Recent payments</h2></div>
                <div class="card-body p-0">
                    @forelse($recentPayments as $payment)
                        <a href="{{ route('fees.show', $payment) }}" class="d-block text-decoration-none text-body border-bottom p-3"><strong>{{ $payment->student->user->name ?? 'Unknown student' }}</strong><span class="d-block small text-muted">₦{{ number_format($payment->amount_paid, 2) }} · {{ ucfirst($payment->status) }}</span></a>
                    @empty
                        <p class="text-muted p-3 mb-0">No payment records available.</p>
                    @endforelse
                </div>
            </section>
            <section class="card border-0 shadow-sm" aria-labelledby="scholarships-heading">
                <div class="card-header bg-white border-bottom"><h2 id="scholarships-heading" class="h5 mb-0">Active scholarships</h2></div>
                <div class="card-body">
                    @forelse($scholarships as $scholarship)
                        <div class="border-bottom py-2"><strong>{{ $scholarship->title }}</strong><span class="d-block small text-muted">{{ $scholarship->amount !== null ? '₦' . number_format($scholarship->amount, 2) : 'Amount not specified' }}</span></div>
                    @empty
                        <p class="text-muted mb-0">No active scholarships available.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
