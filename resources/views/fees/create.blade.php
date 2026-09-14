@extends('layouts.app')

@section('title', 'Record Fee Payment')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('fees.index') }}">Fees</a></li>
    <li class="breadcrumb-item active">Record Payment</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-header-title mb-1">Record Fee Payment</h3>
            <p class="text-muted small mb-0">Issue receipts and log transactions for student fees.</p>
        </div>
        <a href="{{ route('fees.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-cash me-2 text-danger"></i>Transaction Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('fees.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label required fw-semibold">Select Student</label>
                            <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                <option value="">Choose Student</option>
                                @foreach($students as $s)
                                    <option value="{{ $s->id }}">
                                        {{ $s->user->name }} ({{ $s->roll_number }} - {{ $s->schoolClass->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required fw-semibold">Select Fee Structure</label>
                            <select name="fee_structure_id" class="form-select @error('fee_structure_id') is-invalid @enderror" required id="feeStructureSelect">
                                <option value="">Choose Fee</option>
                                @foreach($structures as $st)
                                    <option value="{{ $st->id }}" data-amount="{{ $st->amount }}">
                                        {{ $st->schoolClass->name }} - {{ $st->fee_type }} (${{ number_format($st->amount, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label required fw-semibold">Amount Paid ($)</label>
                                <input type="number" step="0.01" name="amount_paid" id="amountPaid" class="form-control @error('amount_paid') is-invalid @enderror" required placeholder="0.00">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Discount ($)</label>
                                <input type="number" step="0.01" name="discount" class="form-control" value="0.00">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Late Fee / Penalty ($)</label>
                                <input type="number" step="0.01" name="late_fee" class="form-control" value="0.00">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Payment Date</label>
                                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Payment Method</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="cash" selected>Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="online">Online / Card</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Remarks / Memo</label>
                            <textarea name="remarks" class="form-control" rows="2" placeholder="Optional payment reference or transaction note..."></textarea>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('fees.index') }}" class="btn btn-light me-2">Cancel</a>
                            <button type="submit" class="btn btn-crimson px-4 fw-semibold">
                                <i class="bi bi-check-circle-fill me-1"></i> Issue Receipt & Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('feeStructureSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const amount = selectedOption.dataset.amount;
    if (amount) {
        document.getElementById('amountPaid').value = parseFloat(amount).toFixed(2);
    }
});
</script>
@endpush
@endsection
