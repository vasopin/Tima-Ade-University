@extends('layouts.app')

@section('title', 'Post New Notice')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.notices.index') }}">Notices</a></li>
    <li class="breadcrumb-item active">New Notice</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-megaphone me-2 text-danger"></i>Post New Announcement / Notice</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.notices.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notice Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   placeholder="e.g. Mid-Term Examination Schedule Announcement" value="{{ old('title') }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6"><label class="form-label fw-semibold">Video file</label><input type="file" name="video" class="form-control @error('video') is-invalid @enderror" accept="video/mp4,video/webm,video/quicktime">@error('video')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="col-md-6"><label class="form-label fw-semibold">Thumbnail / cover image</label><input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">@error('thumbnail')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4"><label class="form-label fw-semibold">Status</label><select name="status" class="form-select"><option value="draft">Draft</option><option value="published" selected>Published</option><option value="scheduled">Scheduled</option></select></div>
                            <div class="col-md-4"><label class="form-label fw-semibold">Target audience</label><select name="audience" class="form-select"><option value="everyone">Everyone</option><option value="students">Students</option><option value="teachers">Teachers</option><option value="parents">Parents</option><option value="admins">Admins</option></select></div>
                            <div class="col-md-4"><label class="form-label fw-semibold">Expires on</label><input type="date" name="expires_at" class="form-control" value="{{ old('expires_at') }}"></div>
                        </div>
                        <div class="row g-3 mb-3"><div class="col-md-6"><label class="form-label fw-semibold">Optional attachment</label><input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx"></div><div class="col-md-6"><label class="form-label fw-semibold">External video URL</label><input type="url" name="external_video_url" class="form-control" value="{{ old('external_video_url') }}" placeholder="https://..."></div></div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="General">General</option>
                                    <option value="Academic">Academic</option>
                                    <option value="Event">Event / Activity</option>
                                    <option value="Holiday">Holiday Notice</option>
                                    <option value="Emergency">Urgent / Emergency</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Published Date</label>
                                <input type="date" name="published_date" class="form-control" value="{{ old('published_date', today()->toDateString()) }}" required>
                            </div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_pinned" id="is_pinned" value="1" {{ old('is_pinned') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_pinned">Pin this notice</label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notice Content <span class="text-danger">*</span></label>
                            <textarea name="content" rows="6" class="form-control @error('content') is-invalid @enderror" 
                                      placeholder="Write the full notice announcement details here..." required>{{ old('content') }}</textarea>
                            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                            <label class="form-check-label fw-semibold" for="is_active">Publish immediately on public website & student portals</label>
                        </div>

                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('admin.notices.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-crimson px-4"><i class="bi bi-send me-1"></i> Publish Notice</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
