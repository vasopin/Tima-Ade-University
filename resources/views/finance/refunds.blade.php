@extends('layouts.app')

@section('title', 'Payments and Refunds')

@section('content')
<div class="container-fluid px-4 py-4 bg-light min-vh-100">
    <div class="mb-4"><p class="text-uppercase text-muted small fw-semibold mb-1">Finance workspace</p><h1 class="h2 mb-1">Payments &amp; Refunds</h1><p class="text-muted mb-0">Review existing payment transactions and refund requests.</p></div>
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Payment</th><th>Student</th><th>Amount</th><th>Reason</th><th>Status</th><th></th></tr></thead><tbody>@forelse($refunds as $refund)<tr><td>{{ $refund->payment->receipt_number }}</td><td>{{ $refund->payment->student->user->name ?? '—' }}</td><td>₦{{ number_format($refund->amount, 2) }}</td><td>{{ $refund->reason }}</td><td>{{ ucfirst($refund->status) }}</td><td>@if($refund->status === 'requested')<form method="post" action="{{ route('finance.refunds.approve', $refund) }}">@csrf<button class="btn btn-sm btn-outline-success">Process</button></form>@endif</td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-5">No refund requests available.</td></tr>@endforelse</tbody></table></div><div class="p-3">{{ $refunds->links() }}</div></div>
</div>
@endsection
