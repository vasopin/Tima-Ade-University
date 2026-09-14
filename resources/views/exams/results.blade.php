@extends('layouts.app')

@section('title', 'Student Academic Results')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Examinations</a></li>
    <li class="breadcrumb-item active">Results & Transcripts</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title mb-1">Student Academic Transcripts & Results</h3>
            <p class="text-muted small mb-0">Search and view comprehensive examination transcripts per student.</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary d-none d-md-inline-flex align-items-center">
            <i class="bi bi-printer me-1"></i> Print Transcript
        </button>
    </div>

    <!-- Student Selector -->
    <div class="card custom-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('exams.results') }}" class="row g-3 align-items-end">
                <div class="col-md-9">
                    <label class="form-label fw-semibold">Select Student</label>
                    <select name="student_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Choose a Student to view academic records --</option>
                        @foreach($classes as $class)
                            <optgroup label="{{ $class->name }}">
                                @foreach($class->students as $student)
                                    <option value="{{ $student->id }}" {{ $studentId == $student->id ? 'selected' : '' }}>
                                        {{ $student->roll_number }} — {{ $student->user->name }} ({{ $class->name }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-navy w-100"><i class="bi bi-search me-1"></i>View Transcript</button>
                </div>
            </form>
        </div>
    </div>

    @if($studentId && count($results) > 0)
        @php
            $targetStudent = \App\Models\Student::with(['user', 'schoolClass', 'section'])->find($studentId);
        @endphp
        <!-- Student Header -->
        <div class="card custom-card mb-4 bg-light border">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-4">
                    <img src="{{ $targetStudent->user->avatar_url }}" class="rounded-circle border shadow-sm" width="80" height="80" alt="">
                    <div>
                        <h4 class="fw-bold mb-1">{{ $targetStudent->user->name }}</h4>
                        <div class="d-flex flex-wrap gap-3 text-muted small">
                            <span><i class="bi bi-person-badge me-1"></i>Roll: <strong>{{ $targetStudent->roll_number }}</strong></span>
                            <span><i class="bi bi-card-text me-1"></i>Admission: <strong>{{ $targetStudent->admission_number }}</strong></span>
                            <span><i class="bi bi-building me-1"></i>Class: <strong>{{ $targetStudent->schoolClass->name }} ({{ $targetStudent->section->name }})</strong></span>
                            <span><i class="bi bi-envelope me-1"></i>{{ $targetStudent->user->email }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marks Breakdown by Subject -->
        <div class="card custom-card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="bi bi-journal-check me-2 text-danger"></i>Subject Performance & Exam Marks</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="text-start">Subject</th>
                            <th>Exam Name</th>
                            <th>Exam Type</th>
                            <th>Exam Date</th>
                            <th>Marks Obtained</th>
                            <th>Max Marks</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandObtained = 0;
                            $grandTotal = 0;
                        @endphp
                        @foreach($results as $subjectName => $marksList)
                            @foreach($marksList as $mark)
                                @php
                                    $pct = $mark->exam->total_marks > 0 ? round(($mark->marks_obtained / $mark->exam->total_marks) * 100, 1) : 0;
                                    if (!$mark->is_absent) {
                                        $grandObtained += $mark->marks_obtained;
                                        $grandTotal += $mark->exam->total_marks;
                                    }
                                @endphp
                                <tr class="text-center">
                                    <td class="text-start fw-bold">{{ $subjectName }}</td>
                                    <td>{{ $mark->exam->name }}</td>
                                    <td><span class="badge bg-light text-dark text-uppercase">{{ $mark->exam->exam_type }}</span></td>
                                    <td>{{ $mark->exam->exam_date->format('M d, Y') }}</td>
                                    <td class="fw-bold">{{ $mark->is_absent ? '0 (Absent)' : $mark->marks_obtained }}</td>
                                    <td>{{ $mark->exam->total_marks }}</td>
                                    <td>{{ $pct }}%</td>
                                    <td><span class="badge bg-primary fs-6">{{ $mark->is_absent ? 'F' : $mark->grade }}</span></td>
                                    <td>
                                        @if($mark->is_absent)
                                            <span class="badge bg-danger">Absent</span>
                                        @elseif($mark->marks_obtained >= $mark->exam->pass_marks)
                                            <span class="badge bg-success">Pass</span>
                                        @else
                                            <span class="badge bg-danger">Fail</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold text-center">
                        <tr>
                            <td colspan="4" class="text-end">Grand Total / Overall GPA:</td>
                            <td>{{ $grandObtained }}</td>
                            <td>{{ $grandTotal }}</td>
                            <td>{{ $grandTotal > 0 ? round(($grandObtained / $grandTotal) * 100, 1) : 0 }}%</td>
                            <td colspan="2">
                                @php
                                    $overallPct = $grandTotal > 0 ? ($grandObtained / $grandTotal) * 100 : 0;
                                    $overallGrade = \App\Models\ExamMark::computeGrade($grandObtained, $grandTotal);
                                @endphp
                                <span class="badge bg-success fs-6">Overall Grade: {{ $overallGrade }}</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @elseif($studentId)
        <div class="card custom-card text-center py-5">
            <i class="bi bi-file-earmark-x fs-1 text-muted mb-2"></i>
            <h5>No Exam Records Found</h5>
            <p class="text-muted small">This student does not have any recorded examination marks yet.</p>
        </div>
    @else
        <div class="card custom-card text-center py-5 text-muted">
            <i class="bi bi-mortarboard fs-1 mb-2 text-secondary"></i>
            <h5>Please select a student from the dropdown above</h5>
            <p class="small">The academic transcript and subject marks breakdown will appear here.</p>
        </div>
    @endif
</div>
@endsection
