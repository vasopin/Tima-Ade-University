@extends('layouts.app')

@section('title', $title . ' — Department Head')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ $department->name }} Department</a></li>
    <li class="breadcrumb-item active">{{ $title }}</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <h1 class="h3 mb-1">{{ $title }}</h1>
    <p class="text-muted mb-4">{{ $description }}</p>
    <div class="card custom-card"><div class="card-body">
        @if($type === 'students')
            <form method="GET" class="row g-2 mb-3"><div class="col-md-6"><label class="visually-hidden" for="search">Search students</label><input id="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or student number"></div><div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Search</button></div></form>
        @endif
        <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead>
            @if($type === 'programs') <tr><th>Program</th><th>Students</th><th>Curricula</th></tr>
            @elseif($type === 'courses') <tr><th>Course</th><th>Code</th><th>Credits</th><th>Sections</th></tr>
            @elseif($type === 'sections') <tr><th>Course</th><th>Section</th><th>Term</th><th>Instructor</th><th>Enrollments</th></tr>
            @elseif($type === 'students') <tr><th>Student</th><th>Student number</th><th>Program</th><th>Status</th><th>Enrollments</th></tr>
            @elseif($type === 'instructors') <tr><th>Instructor</th><th>Sections</th><th>Enrollments</th></tr>
            @elseif($type === 'performance') <tr><th>Course</th><th>Assessments</th><th>Average</th><th>Pass rate</th></tr>
            @else <tr><th>Student</th><th>Advisor</th><th>Assigned</th></tr>
            @endif
        </thead><tbody>
        @forelse($items as $item)
            @if($type === 'programs') <tr><td>{{ $item->name }}</td><td>{{ $item->students_count }}</td><td>{{ $item->curricula_count }}</td></tr>
            @elseif($type === 'courses') <tr><td>{{ $item->name }}</td><td>{{ $item->code }}</td><td>{{ $item->credits ?? '—' }}</td><td>{{ $item->course_sections_count }}</td></tr>
            @elseif($type === 'sections') <tr><td>{{ $item->course?->name ?? '—' }}</td><td>{{ $item->code }}</td><td>{{ $item->term?->name ?? '—' }}</td><td>{{ $item->teacher?->name ?? 'Unassigned' }}</td><td>{{ $item->enrollments_count }}</td></tr>
            @elseif($type === 'students') <tr><td>{{ $item->user?->name ?? '—' }}</td><td>{{ $item->student_id ?? $item->admission_number ?? '—' }}</td><td>{{ $item->program?->name ?? '—' }}</td><td>{{ ucfirst($item->status ?? 'unknown') }}</td><td>{{ $item->enrollments_count }}</td></tr>
            @elseif($type === 'instructors') <tr><td>{{ $item['teacher']?->name ?? '—' }}</td><td>{{ $item['sections'] }}</td><td>{{ $item['enrollments'] }}</td></tr>
            @elseif($type === 'performance') <tr><td>{{ $item['course']->name }}</td><td>{{ $item['assessments'] }}</td><td>{{ $item['average'] }}%</td><td>{{ $item['pass_rate'] }}%</td></tr>
            @else <tr><td>{{ $item->student?->user?->name ?? '—' }}</td><td>{{ $item->advisor?->name ?? '—' }}</td><td>{{ $item->assigned_at ?? '—' }}</td></tr>
            @endif
        @empty <tr><td colspan="6" class="text-center text-muted py-4">No authoritative records are available.</td></tr> @endforelse
        </tbody></table></div>
        @if($type === 'students') <div class="mt-3">{{ $items->links() }}</div> @endif
    </div></div>
</div>
@endsection
