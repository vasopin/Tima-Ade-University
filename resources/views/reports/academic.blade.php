@extends('layouts.app')

@section('title', 'Academic Performance Reports')

@section('breadcrumb')
    <li class="breadcrumb-item">Reports</li>
    <li class="breadcrumb-item active">Academic Report</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title mb-1">Class Academic Performance Matrix</h3>
            <p class="text-muted small mb-0">Cross-tabular mark sheet per class showing all completed exams and student grades.</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="bi bi-printer me-1"></i> Print Matrix
        </button>
    </div>

    <!-- Filter Card -->
    <div class="card custom-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.academic') }}" class="row g-3 align-items-end">
                <div class="col-md-9">
                    <label class="form-label fw-semibold">Select Class to View Full Matrix</label>
                    <select name="class_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Choose a Class --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClass == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-navy w-100"><i class="bi bi-bar-chart me-1"></i>Generate Matrix</button>
                </div>
            </form>
        </div>
    </div>

    @if($selectedClass && count($exams) > 0)
        <div class="card custom-card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="bi bi-table me-2 text-danger"></i>Class Examination Matrix: {{ \App\Models\SchoolClass::find($selectedClass)->name }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start">Roll No</th>
                            <th class="text-start">Student Name</th>
                            @foreach($exams as $exam)
                                <th>
                                    <div>{{ $exam->subject->name }}</div>
                                    <small class="text-muted fw-normal">Max: {{ $exam->total_marks }}</small>
                                </th>
                            @endforeach
                            <th>Overall %</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $row)
                            <tr>
                                <td class="text-start"><span class="badge bg-light text-dark">{{ $row['student']->roll_number }}</span></td>
                                <td class="text-start fw-semibold">{{ $row['student']->user->name }}</td>
                                @foreach($row['marks'] as $m)
                                    <td>
                                        @if($m === '-')
                                            <span class="text-muted">—</span>
                                        @else
                                            <strong>{{ $m }}</strong>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="fw-bold">{{ $row['total_pct'] }}%</td>
                                <td>
                                    @if($row['pass'])
                                        <span class="badge bg-success">Passed</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($exams) + 4 }}" class="text-center py-4 text-muted">No students found for this class.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($selectedClass)
        <div class="card custom-card text-center py-5">
            <i class="bi bi-clipboard-x fs-1 text-muted mb-2"></i>
            <h5>No Completed Exams Found</h5>
            <p class="text-muted small">There are no completed examinations with recorded marks for this class yet.</p>
        </div>
    @else
        <div class="card custom-card text-center py-5 text-muted">
            <i class="bi bi-layers fs-1 mb-2 text-secondary"></i>
            <h5>Please select a class from above</h5>
            <p class="small">The complete class performance matrix and results will appear here.</p>
        </div>
    @endif
</div>
@endsection
