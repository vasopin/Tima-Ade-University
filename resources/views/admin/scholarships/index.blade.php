@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Scholarships</h1>
        <a href="{{ route('admin.scholarships.create') }}" class="btn btn-primary">Add Scholarship</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
    <table class="table table-striped table-hover datatable">
        <thead>
            <tr>
                <th>Title</th>
                <th>Amount</th>
                <th>Active</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->title }}</td>
                <td>{{ $item->amount ? '$' . number_format($item->amount,2) : '-' }}</td>
                <td>{{ $item->is_active ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="{{ route('admin.scholarships.edit', $item) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                    <form action="{{ route('admin.scholarships.destroy', $item) }}" method="POST" style="display:inline-block" data-confirm="Delete this scholarship?">
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

    <nav aria-label="Scholarships pagination" class="mt-3">
        {{ $items->links('pagination::bootstrap-5') }}
    </nav>
</div>
@endsection
