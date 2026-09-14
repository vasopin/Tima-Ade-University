@extends('layouts.app')

@section('title', 'Course Registration')

@section('content')
<div class="container-fluid px-4 py-4 bg-light min-vh-100">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div><p class="text-uppercase text-muted small fw-semibold mb-1">Student portal</p><h1 class="h2 mb-1">Course registration</h1><p class="text-muted mb-0">{{ $term?->name ?? 'Current term' }} · Add, drop, or withdraw from sections.</p></div>
        <form method="get" class="d-flex gap-2"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search course"><button class="btn btn-outline-primary">Search</button></form>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="row g-4">
        <div class="col-12 col-xl-7"><section class="card border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h5 mb-0">Available sections</h2></div><div class="card-body">
            @forelse($sections as $section)
                <div class="border rounded p-3 mb-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div><h3 class="h6 mb-1">{{ $section->course->code }} — {{ $section->course->name }}</h3><p class="small text-muted mb-0">Section {{ $section->code }} · {{ $section->course->credits ?? '—' }} credits · {{ $section->active_enrollment_count }}{{ $section->capacity ? '/'.$section->capacity : '' }} seats</p></div>
                    <form method="post" action="{{ route('student.registration.store') }}">@csrf<input type="hidden" name="student_id" value="{{ $student->id }}"><input type="hidden" name="course_section_id" value="{{ $section->id }}"><button class="btn btn-sm btn-primary">Register</button></form>
                </div>
            @empty <p class="text-muted mb-0">No open sections are available for this term.</p>@endforelse
        </div></section></div>
        <div class="col-12 col-xl-5"><section class="card border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h5 mb-0">Your current enrollment</h2></div><div class="card-body">
            @forelse($current as $enrollment)
                <div class="border rounded p-3 mb-3"><div class="d-flex justify-content-between"><strong>{{ $enrollment->courseSection->course->code }}</strong><span class="badge text-bg-success">{{ ucfirst($enrollment->status) }}</span></div><p class="small mb-2">{{ $enrollment->courseSection->course->name }} · Section {{ $enrollment->courseSection->code }}</p><div class="d-flex gap-2"><form method="post" action="{{ route('student.enrollments.drop', $enrollment) }}">@csrf<button class="btn btn-sm btn-outline-warning">Drop</button></form><form method="post" action="{{ route('student.enrollments.withdraw', $enrollment) }}">@csrf<input type="hidden" name="reason" value="Student self-service withdrawal"><button class="btn btn-sm btn-outline-danger">Withdraw</button></form></div></div>
            @empty <p class="text-muted mb-0">You have no active enrollments.</p>@endforelse
        </div></section></div>
    </div>
</div>
@endsection
