@extends('layouts.app')

@section('title', 'Teacher Attendance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Attendance</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="text-uppercase small text-primary fw-semibold mb-1">University Portal</div>
            <h2 class="mb-0">Attendance</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('attendance.index', ['class_id' => $classId, 'date' => $date]) }}" class="btn btn-outline-secondary">
                <i class="bi bi-list-check me-1"></i> Daily Attendance Register
            </a>
            <a href="{{ route('attendance.report') }}" class="btn btn-outline-secondary">
                <i class="bi bi-bar-chart me-1"></i> Attendance Reports
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('teacher.attendance') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Select class</option>
                        @foreach($teacherClasses as $class)
                            <option value="{{ $class->id }}" {{ (string) $classId === (string) $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}" onchange="this.form.submit()">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Section</label>
                    <select name="section_id" class="form-select" onchange="this.form.submit()">
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ (string) $sectionId === (string) $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('teacher.attendance') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if($selectedClass)
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h5 class="mb-1">{{ $selectedClass->name }}</h5>
                        <small class="text-muted">{{ $date }}</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">{{ $students->count() }} students</span>
                </div>

                @if($students->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-1 mb-3 d-block"></i>
                        <h6>No students are enrolled in this class.</h6>
                    </div>
                @else
                    <form method="POST" action="{{ route('attendance.store') }}">
                        @csrf
                        <input type="hidden" name="attendance_date" value="{{ $date }}">
                        <input type="hidden" name="school_class_id" value="{{ $selectedClass->id }}">
                        <input type="hidden" name="section_id" value="{{ $sectionId }}">

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Roll number</th>
                                        <th>Section</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                        @php
                                            $existing = $student->attendances->first();
                                            $currentStatus = $existing?->status ?? 'present';
                                        @endphp
                                        <tr>
                                            <td>{{ $student->user?->name ?? 'Student' }}</td>
                                            <td>{{ $student->roll_number ?? '-' }}</td>
                                            <td>{{ $student->section?->name ?? '-' }}</td>
                                            <td>
                                                <select name="attendance[{{ $student->id }}]" class="form-select form-select-sm">
                                                    @foreach(['present', 'absent', 'late', 'excused'] as $status)
                                                        <option value="{{ $status }}" {{ $currentStatus === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary">Save attendance</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x fs-1 text-muted mb-3 d-block"></i>
                <h5 class="mb-2">No classes are available for attendance tracking.</h5>
                <p class="text-muted mb-0">Select a class from your assigned teaching list to mark attendance.</p>
            </div>
        </div>
    @endif
</div>
@endsection
