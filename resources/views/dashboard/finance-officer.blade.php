@extends('layouts.app')

@section('title', 'Finance Officer Dashboard')

@section('content')
<div class="container-fluid px-4 py-6 bg-light min-vh-100">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="h2 mb-2">Finance Officer Dashboard</h1>
            <p class="text-muted">Track student fees, payments, and outstanding balances</p>
        </div>
    </div>

    <!-- Key Statistics -->
    <div class="row mb-4 g-3">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Students</p>
                            <h3 class="mb-0">{{ $stats['total_students'] }}</h3>
                        </div>
                        <div class="row mb-4 g-3">
                            <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">Total billed</small><h4>₦{{ number_format($stats['total_billed'] ?? 0, 2) }}</h4></div></div></div>
                            <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">Outstanding balance</small><h4 class="text-danger">₦{{ number_format($stats['outstanding_balance'] ?? 0, 2) }}</h4></div></div></div>
                            <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">Refunds processed</small><h4>₦{{ number_format($stats['refunds'] ?? 0, 2) }}</h4></div></div></div>
                            <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><small class="text-muted">Reconciliation exceptions</small><h4 class="text-warning">{{ $stats['reconciliation_exceptions'] ?? 0 }}</h4></div></div></div>
                        </div>
                        <span class="badge bg-primary">👥</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Fees Collected</p>
                            <h3 class="mb-0">₦{{ number_format($stats['total_collected'], 0) }}</h3>
                        </div>
                        <span class="badge bg-success">💰</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Pending Payments</p>
                            <h3 class="mb-0 text-warning">{{ $stats['total_pending'] }}</h3>
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
                            <p class="text-muted small mb-1">Overdue Payments</p>
                            <h3 class="mb-0 text-danger">{{ $stats['overdue_count'] }}</h3>
                        </div>
                        <span class="badge bg-danger">⚠</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Status Overview -->
    <div class="row mb-4 g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Payment Status Breakdown</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tbody>
                            @php
                                $statusLabels = [
                                    'paid' => 'Paid',
                                    'partial' => 'Partial Payment',
                                    'pending' => 'Pending',
                                    'overdue' => 'Overdue'
                                ];
                                $statusColors = [
                                    'paid' => 'bg-success',
                                    'partial' => 'bg-info',
                                    'pending' => 'bg-warning',
                                    'overdue' => 'bg-danger'
                                ];
                            @endphp
                            @foreach($paymentsByStatus as $status => $count)
                                <tr>
                                    <td>
                                        <span>{{ $statusLabels[$status] ?? ucfirst($status) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ $count }}</strong>
                                    </td>
                                    <td class="text-end" style="width: 40%;">
                                        <div class="progress" style="height: 20px;">
                                            @php
                                                $total = array_sum($paymentsByStatus);
                                                $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                            @endphp
                                            <div class="progress-bar {{ $statusColors[$status] }}" role="progressbar"
                                                 style="width: {{ $percentage }}%;"
                                                 aria-valuenow="{{ $count }}" aria-valuemin="0"
                                                 aria-valuemax="{{ $total }}">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Collections by Class</h5>
                </div>
                <div class="card-body">
                    @if($feeByClass->isNotEmpty())
                        <table class="table table-sm table-borderless">
                            <tbody>
                                @foreach($feeByClass as $class)
                                    <tr>
                                        <td>
                                            <span class="text-capitalize fw-medium">{{ $class['name'] }}</span>
                                            <br>
                                            <small class="text-muted">{{ $class['student_count'] }} student{{ $class['student_count'] !== 1 ? 's' : '' }}</small>
                                        </td>
                                        <td class="text-end">
                                            <strong>₦{{ number_format($class['collected'], 0) }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted mb-0">No class data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Payments</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Student</th>
                                <th>Payment Method</th>
                                <th>Amount Paid</th>
                                <th>Payment Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                                <tr>
                                    <td class="px-4">{{ $payment->student->user->name }}</td>
                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                    <td>₦{{ number_format($payment->amount_paid, 2) }}</td>
                                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'paid' => 'bg-success',
                                                'partial' => 'bg-info',
                                                'pending' => 'bg-warning text-dark',
                                                'overdue' => 'bg-danger'
                                            ];
                                            $statusClass = $statusClasses[$payment->status] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No recent payments found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Outstanding Balances -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Outstanding Balances</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Student</th>
                                <th>Class</th>
                                <th>Fee Type</th>
                                <th>Amount Due</th>
                                <th>Status</th>
                                <th>Days Overdue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($outstandingBalances as $payment)
                                <tr>
                                    <td class="px-4">{{ $payment->student->user->name }}</td>
                                    <td>{{ $payment->student->schoolClass->name ?? 'N/A' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $payment->feeStructure->fee_type ?? 'Unknown')) }}</td>
                                    <td>₦{{ number_format($payment->getTotalDueAttribute(), 2) }}</td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'paid' => 'bg-success',
                                                'partial' => 'bg-info',
                                                'pending' => 'bg-warning text-dark',
                                                'overdue' => 'bg-danger'
                                            ];
                                            $statusClass = $statusClasses[$payment->status] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($payment->status === 'overdue' && $payment->feeStructure->due_date)
                                            @php
                                                $daysOverdue = now()->diffInDays($payment->feeStructure->due_date);
                                            @endphp
                                            <span class="badge bg-danger">{{ $daysOverdue }} days</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No outstanding balances found.
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
