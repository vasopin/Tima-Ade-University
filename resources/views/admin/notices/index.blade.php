@extends('layouts.app')

@section('title', 'Notices Management')

@section('breadcrumb')
    <li class="breadcrumb-item">Administration</li>
    <li class="breadcrumb-item active">Notices</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title mb-1">Notice Board Management</h3>
            <p class="text-muted small mb-0">Publish official announcements, event circulars, and academic notices visible to students, staff, and public.</p>
        </div>
        <div class="d-flex gap-2"><a href="{{ route('admin.notices.videos') }}" class="btn btn-outline-primary"><i class="bi bi-camera-video me-1"></i> Videos / Media</a><a href="{{ route('admin.notices.create') }}" class="btn btn-crimson"><i class="bi bi-plus-circle me-1"></i> Post New Notice</a></div>
    </div>

    <div class="card custom-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.notices.index') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small text-muted">Search notices</label>
                    <input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search title">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Category</label>
                    <input type="text" name="category" class="form-control" value="{{ request('category') }}" placeholder="e.g. Academic">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All statuses</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-navy w-100"><i class="bi bi-filter me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notice List -->
    <div class="card custom-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Published Date</th>
                        <th>Pinned</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notices as $notice)
                        <tr>
                            <td>
                                <a href="{{ route('public.notices.single', $notice->slug ?? $notice->id) }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                                    {{ $notice->title }}
                                </a>
                            </td>
                            <td><span class="badge bg-light text-dark text-uppercase">{{ $notice->category }}</span></td>
                            <td>
                                @if($notice->is_active)
                                    <span class="badge bg-success">{{ ucfirst($notice->status ?: 'published') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($notice->status ?: 'draft') }}</span>
                                @endif
                            </td>
                            <td>{{ $notice->published_date->format('M d, Y') }}</td>
                            <td>{{ $notice->is_pinned ? 'Pinned' : '—' }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('public.notices.single', $notice->slug ?? $notice->id) }}" target="_blank" class="btn btn-outline-secondary" title="View Public Page">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.notices.edit', $notice) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.notices.destroy', $notice) }}" class="d-inline" onsubmit="return confirm('Delete this notice?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No notices posted yet. Click "Post New Notice" to create one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($notices->hasPages())
            <div class="card-footer bg-white">
                {{ $notices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
