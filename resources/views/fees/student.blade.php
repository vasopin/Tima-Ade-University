@extends('layouts.app')

@section('title', 'Fees for ' . $student->user->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('fees.index') }}">Fees</a></li>
    <li class="breadcrumb-item active">{{ $student->user->name }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="page-header-title mb-1">Student Billing: {{ $student->user->name }}</h3>
            <p class="text-muted small mb-0">{{ $student->roll_number }} • {{ $student->schoolClass->name }} ({{ $student->section->name }})</p>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('fees.create') }}" class="btn btn-crimson btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Record Payment
                </a>
            @endif
            <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Student Profile
            </a>
        </div>
    </div>

    <div class="card custom-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Receipt #</th>
                        <th>Fee Type</th>
                        <th>Amount Paid</th>
                        <th>Payment Date</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($student->feePayments as $payment)
                        <tr>
                            <td><code>{{ $payment->receipt_number }}</code></td>
                            <td>{{ $payment->feeStructure->fee_type ?? 'N/A' }}</td>
                            <td class="fw-bold text-success">${{ number_format($payment->amount_paid, 2) }}</td>
                            <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : '—' }}</td>
                            <td class="text-capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                            <td>{!! $payment->status_badge !!}</td>
                            <td class="text-end">
                                <a href="{{ route('fees.show', $payment) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-receipt"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No fee records found for this student.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
