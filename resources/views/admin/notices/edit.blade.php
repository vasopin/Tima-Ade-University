@extends('layouts.app')

@section('title', 'Edit Notice — ' . $notice->title)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.notices.index') }}">Notices</a></li>
    <li class="breadcrumb-item active">Edit Notice</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-pencil me-2 text-primary"></i>Edit Announcement / Notice</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.notices.update', $notice) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notice Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title', $notice->title) }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6"><label class="form-label fw-semibold">Replace video</label><input type="file" name="video" class="form-control @error('video') is-invalid @enderror" accept="video/mp4,video/webm,video/quicktime">@if($notice->video_path)<small class="text-muted">A video is currently attached.</small>@endif</div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Change thumbnail</label><input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/jpeg,image/png,image/webp"></div>
                        </div>
                        <div class="row g-3 mb-3"><div class="col-md-4"><label class="form-label fw-semibold">Status</label><select name="status" class="form-select"><option value="draft" {{ old('status', $notice->status) === 'draft' ? 'selected' : '' }}>Draft</option><option value="published" {{ old('status', $notice->status ?: ($notice->is_active ? 'published' : 'draft')) === 'published' ? 'selected' : '' }}>Published</option><option value="scheduled" {{ old('status', $notice->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option></select></div><div class="col-md-4"><label class="form-label fw-semibold">Target audience</label><select name="audience" class="form-select">@foreach(['everyone','students','teachers','parents','admins'] as $audience)<option value="{{ $audience }}" {{ old('audience', $notice->audience ?: 'everyone') === $audience ? 'selected' : '' }}>{{ ucfirst($audience) }}</option>@endforeach</select></div><div class="col-md-4"><label class="form-label fw-semibold">Expires on</label><input type="date" name="expires_at" class="form-control" value="{{ old('expires_at', $notice->expires_at?->toDateString()) }}"></div></div>
                        <div class="row g-3 mb-3"><div class="col-md-6"><label class="form-label fw-semibold">Optional attachment</label><input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx"></div><div class="col-md-6"><label class="form-label fw-semibold">External video URL</label><input type="url" name="external_video_url" class="form-control" value="{{ old('external_video_url', $notice->external_video_url) }}"></div></div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="General" {{ old('category', $notice->category) == 'General' ? 'selected' : '' }}>General</option>
                                    <option value="Academic" {{ old('category', $notice->category) == 'Academic' ? 'selected' : '' }}>Academic</option>
                                    <option value="Event" {{ old('category', $notice->category) == 'Event' ? 'selected' : '' }}>Event / Activity</option>
                                    <option value="Holiday" {{ old('category', $notice->category) == 'Holiday' ? 'selected' : '' }}>Holiday Notice</option>
                                    <option value="Emergency" {{ old('category', $notice->category) == 'Emergency' ? 'selected' : '' }}>Urgent / Emergency</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Published Date</label>
                                <input type="date" name="published_date" class="form-control" 
                                       value="{{ old('published_date', $notice->published_date?->toDateString()) }}" required>
                            </div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_pinned" id="is_pinned" value="1" {{ old('is_pinned', $notice->is_pinned) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_pinned">Pinned notice</label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notice Content <span class="text-danger">*</span></label>
                            <textarea name="content" rows="6" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $notice->content) }}</textarea>
                            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $notice->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Published and active</label>
                        </div>

                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('admin.notices.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check2-circle me-1"></i> Update Notice</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
