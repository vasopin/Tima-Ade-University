@extends('layouts.app')

@section('title', 'Dean '.ucfirst($type))
@section('breadcrumb')<li class="breadcrumb-item active">{{ ucfirst($type) }}</li>@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="welcome-banner mb-4"><h1 class="welcome-title">{{ $faculty->name }} Faculty — {{ ucfirst($type) }}</h1><p class="welcome-text mb-0">Faculty-scoped academic oversight. Results are limited to your assigned Faculty.</p></div>
    <form method="GET" class="card custom-card border-0 shadow-sm mb-4"><div class="card-body row g-2">
        <div class="col-md-5"><label class="visually-hidden" for="search">Search</label><input id="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search {{ $type }}"></div>
        @if(in_array($type, ['programs','courses','students']))<div class="col-md-3"><select class="form-select" name="department_id"><option value="">All departments</option>@foreach($departments as $department)<option value="{{ $department->id }}" @selected((string)request('department_id') === (string)$department->id)>{{ $department->name }}</option>@endforeach</select></div>@endif
        @if($type === 'students')<div class="col-md-3"><select class="form-select" name="program_id"><option value="">All programs</option>@foreach($programs as $program)<option value="{{ $program->id }}" @selected((string)request('program_id') === (string)$program->id)>{{ $program->name }}</option>@endforeach</select></div>@endif
        @if($type === 'students')<div class="col-md-2"><select class="form-select" name="status"><option value="">All statuses</option>@foreach(['active','inactive','graduated','expelled'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>@endif
        <div class="col-auto"><button class="btn btn-primary">Filter</button></div>
    </div></form>
    <div class="card custom-card"><div class="card-body"><div class="table-responsive"><table class="table align-middle"><thead><tr>
        @if($type === 'departments')<th>Department</th><th>Head</th><th>Programs</th><th>Courses</th>
        @elseif($type === 'programs')<th>Program</th><th>Department</th><th>Degree</th><th>Students</th><th>Status</th>
        @elseif($type === 'courses')<th>Course</th><th>Department</th><th>Credits</th><th>Sections</th><th>Prerequisites</th>
        @elseif($type === 'sections')<th>Section</th><th>Course</th><th>Term</th><th>Instructor</th><th>Capacity</th><th>Enrollment</th>
        @elseif($type === 'staff')<th>Instructor</th><th>Email</th><th>Employee ID</th><th>Assigned sections</th>
        @else<th>Name</th><th>Student ID</th><th>Program</th><th>Department</th><th>Enrollment records</th><th>Status</th>@endif
    </tr></thead><tbody>
    @forelse($items as $item)<tr>
        @if($type === 'departments')<td><strong>{{ $item->name }}</strong><small class="d-block text-muted">{{ $item->code }}</small></td><td>{{ $item->head?->name ?? 'Not assigned' }}</td><td>{{ $item->programs_count }}</td><td>{{ $item->subjects_count }}</td>
        @elseif($type === 'programs')<td><strong>{{ $item->name }}</strong><small class="d-block text-muted">{{ $item->code }}</small></td><td>{{ $item->department?->name }}</td><td>{{ $item->degree_type }}</td><td>{{ $item->students_count }}</td><td>{{ $item->is_active ? 'Active' : 'Inactive' }}</td>
        @elseif($type === 'courses')<td><strong>{{ $item->name }}</strong><small class="d-block text-muted">{{ $item->code }}</small></td><td>{{ $item->department?->name }}</td><td>{{ $item->credits ?? '—' }}</td><td>{{ $item->course_sections_count }}</td><td>{{ $item->prerequisites->count() }}</td>
        @elseif($type === 'sections')<td>{{ $item->code }}</td><td>{{ $item->course?->name }}</td><td>{{ $item->term?->name }}</td><td>{{ $item->teacher?->name ?? 'Unassigned' }}</td><td>{{ $item->capacity ?? '—' }}</td><td>{{ $item->enrollments_count }}</td>
        @elseif($type === 'staff')<td>{{ $item->name }}</td><td>{{ $item->email }}</td><td>{{ $item->teacher?->employee_id ?? '—' }}</td><td>{{ $item->assigned_sections_count }}</td>
        @else<td>{{ $item->user?->name }}</td><td>{{ $item->student_id ?? $item->admission_number }}</td><td>{{ $item->program?->name ?? 'Not assigned' }}</td><td>{{ $item->program?->department?->name ?? 'Not assigned' }}</td><td>{{ $item->enrollments_count }}</td><td>{{ ucfirst($item->status) }}</td>@endif
    </tr>@empty<tr><td colspan="8" class="text-center text-muted py-4">No faculty records match the selected filters.</td></tr>@endforelse
    </tbody></table></div>{{ $items->links() }}</div></div>
</div>
@endsection
