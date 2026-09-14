@extends('layouts.app')

@section('title', 'Edit Fee Payment')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('fees.index') }}">Fees</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Receipt</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-pencil-square me-2 text-warning"></i>Edit Payment Receipt: {{ $fee->receipt_number }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-light border mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="text-muted small">Scholar:</span>
                                <div class="fw-bold text-dark">{{ $fee->student->user->name ?? 'N/A' }} ({{ $fee->student->roll_number ?? '' }})</div>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small">Fee Category:</span>
                                <div class="fw-bold text-dark">{{ $fee->feeStructure->fee_type ?? 'Tuition Fee' }} (${{ number_format($fee->feeStructure->amount ?? 0, 2) }})</div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('fees.update', $fee) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="amount_paid" class="form-label small fw-semibold text-muted">AMOUNT PAID ($)</label>
                                <input type="number" step="0.01" class="form-control @error('amount_paid') is-invalid @enderror" id="amount_paid" name="amount_paid" value="{{ old('amount_paid', $fee->amount_paid) }}" required>
                                @error('amount_paid')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="discount" class="form-label small fw-semibold text-muted">DISCOUNT ($)</label>
                                <input type="number" step="0.01" class="form-control @error('discount') is-invalid @enderror" id="discount" name="discount" value="{{ old('discount', $fee->discount) }}">
                            </div>

                            <div class="col-md-4">
                                <label for="late_fee" class="form-label small fw-semibold text-muted">LATE FEE ($)</label>
                                <input type="number" step="0.01" class="form-control @error('late_fee') is-invalid @enderror" id="late_fee" name="late_fee" value="{{ old('late_fee', $fee->late_fee) }}">
                            </div>

                            <div class="col-md-4">
                                <label for="payment_date" class="form-label small fw-semibold text-muted">PAYMENT DATE</label>
                                <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date', $fee->payment_date) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label for="payment_method" class="form-label small fw-semibold text-muted">PAYMENT METHOD</label>
                                <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                    <option value="cash" {{ old('payment_method', $fee->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="bank_transfer" {{ old('payment_method', $fee->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="cheque" {{ old('payment_method', $fee->payment_method) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="online" {{ old('payment_method', $fee->payment_method) == 'online' ? 'selected' : '' }}>Online / Mobile Money</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="status" class="form-label small fw-semibold text-muted">PAYMENT STATUS</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="paid" {{ old('status', $fee->status) == 'paid' ? 'selected' : '' }}>Paid in Full</option>
                                    <option value="partial" {{ old('status', $fee->status) == 'partial' ? 'selected' : '' }}>Partial</option>
                                    <option value="pending" {{ old('status', $fee->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="overdue" {{ old('status', $fee->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="remarks" class="form-label small fw-semibold text-muted">REMARKS / REFERENCE</label>
                                <textarea name="remarks" id="remarks" rows="3" class="form-control">{{ old('remarks', $fee->remarks) }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('fees.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm" style="background-color: #016ED5; border-color: #016ED5;">
                                <i class="bi bi-save me-1"></i> Update Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
