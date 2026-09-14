@extends('layouts.app')

@section('title', 'Teacher Videos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Videos</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="text-uppercase small text-primary fw-semibold mb-1">University Portal</div>
            <h2 class="mb-0">Lecture Videos</h2>
        </div>
        <a href="{{ route('teacher.courses') }}" class="btn btn-outline-secondary">
            <i class="bi bi-journal-bookmark me-1"></i> My courses
        </a>
    </div>

    @if($teacherClasses->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-play-circle fs-1 text-muted mb-3 d-block"></i>
                <h5 class="mb-2">No classes have been assigned to you yet.</h5>
                <p class="text-muted mb-0">You can upload lecture videos only for your assigned classes and courses.</p>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3">Upload lecture video</h5>
                <form method="POST" action="{{ route('teacher.learning.video.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Introduction to Computer Science" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Class</label>
                            <select name="school_class_id" class="form-select" required>
                                <option value="">Select class</option>
                                @foreach($teacherClasses as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Course</label>
                            <select name="subject_id" class="form-select" required>
                                <option value="">Select course</option>
                                @foreach($teacherClasses as $class)
                                    @foreach($class->subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $class->name }} — {{ $subject->name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Video file</label>
                            <input type="file" name="video" class="form-control" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3" class="form-control" placeholder="Add a brief overview of the lecture."></textarea>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i> Upload video</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Available videos</h5>

            @if($teacherVideos->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-play-circle fs-1 mb-3 d-block"></i>
                    <h6>No videos uploaded yet.</h6>
                    <p class="mb-0">Upload your first lecture video to make it available to enrolled students.</p>
                </div>
            @else
                <div class="row g-3">
                    @foreach($teacherVideos as $video)
                        <div class="col-lg-4 col-md-6">
                            <div class="card border h-100">
                                <div class="card-body">
                                    <div class="small text-muted mb-2">{{ $video->schoolClass?->name ?? 'Class' }} · {{ $video->subject?->name ?? 'Course' }}</div>
                                    <h6 class="mb-2">{{ $video->title }}</h6>
                                    <p class="small text-muted mb-3">{{ $video->description ?: 'No description provided.' }}</p>
                                    <div class="d-flex justify-content-between small text-muted mb-3">
                                        <span>{{ $video->created_at->format('M d, Y') }}</span>
                                        <span>{{ $video->file_size ? number_format($video->file_size / 1024 / 1024, 2) . ' MB' : 'Media file' }}</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('learning.video.file', ['video' => $video->id]) }}" class="btn btn-sm btn-primary" target="_blank">Open</a>
                                        <form method="POST" action="{{ route('teacher.learning.video.delete', ['video' => $video->id]) }}" onsubmit="return confirm('Delete this video?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
