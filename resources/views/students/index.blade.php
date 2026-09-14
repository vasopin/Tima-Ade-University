@extends('layouts.app')

@section('title', 'Students')

@section('breadcrumb')
    <li class="breadcrumb-item active">Students</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="page-header-title mb-1">Students Directory</h3>
            <p class="text-muted small mb-0">Manage all student profiles, class enrollments, and academic details.</p>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
            <a href="{{ route('students.create') }}" class="btn btn-crimson shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Add New Student
            </a>
        @endif
    </div>

    <!-- Filter Card -->
    <div class="card custom-card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ ($student360 ?? false) ? route('students.student-360.index') : route('students.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by name, roll number, admission no..." value="{{ request('search') }}">
                    </div>
                </div>
                @if($student360 ?? false)
                <div class="col-md-3">
                    <select name="program_id" class="form-select form-select-sm">
                        <option value="">All Programmes</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>{{ $program->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="lifecycle_status" class="form-select form-select-sm">
                        <option value="">All Lifecycle Statuses</option>
                        @foreach(['active', 'leave', 'suspended', 'withdrawn', 'graduated', 'dismissed'] as $lifecycleStatus)
                            <option value="{{ $lifecycleStatus }}" {{ request('lifecycle_status') === $lifecycleStatus ? 'selected' : '' }}>{{ ucfirst($lifecycleStatus) }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <div class="col-md-3">
                    <select name="class_id" class="form-select form-select-sm">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                        <option value="expelled" {{ request('status') == 'expelled' ? 'selected' : '' }}>Expelled</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-navy flex-grow-1">Filter</button>
                    @if(request()->hasAny(['search', 'class_id', 'status']))
                        <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filters"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card custom-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Official Student ID</th>
                        <th>Student Name</th>
                        <th>Roll No</th>
                        <th>Class & Section</th>
                        <th>Parent Contact</th>
                        <th>Admission Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td><code class="fw-semibold">{{ $student->student_id }}</code></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $student->user->avatar_url }}" class="rounded-circle me-3 border" width="40" height="40" alt="">
                                    <div>
                                        <a href="{{ route('students.show', $student) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $student->user->name }}
                                        </a>
                                        <div class="small text-muted">{{ $student->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-secondary-subtle text-dark border">{{ $student->roll_number }}</span></td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $student->schoolClass->name }}</span>
                                <span class="text-muted small">({{ $student->section->name }})</span>
                            </td>
                            <td>
                                <div>{{ $student->parent_name ?? '—' }}</div>
                                <small class="text-muted">{{ $student->parent_phone ?? $student->user->phone ?? '—' }}</small>
                            </td>
                            <td>{{ $student->admission_date ? $student->admission_date->format('M d, Y') : '—' }}</td>
                            <td>{!! $student->status_badge !!}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route(($student360 ?? false) ? 'students.student-360' : 'students.show', $student) }}" class="btn btn-outline-secondary" title="{{ ($student360 ?? false) ? 'Open Student 360' : 'View Profile' }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                                        <a href="{{ route('students.edit', $student) }}" class="btn btn-outline-primary" title="Edit Student">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()->isAdmin())
                                        <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline" data-confirm="Are you sure you want to delete this student?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete Student">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                                No students found. Click "Add New Student" to enroll one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
