@extends('layouts.app')

@section('title', 'Enter Marks — ' . $exam->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('exams.index') }}">Examinations</a></li>
    <li class="breadcrumb-item"><a href="{{ route('exams.show', $exam) }}">{{ $exam->name }}</a></li>
    <li class="breadcrumb-item active">Enter Marks</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title mb-1">Enter Exam Marks: {{ $exam->name }}</h3>
            <p class="text-muted small mb-0">Class: <strong>{{ $exam->schoolClass->name }}</strong> | Subject: <strong>{{ $exam->subject->name }}</strong> | Total Marks: <strong>{{ $exam->total_marks }}</strong></p>
        </div>
        <a href="{{ route('exams.show', $exam) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Exam
        </a>
    </div>

    <form method="POST" action="{{ route('exams.marks.save', $exam) }}">
        @csrf

        <div class="card custom-card mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-pencil me-2 text-danger"></i>Student Mark Sheet</h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="fillFullMarks()">Set All Full Marks</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllMarks()">Clear All</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 120px;">Roll No</th>
                            <th>Student</th>
                            <th style="width: 180px;">Marks Obtained (Max: {{ $exam->total_marks }})</th>
                            <th style="width: 120px;">Absent?</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            @php
                                $val = old("marks.{$student->id}", $existingMarks[$student->id] ?? '');
                                $isAbs = old("absent.{$student->id}", isset($absentStudents[$student->id]));
                            @endphp
                            <tr>
                                <td><span class="badge bg-light text-dark">{{ $student->roll_number }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $student->user->avatar_url }}" class="rounded-circle me-2" width="32" height="32" alt="">
                                        <div>
                                            <div class="fw-semibold">{{ $student->user->name }}</div>
                                            <small class="text-muted">{{ $student->admission_number }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.5" min="0" max="{{ $exam->total_marks }}"
                                               name="marks[{{ $student->id }}]" 
                                               id="marks_{{ $student->id }}"
                                               class="form-control mark-input" 
                                               value="{{ $val }}"
                                               placeholder="0 - {{ $exam->total_marks }}"
                                               {{ $isAbs ? 'disabled' : '' }}>
                                        <span class="input-group-text">/ {{ $exam->total_marks }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input absent-switch" type="checkbox" 
                                               name="absent[{{ $student->id }}]" 
                                               id="absent_{{ $student->id }}"
                                               value="1" 
                                               onchange="toggleAbsent({{ $student->id }})"
                                               {{ $isAbs ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="absent_{{ $student->id }}">Absent</label>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" name="remarks[{{ $student->id }}]" class="form-control form-control-sm" placeholder="Optional notes">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No students enrolled in this class.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white p-3 d-flex justify-content-between align-items-center">
                <span class="text-muted small">Scores are validated server-side and computed into letter grades. Workflow: <strong>{{ ucfirst($exam->workflow_status ?? 'draft') }}</strong>.</span>
                <div class="d-flex gap-2"><button type="submit" class="btn btn-outline-primary px-4"><i class="bi bi-save me-1"></i> Save Marks</button>@if(in_array($exam->workflow_status ?? 'draft', ['draft', 'rejected'], true))<button type="submit" form="submit-results-form" class="btn btn-crimson px-4"><i class="bi bi-send me-1"></i> Submit Results</button>@endif</div>
            </div>
        </div>
    </form>
    @if(in_array($exam->workflow_status ?? 'draft', ['draft', 'rejected'], true))
        <form id="submit-results-form" method="POST" action="{{ route('exams.submit-results', $exam) }}">@csrf</form>
    @endif
</div>

@push('scripts')
<script>
function toggleAbsent(id) {
    const input = document.getElementById('marks_' + id);
    const cb = document.getElementById('absent_' + id);
    if (cb.checked) {
        input.value = '0';
        input.disabled = true;
    } else {
        input.disabled = false;
    }
}

function fillFullMarks() {
    const maxMarks = {{ $exam->total_marks }};
    document.querySelectorAll('.mark-input').forEach(input => {
        if (!input.disabled) {
            input.value = maxMarks;
        }
    });
}

function clearAllMarks() {
    document.querySelectorAll('.mark-input').forEach(input => {
        input.value = '';
    });
}
</script>
@endpush
@endsection
