@extends('layouts.app')

@section('title', 'Audit History')

@section('content')
<div class="container-fluid px-4 py-4 bg-light min-vh-100">
    <div class="mb-4"><p class="text-uppercase text-muted small fw-semibold mb-1">Finance workspace</p><h1 class="h2 mb-1">Audit History</h1><p class="text-muted mb-0">Recorded fee transactions and the users who received them.</p></div>
    <section class="card border-0 shadow-sm" aria-labelledby="audit-heading"><div class="card-header bg-white border-bottom"><h2 id="audit-heading" class="h5 mb-0">Payment transaction history</h2></div><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Receipt</th><th>Student</th><th>Amount</th><th>Status</th><th>Received by</th><th>Date</th></tr></thead><tbody>@forelse($payments as $payment)<tr><td><a href="{{ route('fees.show', $payment) }}">{{ $payment->receipt_number }}</a></td><td>{{ $payment->student->user->name ?? 'Unknown student' }}</td><td>₦{{ number_format($payment->amount_paid, 2) }}</td><td>{{ ucfirst($payment->status) }}</td><td>{{ $payment->receivedBy->name ?? 'Not recorded' }}</td><td>{{ $payment->created_at?->format('M j, Y, g:i A') }}</td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-5">No payment history available.</td></tr>@endforelse</tbody></table></div><div class="p-3">{{ $payments->links() }}</div></section>
</div>
@endsection
