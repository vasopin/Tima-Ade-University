@extends('layouts.app')

@section('title', 'Librarian Dashboard')

@section('content')
<div class="container-fluid px-4 py-6 bg-light min-vh-100">
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="h2 mb-2">Librarian Dashboard</h1>
            <p class="text-muted">Manage library, books, and borrowing records</p>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Books</p>
                            <h3 class="mb-0">{{ $stats['total_books'] }}</h3>
                        </div>
                        <span class="badge bg-primary">📚</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Available Books</p>
                            <h3 class="mb-0 text-success">{{ $stats['available_books'] }}</h3>
                        </div>
                        <span class="badge bg-success">✓</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Library Members</p>
                            <h3 class="mb-0">{{ $stats['total_members'] }}</h3>
                        </div>
                        <span class="badge bg-info">👤</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Overdue Books</p>
                            <h3 class="mb-0 text-danger">{{ $stats['overdue_books'] }}</h3>
                        </div>
                        <span class="badge bg-danger">⚠</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Recent Borrowings</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Member</th>
                                <th>Book Title</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBorrowings as $borrow)
                                <tr>
                                    <td class="px-4">{{ $borrow->libraryMember->user->name ?? 'N/A' }}</td>
                                    <td>{{ $borrow->book->title ?? 'N/A' }}</td>
                                    <td>{{ $borrow->due_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge
                                            @if($borrow->status === 'returned') bg-success
                                            @elseif($borrow->status === 'overdue') bg-danger
                                            @elseif($borrow->status === 'active') bg-info
                                            @else bg-secondary
                                            @endif">
                                            {{ ucfirst($borrow->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No borrowings found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Active Reservations</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Member</th>
                                <th>Book Title</th>
                                <th>Reservation Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservations as $res)
                                <tr>
                                    <td class="px-4">{{ $res->libraryMember->user->name ?? 'N/A' }}</td>
                                    <td>{{ $res->book->title ?? 'N/A' }}</td>
                                    <td>{{ $res->reservation_date->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No reservations.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
