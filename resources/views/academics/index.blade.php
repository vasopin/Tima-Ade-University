@extends('layouts.app')

@section('title', 'Academic Foundation')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Academic Foundation</h1>
        <p class="text-muted mb-0">Campus, organization, catalogue, calendar, curriculum, and course-section records.</p>
    </div>
    <div class="row g-3">
        @foreach ([
            ['Campuses', $campuses],
            ['Faculties / Schools', $faculties],
            ['Departments', $departments],
            ['Programs', $programs],
            ['Academic Years', $years],
            ['Terms', $terms],
            ['Courses (Subjects)', $courses],
            ['Curricula', $curricula],
            ['Course Sections', $sections],
        ] as [$title, $records])
            <div class="col-12 col-md-6 col-xl-4">
                <section class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white"><h2 class="h6 mb-0">{{ $title }}</h2></div>
                    <div class="card-body">
                        <div class="display-6 fw-bold">{{ $records->count() }}</div>
                        <p class="text-muted small mb-0">Existing records available for academic administration.</p>
                    </div>
                </section>
            </div>
        @endforeach
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isRegistrar())
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white">
                <h2 class="h5 mb-1">Configure academic programs</h2>
                <p class="text-muted small mb-0">Create the academic hierarchy first, then activate programs here for public applications.</p>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <form method="POST" action="{{ route('admin.academics.campuses.store') }}" class="col-12 col-lg-3">
                        @csrf
                        <h3 class="h6">1. Campus</h3>
                        <input name="name" class="form-control mb-2" placeholder="Campus name" required>
                        <input name="code" class="form-control mb-2" placeholder="Code" required>
                        <button class="btn btn-outline-primary btn-sm" type="submit">Create campus</button>
                    </form>
                    <form method="POST" action="{{ route('admin.academics.faculties.store') }}" class="col-12 col-lg-3">
                        @csrf
                        <h3 class="h6">2. Faculty</h3>
                        <select name="campus_id" class="form-select mb-2">
                            <option value="">No campus</option>
                            @foreach($campuses as $campus)<option value="{{ $campus->id }}">{{ $campus->name }}</option>@endforeach
                        </select>
                        <input name="name" class="form-control mb-2" placeholder="Faculty name" required>
                        <input name="code" class="form-control mb-2" placeholder="Code" required>
                        <button class="btn btn-outline-primary btn-sm" type="submit">Create faculty</button>
                    </form>
                    <form method="POST" action="{{ route('admin.academics.departments.store') }}" class="col-12 col-lg-3">
                        @csrf
                        <h3 class="h6">3. Department</h3>
                        <select name="faculty_id" class="form-select mb-2" required>
                            <option value="">Select faculty</option>
                            @foreach($faculties as $faculty)<option value="{{ $faculty->id }}">{{ $faculty->name }}</option>@endforeach
                        </select>
                        <input name="name" class="form-control mb-2" placeholder="Department name" required>
                        <input name="code" class="form-control mb-2" placeholder="Code" required>
                        <button class="btn btn-outline-primary btn-sm" type="submit">Create department</button>
                    </form>
                    <form method="POST" action="{{ route('admin.academics.programs.store') }}" class="col-12 col-lg-3">
                        @csrf
                        <h3 class="h6">4. Program</h3>
                        <select name="department_id" class="form-select mb-2" required>
                            <option value="">Select department</option>
                            @foreach($departments as $department)<option value="{{ $department->id }}">{{ $department->name }}</option>@endforeach
                        </select>
                        <input name="name" class="form-control mb-2" placeholder="Program name" required>
                        <input name="code" class="form-control mb-2" placeholder="Unique code" required>
                        <input name="degree_type" class="form-control mb-2" placeholder="Degree type" required>
                        <input name="duration_years" type="number" min="1" max="20" class="form-control mb-2" placeholder="Duration (years)">
                        <label class="form-check mb-2"><input type="checkbox" name="is_active" value="1" class="form-check-input" checked> <span class="form-check-label">Available for applications</span></label>
                        <button class="btn btn-primary btn-sm" type="submit">Create program</button>
                    </form>
                </div>
                @if($programs->isNotEmpty())
                    <hr class="my-4">
                    <h3 class="h6">Existing programs</h3>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>Name</th><th>Code</th><th>Department</th><th>Degree</th><th>Public admission</th><th></th></tr></thead>
                            <tbody>
                                @foreach($programs as $program)
                                    <tr>
                                        <form method="POST" action="{{ route('admin.academics.programs.update', $program) }}">
                                            @csrf @method('PUT')
                                            <td><input name="name" value="{{ $program->name }}" class="form-control form-control-sm" required></td>
                                            <td><input name="code" value="{{ $program->code }}" class="form-control form-control-sm" required></td>
                                            <td><select name="department_id" class="form-select form-select-sm" required>@foreach($departments as $department)<option value="{{ $department->id }}" @selected($program->department_id === $department->id)>{{ $department->name }}</option>@endforeach</select></td>
                                            <td><input name="degree_type" value="{{ $program->degree_type }}" class="form-control form-control-sm" required></td>
                                            <td><label class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" @checked($program->is_active)> <span class="form-check-label">{{ $program->is_active ? 'Active' : 'Archived' }}</span></label></td>
                                            <td><button class="btn btn-outline-primary btn-sm" type="submit">Save</button></td>
                                        </form>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted small mb-0">Archived programs remain in the catalogue but are excluded from Apply Now. Activate only programs currently accepting applications.</p>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
