@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1>Edit Accreditation</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.accreditations.update', $item) }}" method="POST" class="needs-validation" novalidate>
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Issuer</label>
            <input name="issuer" class="form-control" value="{{ old('issuer', $item->issuer) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Issued At</label>
            <input type="date" name="issued_at" class="form-control" value="{{ old('issued_at', $item->issued_at?->format('Y-m-d')) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Expires At</label>
            <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at', $item->expires_at?->format('Y-m-d')) }}">
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" {{ $item->is_active ? 'checked' : '' }}>
            <label for="is_active" class="form-check-label">Active</label>
        </div>
        <button class="btn btn-primary" type="submit" data-loading-text="<span class=\"spinner-border spinner-border-sm spinner-border-sm-custom\" role=\"status\" aria-hidden=\"true\"></span> Saving...">Save</button>
    </form>
</div>
@endsection
