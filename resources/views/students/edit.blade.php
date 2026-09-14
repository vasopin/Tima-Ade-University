@extends('layouts.app')

@section('title', 'Edit Student')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Edit Student</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-header-title mb-1">Edit Student: {{ $student->user->name }}</h3>
            <p class="text-muted small mb-0">Update profile details, class assignments, and status.</p>
        </div>
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <form method="POST" action="{{ route('students.update', $student) }}" class="needs-validation" novalidate>
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Account & Basic Info -->
            <div class="col-lg-6">
                <div class="card custom-card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-person-fill me-2 text-danger"></i>Basic & Account Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $student->user->name) }}" required @readonly(auth()->user()->isStaff())>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $student->user->email) }}" required @readonly(auth()->user()->isStaff())>
                            </div>
                            @if(auth()->user()->isAdmin())
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Change Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                            </div>
                            @endif
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Student Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->user->phone) }}" @readonly(auth()->user()->isStaff())>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $student->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Blood Group</label>
                                <select name="blood_group" class="form-select">
                                    <option value="">Select Blood Group</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                        <option value="{{ $bg }}" {{ old('blood_group', $student->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Residential Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $student->address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic & Parent Info -->
            <div class="col-lg-6">
                <div class="card custom-card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-mortarboard-fill me-2 text-primary"></i>Academic Enrollment</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Roll Number</label>
                                <input type="text" name="roll_number" class="form-control" value="{{ old('roll_number', $student->roll_number) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Admission Number</label>
                                <input type="text" class="form-control bg-light" value="{{ $student->admission_number }}" disabled>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Class</label>
                                <select name="school_class_id" id="classSelect" class="form-select" required>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('school_class_id', $student->school_class_id) == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Section</label>
                                <select name="section_id" id="sectionSelect" class="form-select" required>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}" {{ old('section_id', $student->section_id) == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Admission Date</label>
                                <input type="date" name="admission_date" class="form-control" value="{{ old('admission_date', $student->admission_date?->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Status</label>
                                <select name="status" class="form-select" required>
                                    @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'graduated' => 'Graduated', 'expelled' => 'Expelled'] as $key => $label)
                                        <option value="{{ $key }}" {{ old('status', $student->status) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guardian Info -->
                <div class="card custom-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-people-fill me-2 text-success"></i>Parent / Guardian Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Parent / Guardian Name</label>
                            <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name', $student->parent_name) }}">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Parent Phone</label>
                                <input type="text" name="parent_phone" class="form-control" value="{{ old('parent_phone', $student->parent_phone) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Parent Email</label>
                                <input type="email" name="parent_email" class="form-control" value="{{ old('parent_email', $student->parent_email) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <a href="{{ route('students.index') }}" class="btn btn-light me-2">Cancel</a>
            <button type="submit" class="btn btn-crimson px-4 py-2 fw-semibold" data-loading-text="<span class=\"spinner-border spinner-border-sm spinner-border-sm-custom\" role=\"status\" aria-hidden=\"true\"></span> Updating...">
                <i class="bi bi-check-lg me-1"></i> Update Student
            </button>
        </div>
    </form>
</div>
@endsection
