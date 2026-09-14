@extends('layouts.app')

@section('title', $class->name . ' — Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('classes.index') }}">Classes</a></li>
    <li class="breadcrumb-item active">{{ $class->name }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h3 class="page-header-title mb-0">{{ $class->name }}</h3>
                <span class="badge {{ $class->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $class->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="text-muted small mt-1">
                <span><i class="bi bi-person-badge me-1"></i>Class Teacher: {{ $class->classTeacher?->user->name ?? 'Not Assigned' }}</span> • 
                <span><i class="bi bi-people me-1"></i>Total Students: {{ $class->students->count() }}</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('classes.edit', $class) }}" class="btn btn-crimson btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit Class
            </a>
            <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sections & Subjects -->
        <div class="col-lg-4">
            <!-- Sections Card -->
            <div class="card custom-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-grid me-2 text-primary"></i>Sections</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($class->sections as $sec)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">{{ $sec->name }}</span>
                            <span class="badge bg-light text-dark border">{{ $sec->students->count() }} / {{ $sec->capacity }} Students</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center">No sections defined.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Subjects Card -->
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-book me-2 text-danger"></i>Assigned Subjects</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($class->subjects as $subj)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">{{ $subj->name }}</div>
                                <small class="text-muted">{{ $subj->code }}</small>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center">No subjects mapped.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Enrolled Students Table -->
        <div class="col-lg-8">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-people me-2 text-success"></i>Enrolled Students</h5>
                    <a href="{{ route('students.create') }}" class="btn btn-sm btn-crimson">+ Enroll Student</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Roll #</th>
                                <th>Student</th>
                                <th>Section</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($class->students as $student)
                                <tr>
                                    <td><span class="badge bg-light text-dark border">{{ $student->roll_number }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $student->user->avatar_url }}" class="rounded-circle me-2" width="32" height="32" alt="">
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $student->user->name }}</div>
                                                <small class="text-muted">{{ $student->admission_number }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $student->section->name }}</td>
                                    <td>{!! $student->status_badge !!}</td>
                                    <td class="text-end">
                                        <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No students currently in this class.</td>
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
