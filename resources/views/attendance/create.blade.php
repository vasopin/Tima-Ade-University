@extends('layouts.app')

@section('title', 'Mark Attendance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}">Attendance</a></li>
    <li class="breadcrumb-item active">Mark Attendance</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="page-header-title mb-1">Mark Classroom Attendance</h3>
            <p class="text-muted small mb-0">Select class and section to load students and record attendance.</p>
        </div>
        <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to History
        </a>
    </div>

    <!-- Class Selector Bar -->
    <div class="card custom-card mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('attendance.create') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label required fw-semibold">Attendance Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label required fw-semibold">Class</label>
                    <select name="class_id" id="classSelect" class="form-select" required onchange="this.form.submit()">
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label required fw-semibold">Section</label>
                    <select name="section_id" class="form-select" required onchange="this.form.submit()">
                        <option value="">Select Section</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-navy w-100 fw-semibold">
                        <i class="bi bi-arrow-clockwise me-1"></i> Load List
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($classId && $sectionId)
        @if($students->isNotEmpty())
            <form method="POST" action="{{ route('attendance.store') }}">
                @csrf
                <input type="hidden" name="attendance_date" value="{{ $date }}">
                <input type="hidden" name="school_class_id" value="{{ $classId }}">
                <input type="hidden" name="section_id" value="{{ $sectionId }}">

                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-people-fill me-2 text-danger"></i>Student Roll Call ({{ $students->count() }} enrolled)
                        </h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="markAll('present')">All Present</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAll('absent')">All Absent</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Roll #</th>
                                    <th>Student Name</th>
                                    <th class="text-center" style="width: 320px;">Attendance Status</th>
                                    <th>Remarks (Optional)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    @php
                                        $currentStatus = $student->attendances->first()?->status ?? 'present';
                                    @endphp
                                    <tr>
                                        <td><span class="badge bg-light text-dark border">{{ $student->roll_number }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $student->user->avatar_url }}" class="rounded-circle me-2" width="36" height="36" alt="">
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ $student->user->name }}</div>
                                                    <small class="text-muted">{{ $student->admission_number }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group w-100" role="group">
                                                <input type="radio" class="btn-check status-radio status-present" name="attendance[{{ $student->id }}]" id="pres_{{ $student->id }}" value="present" {{ $currentStatus == 'present' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success btn-sm" for="pres_{{ $student->id }}">Present</label>

                                                <input type="radio" class="btn-check status-radio status-absent" name="attendance[{{ $student->id }}]" id="abs_{{ $student->id }}" value="absent" {{ $currentStatus == 'absent' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-danger btn-sm" for="abs_{{ $student->id }}">Absent</label>

                                                <input type="radio" class="btn-check status-radio status-late" name="attendance[{{ $student->id }}]" id="late_{{ $student->id }}" value="late" {{ $currentStatus == 'late' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-warning btn-sm" for="late_{{ $student->id }}">Late</label>

                                                <input type="radio" class="btn-check status-radio status-excused" name="attendance[{{ $student->id }}]" id="exc_{{ $student->id }}" value="excused" {{ $currentStatus == 'excused' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-info btn-sm" for="exc_{{ $student->id }}">Excused</label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="remarks[{{ $student->id }}]" class="form-control form-control-sm" placeholder="e.g. Doctor appointment" value="{{ $student->attendances->first()?->remarks }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white p-3 text-end">
                        <button type="submit" class="btn btn-crimson px-5 py-2 fw-semibold shadow-sm">
                            <i class="bi bi-check-circle-fill me-2"></i> Save Daily Attendance
                        </button>
                    </div>
                </div>
            </form>
        @else
            <div class="card custom-card text-center py-5">
                <i class="bi bi-person-x fs-1 text-secondary mb-2"></i>
                <h5 class="text-muted">No students found enrolled in this section.</h5>
                <p class="small text-muted mb-3">Add students to this class and section first.</p>
                <a href="{{ route('students.create') }}" class="btn btn-crimson btn-sm">Enroll New Student</a>
            </div>
        @endif
    @else
        <div class="card custom-card text-center py-5">
            <i class="bi bi-arrow-up-circle fs-1 text-primary mb-2"></i>
            <h5 class="text-dark fw-bold">Select Class and Section Above</h5>
            <p class="text-muted small">Choose the academic class and section to load the student roll list for attendance.</p>
        </div>
    @endif
</div>

<script>
function markAll(status) {
    document.querySelectorAll(`.status-${status}`).forEach(el => el.checked = true);
}
</script>
@endsection
