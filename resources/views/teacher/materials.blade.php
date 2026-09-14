@extends('layouts.app')

@section('title', 'Teacher Materials')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Materials</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="text-uppercase small text-primary fw-semibold mb-1">University Portal</div>
            <h2 class="mb-0">Course Materials</h2>
        </div>
        <a href="{{ route('teacher.courses') }}" class="btn btn-outline-secondary">
            <i class="bi bi-journal-bookmark me-1"></i> My courses
        </a>
    </div>

    @if($teacherClasses->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-file-earmark-pdf fs-1 text-muted mb-3 d-block"></i>
                <h5 class="mb-2">No learning materials have been uploaded yet.</h5>
                <p class="text-muted mb-0">You can upload PDF course materials once you are assigned to a class and course.</p>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3">Upload PDF material</h5>
                <form method="POST" action="{{ route('teacher.learning.material.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Lecture Notes Week 3" required>
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
                            <label class="form-label">PDF file</label>
                            <input type="file" name="material" class="form-control" accept="application/pdf" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3" class="form-control" placeholder="Describe this learning material."></textarea>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-success"><i class="bi bi-file-earmark-pdf me-1"></i> Upload PDF</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Uploaded materials</h5>

            @if($teacherMaterials->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-file-earmark-pdf fs-1 mb-3 d-block"></i>
                    <h6>No learning materials have been uploaded yet.</h6>
                    <p class="mb-0">Your PDF resources will appear here as soon as they are added.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Course</th>
                                <th>Class</th>
                                <th>Uploaded</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teacherMaterials as $material)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-file-earmark-pdf text-danger"></i>
                                            <div>
                                                <div class="fw-semibold">{{ $material->title }}</div>
                                                <small class="text-muted">{{ $material->original_name ?? 'PDF document' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $material->subject?->name ?? 'Course' }}</td>
                                    <td>{{ $material->schoolClass?->name ?? 'Class' }}</td>
                                    <td>{{ $material->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('learning.material.file', ['material' => $material->id]) }}" class="btn btn-sm btn-primary" target="_blank">Open</a>
                                            <form method="POST" action="{{ route('teacher.learning.material.delete', ['material' => $material->id]) }}" onsubmit="return confirm('Delete this material?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
