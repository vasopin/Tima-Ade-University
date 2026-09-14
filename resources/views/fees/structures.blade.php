@extends('layouts.app')

@section('title', 'Fee Structures')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('fees.index') }}">Fees</a></li>
    <li class="breadcrumb-item active">Fee Structures</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="page-header-title mb-1">Fee Structures & Pricing</h3>
            <p class="text-muted small mb-0">Define tuition, transport, exam, and other fees per class level.</p>
        </div>
        <a href="{{ route('fees.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Payments
        </a>
    </div>

    <div class="row g-4">
        <!-- Add Structure Form -->
        <div class="col-lg-4">
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-plus-circle me-2 text-danger"></i>Add Fee Structure</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('fees.structures.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label required fw-semibold">Target Class</label>
                            <select name="school_class_id" class="form-select @error('school_class_id') is-invalid @enderror" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required fw-semibold">Fee Type / Title</label>
                            <input type="text" name="fee_type" class="form-control" required placeholder="e.g. Tuition Fee or Lab Fee">
                        </div>

                        <div class="mb-3">
                            <label class="form-label required fw-semibold">Amount ($)</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required placeholder="0.00">
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Academic Year</label>
                                <input type="text" name="academic_year" class="form-control" value="2024-2025" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Term</label>
                                <input type="text" name="term" class="form-control" placeholder="e.g. Term 1">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Due Date</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-crimson w-100 fw-semibold">
                            <i class="bi bi-check-lg me-1"></i> Save Fee Structure
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Structures List -->
        <div class="col-lg-8">
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-list-columns me-2 text-primary"></i>Configured Fee Tiers</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Class</th>
                                <th>Fee Type</th>
                                <th>Amount</th>
                                <th>Academic Year</th>
                                <th>Term</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($structures as $st)
                                <tr>
                                    <td><span class="fw-semibold text-dark">{{ $st->schoolClass->name ?? 'All' }}</span></td>
                                    <td>{{ $st->fee_type }}</td>
                                    <td class="fw-bold text-success">${{ number_format($st->amount, 2) }}</td>
                                    <td>{{ $st->academic_year }}</td>
                                    <td>{{ $st->term ?? '—' }}</td>
                                    <td>{{ $st->due_date ? $st->due_date->format('M d, Y') : '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No fee structures configured yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($structures->hasPages())
                    <div class="card-footer bg-white py-3">
                        {{ $structures->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
