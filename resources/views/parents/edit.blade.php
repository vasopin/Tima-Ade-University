@extends('layouts.app')

@section('title', 'Edit Parent / Guardian')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('parents.index') }}">Parents</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Parent / Guardian: {{ $parent->user->name }}</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('parents.update', $parent) }}">
                        @csrf
                        @method('PUT')

                        <h6 class="fw-bold text-dark mb-3 small text-uppercase tracking-wider">Account & Personal Info</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label small fw-semibold text-muted">FULL NAME</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $parent->user->name) }}" required @readonly(auth()->user()->isStaff())>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label small fw-semibold text-muted">EMAIL ADDRESS</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $parent->user->email) }}" required @readonly(auth()->user()->isStaff())>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label small fw-semibold text-muted">PHONE NUMBER</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $parent->user->phone) }}" @readonly(auth()->user()->isStaff())>
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            @if(auth()->user()->isAdmin())
                            <div class="col-md-6">
                                <label for="password" class="form-label small fw-semibold text-muted">RESET PASSWORD (LEAVE BLANK TO KEEP CURRENT)</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="New password (optional)">
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                        </div>

                        <h6 class="fw-bold text-dark mb-3 small text-uppercase tracking-wider">Guardian Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="relationship" class="form-label small fw-semibold text-muted">RELATIONSHIP</label>
                                <select name="relationship" id="relationship" class="form-select @error('relationship') is-invalid @enderror" required>
                                    <option value="father" {{ old('relationship', $parent->relationship) == 'father' ? 'selected' : '' }}>Father</option>
                                    <option value="mother" {{ old('relationship', $parent->relationship) == 'mother' ? 'selected' : '' }}>Mother</option>
                                    <option value="guardian" {{ old('relationship', $parent->relationship) == 'guardian' ? 'selected' : '' }}>Guardian / Sponsor</option>
                                    <option value="other" {{ old('relationship', $parent->relationship) == 'other' ? 'selected' : '' }}>Other Relative</option>
                                </select>
                                @error('relationship')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="occupation" class="form-label small fw-semibold text-muted">OCCUPATION</label>
                                <input type="text" class="form-control @error('occupation') is-invalid @enderror" id="occupation" name="occupation" value="{{ old('occupation', $parent->occupation) }}">
                                @error('occupation')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="national_id" class="form-label small fw-semibold text-muted">NATIONAL ID</label>
                                <input type="text" class="form-control @error('national_id') is-invalid @enderror" id="national_id" name="national_id" value="{{ old('national_id', $parent->national_id) }}">
                                @error('national_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h6 class="fw-bold text-dark mb-3 small text-uppercase tracking-wider">Linked Scholars</h6>
                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-muted">SELECT LINKED STUDENTS</label>
                            <div class="border rounded-3 p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                                @forelse($students as $st)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="student_ids[]" value="{{ $st->id }}" id="student_{{ $st->id }}" {{ in_array($st->id, old('student_ids', $selectedStudentIds)) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="student_{{ $st->id }}">
                                            <strong class="text-dark">{{ $st->user->name ?? 'Scholar' }}</strong> — Roll: {{ $st->roll_number }} ({{ $st->schoolClass->name ?? 'Class' }})
                                        </label>
                                    </div>
                                @empty
                                    <span class="text-muted small">No active students available.</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('parents.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm" style="background-color: #016ED5; border-color: #016ED5;">
                                <i class="bi bi-save me-1"></i> Update Parent
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
