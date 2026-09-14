@extends('layouts.app')

@section('title', 'Receipt #' . $fee->receipt_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('fees.index') }}">Fees</a></li>
    <li class="breadcrumb-item active">Receipt</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="page-header-title mb-1">Official Fee Receipt</h3>
            <p class="text-muted small mb-0">Transaction voucher and verification document.</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-navy shadow-sm">
                <i class="bi bi-printer me-1"></i> Print Receipt
            </button>
            <a href="{{ route('fees.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Receipt Sheet Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card custom-card p-4 receipt-card shadow-sm">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                    <div class="d-flex align-items-center">
                        <div class="brand-icon me-3" style="width: 48px; height: 48px; font-size: 1.5rem;">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">Tima-Ade University</h4>
                            <span class="text-muted small">Higher Academic Institute</span>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-crimson fs-6 px-3 py-2">RECEIPT</span>
                        <div class="fw-bold mt-2 text-dark">{{ $fee->receipt_number }}</div>
                        <div class="small text-muted">{{ $fee->payment_date ? $fee->payment_date->format('M d, Y') : date('M d, Y') }}</div>
                    </div>
                </div>

                <!-- Student & Payment Info -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted small fw-bold text-uppercase">Billed To</h6>
                        <h5 class="fw-bold text-dark mb-1">{{ $fee->student->user->name ?? 'N/A' }}</h5>
                        <div class="text-muted small">
                            <div><strong>Roll No:</strong> {{ $fee->student->roll_number }}</div>
                            <div><strong>Admission No:</strong> {{ $fee->student->admission_number }}</div>
                            <div><strong>Class:</strong> {{ $fee->student->schoolClass->name }} ({{ $fee->student->section->name }})</div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="text-muted small fw-bold text-uppercase">Payment Info</h6>
                        <div class="text-muted small">
                            <div><strong>Method:</strong> <span class="text-capitalize">{{ str_replace('_', ' ', $fee->payment_method) }}</span></div>
                            <div><strong>Status:</strong> {!! $fee->status_badge !!}</div>
                            <div><strong>Collected By:</strong> {{ $fee->receivedBy->name ?? 'Admin' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th>Academic Term</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong>{{ $fee->feeStructure->fee_type ?? 'School Fee' }}</strong>
                                    <div class="small text-muted">Class: {{ $fee->feeStructure->schoolClass->name ?? 'N/A' }}</div>
                                </td>
                                <td>{{ $fee->feeStructure->academic_year ?? 'Current' }} ({{ $fee->feeStructure->term ?? 'Term' }})</td>
                                <td class="text-end fw-semibold">${{ number_format($fee->feeStructure->amount ?? 0, 2) }}</td>
                            </tr>
                            @if($fee->discount > 0)
                                <tr class="text-success">
                                    <td colspan="2" class="text-end">Discount Applied:</td>
                                    <td class="text-end">-${{ number_format($fee->discount, 2) }}</td>
                                </tr>
                            @endif
                            @if($fee->late_fee > 0)
                                <tr class="text-danger">
                                    <td colspan="2" class="text-end">Late Fee / Penalty:</td>
                                    <td class="text-end">+${{ number_format($fee->late_fee, 2) }}</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <th colspan="2" class="text-end fs-5">Total Paid:</th>
                                <th class="text-end fs-5 text-success">${{ number_format($fee->amount_paid, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($fee->remarks)
                    <div class="alert alert-light border small text-muted mb-4">
                        <strong>Note:</strong> {{ $fee->remarks }}
                    </div>
                @endif

                <!-- Footer Signatures -->
                <div class="row pt-5 mt-4 text-center">
                    <div class="col-6">
                        <div class="border-top pt-2 small text-muted">Student / Guardian Signature</div>
                    </div>
                    <div class="col-6">
                        <div class="border-top pt-2 small text-muted">Authorized Registrar Signature</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
