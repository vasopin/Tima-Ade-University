@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1>Add Scholarship</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.scholarships.store') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input name="title" class="form-control" value="{{ old('title') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Amount (optional)</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Criteria</label>
            <textarea name="criteria" class="form-control">{{ old('criteria') }}</textarea>
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input">
            <label for="is_active" class="form-check-label">Active</label>
        </div>
        <button class="btn btn-primary" type="submit" data-loading-text="<span class=\"spinner-border spinner-border-sm spinner-border-sm-custom\" role=\"status\" aria-hidden=\"true\"></span> Saving...">Save</button>
    </form>
</div>
@endsection
