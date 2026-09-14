@extends('layouts.app')

@section('title', 'Examinations')

@section('breadcrumb')
    <li class="breadcrumb-item active">Examinations</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title mb-1">Examinations Management</h3>
            <p class="text-muted small mb-0">Schedule exams, record marks, and generate academic result sheets.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('exams.results') }}" class="btn btn-navy">
                <i class="bi bi-file-earmark-text me-1"></i> Student Results
            </a>
            <a href="{{ route('exams.create') }}" class="btn btn-crimson">
                <i class="bi bi-plus-circle me-1"></i> Schedule Exam
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card custom-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('exams.index') }}" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small text-muted">Filter by Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small text-muted">Filter by Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-navy w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Exams Table -->
    <div class="card custom-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Exam Name</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Date & Time</th>
                        <th>Total / Pass</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr>
                            <td>
                                <a href="{{ route('exams.show', $exam) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $exam->name }}
                                </a>
                                <span class="badge bg-light text-secondary ms-1 text-uppercase">{{ $exam->exam_type }}</span>
                            </td>
                            <td><span class="badge bg-info-subtle text-info fw-semibold">{{ $exam->schoolClass->name }}</span></td>
                            <td>{{ $exam->subject->name }}</td>
                            <td>
                                <div><i class="bi bi-calendar-event me-1 text-muted"></i>{{ $exam->exam_date->format('M d, Y') }}</div>
                                @if($exam->start_time)
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $exam->start_time->format('h:i A') }} ({{ $exam->duration_minutes }}m)</small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $exam->total_marks }}</strong> / <span class="text-muted small">{{ $exam->pass_marks }} pass</span>
                            </td>
                            <td>{!! $exam->status_badge !!}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('exams.marks', $exam) }}" class="btn btn-outline-success" title="Enter Marks">
                                        <i class="bi bi-pencil-square me-1"></i>Marks
                                    </a>
                                    <a href="{{ route('exams.show', $exam) }}" class="btn btn-outline-secondary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('exams.edit', $exam) }}" class="btn btn-outline-primary" title="Edit Exam">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('exams.destroy', $exam) }}" class="d-inline" onsubmit="return confirm('Delete this exam?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                                No examinations scheduled yet. Click "Schedule Exam" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($exams->hasPages())
            <div class="card-footer bg-white">
                {{ $exams->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
