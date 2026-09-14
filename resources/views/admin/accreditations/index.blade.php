@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Accreditations</h1>
        <a href="{{ route('admin.accreditations.create') }}" class="btn btn-primary">Add Accreditation</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
    <table class="table table-striped table-hover datatable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Issuer</th>
                <th>Issued</th>
                <th>Expires</th>
                <th>Active</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->issuer }}</td>
                <td>{{ $item->issued_at?->format('Y-m-d') }}</td>
                <td>{{ $item->expires_at?->format('Y-m-d') }}</td>
                <td>{{ $item->is_active ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="{{ route('admin.accreditations.edit', $item) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                    <form action="{{ route('admin.accreditations.destroy', $item) }}" method="POST" style="display:inline-block" data-confirm="Delete this accreditation?">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    <nav aria-label="Accreditations pagination" class="mt-3">
        {{ $items->links('pagination::bootstrap-5') }}
    </nav>
</div>
@endsection
