@extends('layouts.app')

@section('title', 'Notice Board Videos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.notices.index') }}">Notice Board</a></li>
    <li class="breadcrumb-item active">Videos / Media</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h3 class="page-title mb-1">Notice Board Video Management</h3><p class="text-muted small mb-0">Publish, review, and maintain official university video bulletins.</p></div>
        <a href="{{ route('admin.notices.create') }}" class="btn btn-crimson"><i class="bi bi-cloud-upload me-1"></i> Upload Video</a>
    </div>

    <div class="card custom-card mb-4"><div class="card-body"><form method="GET" class="row g-2 align-items-end">
        <div class="col-md-5"><label class="form-label small text-muted">Search videos</label><input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search title"></div>
        <div class="col-md-3"><label class="form-label small text-muted">Category</label><select name="category" class="form-select"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>{{ $category }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label small text-muted">Status</label><select name="status" class="form-select"><option value="">All statuses</option>@foreach(['draft','published','scheduled'] as $status)<option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <div class="col-md-2"><button class="btn btn-navy w-100"><i class="bi bi-filter me-1"></i>Filter</button></div>
    </form></div></div>

    <div class="card custom-card"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Video</th><th>Category</th><th>Audience</th><th>Status</th><th>Published</th><th>Expires</th><th>Featured</th><th class="text-end">Actions</th></tr></thead><tbody>
        @forelse($videos as $video)
            <tr><td><div class="d-flex align-items-center gap-3">@if($video->thumbnail_url)<img src="{{ $video->thumbnail_url }}" alt="" width="88" height="50" class="rounded object-fit-cover">@else<div class="bg-dark text-white rounded d-flex align-items-center justify-content-center" style="width:88px;height:50px"><i class="bi bi-play-fill"></i></div>@endif<div><a href="{{ route('public.notices.single', $video) }}" target="_blank" class="fw-bold text-dark text-decoration-none">{{ $video->title }}</a><div class="small text-muted"><i class="bi bi-camera-video me-1"></i>{{ $video->video_original_name ?: 'External video' }}</div></div></div></td><td><span class="badge bg-light text-dark">{{ $video->category }}</span></td><td>{{ ucfirst($video->audience ?: 'everyone') }}</td><td><span class="badge {{ $video->status === 'published' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($video->status ?: 'draft') }}</span></td><td>{{ $video->published_date?->format('M d, Y') }}</td><td>{{ $video->expires_at?->format('M d, Y') ?: 'No expiry' }}</td><td>@if($video->is_pinned)<span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> Featured</span>@else<span class="text-muted">No</span>@endif</td><td class="text-end"><div class="btn-group btn-group-sm"><a href="{{ route('public.notices.single', $video) }}" target="_blank" class="btn btn-outline-secondary" title="Preview"><i class="bi bi-play"></i></a><a href="{{ route('admin.notices.edit', $video) }}" class="btn btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a><form method="POST" action="{{ route('admin.notices.destroy', $video) }}" onsubmit="return confirm('Delete this video notice?');">@csrf @method('DELETE')<button class="btn btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button></form></div></td></tr>
        @empty
            <tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-camera-video display-6 d-block mb-2"></i>No video notices yet. Use Upload Video to publish the first one.</td></tr>
        @endforelse
    </tbody></table></div>@if($videos->hasPages())<div class="card-footer bg-white">{{ $videos->links() }}</div>@endif</div>
</div>
@endsection
