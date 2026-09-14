@extends('layouts.app')

@section('title', $title . ' — Executive Oversight')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Executive Oversight</a></li>
    <li class="breadcrumb-item active">{{ $title }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <h1 class="h3 mb-1">{{ $title }}</h1>
        <p class="text-muted mb-0">{{ $description }}</p>
    </div>

    <div class="card custom-card">
        <div class="card-body">
            @if($type === 'students')
                <form method="GET" class="row g-2 mb-3">
                    <div class="col-md-6"><label class="visually-hidden" for="search">Search students</label><input id="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or student number"></div>
                    <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Search</button></div>
                </form>
            @endif
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        @if($type === 'faculties') <tr><th>Faculty</th><th>Departments</th><th>Programs</th><th>Students</th></tr>
                        @elseif($type === 'departments') <tr><th>Department</th><th>Faculty</th><th>Head</th><th>Programs</th><th>Courses</th><th>Students</th></tr>
                        @elseif($type === 'programs') <tr><th>Program</th><th>Department</th><th>Faculty</th><th>Degree</th><th>Students</th></tr>
                        @elseif($type === 'students') <tr><th>Student</th><th>Student number</th><th>Program</th><th>Department</th><th>Faculty</th><th>Status</th><th>Enrollments</th></tr>
                        @else <tr><th>Faculty</th><th>Department</th><th>Program</th><th>Assessments</th><th>Average mark</th><th>Failed</th></tr>
                        @endif
                    </thead>
                    <tbody>
                    @forelse($items as $item)
                        @if($type === 'faculties')
                            <tr><td>{{ $item->name }}</td><td>{{ $item->active_departments_count }}</td><td>{{ $item->programs_count }}</td><td>{{ $item->students_count }}</td></tr>
                        @elseif($type === 'departments')
                            <tr><td>{{ $item->name }}</td><td>{{ $item->faculty?->name ?? '—' }}</td><td>{{ $item->head?->name ?? '—' }}</td><td>{{ $item->active_programs_count }}</td><td>{{ $item->subjects_count }}</td><td>{{ $item->students_count }}</td></tr>
                        @elseif($type === 'programs')
                            <tr><td>{{ $item->name }}</td><td>{{ $item->department?->name ?? '—' }}</td><td>{{ $item->department?->faculty?->name ?? '—' }}</td><td>{{ $item->degree_type ?? '—' }}</td><td>{{ $item->students_count }}</td></tr>
                        @elseif($type === 'students')
                            <tr><td>{{ $item->user?->name ?? '—' }}</td><td>{{ $item->student_id ?? $item->admission_number ?? '—' }}</td><td>{{ $item->program?->name ?? '—' }}</td><td>{{ $item->program?->department?->name ?? '—' }}</td><td>{{ $item->program?->department?->faculty?->name ?? '—' }}</td><td>{{ ucfirst($item->status ?? 'unknown') }}</td><td>{{ $item->enrollments_count }}</td></tr>
                        @else
                            <tr><td>{{ $item->faculty_name }}</td><td>{{ $item->department_name }}</td><td>{{ $item->program_name }}</td><td>{{ $item->assessment_count }}</td><td>{{ $item->average_mark }}%</td><td>{{ $item->failed_count }}</td></tr>
                        @endif
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No authoritative records are available.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($type === 'students') <div class="mt-3">{{ $items->links() }}</div> @endif
        </div>
    </div>
</div>
@endsection
