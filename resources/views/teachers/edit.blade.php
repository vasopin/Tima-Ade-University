@extends('layouts.app')

@section('title', 'Edit Teacher')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('teachers.index') }}">Teachers</a></li>
    <li class="breadcrumb-item active">Edit Teacher</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-header-title mb-1">Edit Faculty: {{ $teacher->user->name }}</h3>
            <p class="text-muted small mb-0">Update credentials, faculty details, and assignments.</p>
        </div>
        <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <form method="POST" action="{{ route('teachers.update', $teacher) }}" class="needs-validation" novalidate>
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card custom-card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-person-fill me-2 text-danger"></i>Account & Personal Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required fw-semibold">Faculty Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $teacher->user->name) }}" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $teacher->user->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Change Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $teacher->user->phone) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $teacher->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $teacher->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $teacher->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $teacher->date_of_birth?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Emergency Contact</label>
                                <input type="text" name="emergency_contact" class="form-control" value="{{ old('emergency_contact', $teacher->emergency_contact) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Residential Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $teacher->address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card custom-card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-award-fill me-2 text-primary"></i>Professional & Employment Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required fw-semibold">Employee ID</label>
                            <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id', $teacher->employee_id) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Highest Qualification</label>
                            <input type="text" name="qualification" class="form-control" value="{{ old('qualification', $teacher->qualification) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Specialization / Subjects</label>
                            <input type="text" name="specialization" class="form-control" value="{{ old('specialization', $teacher->specialization) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Joining Date</label>
                            <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', $teacher->joining_date?->format('Y-m-d')) }}">
                        </div>

                        <div class="border-top pt-3 mt-3">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_class_teacher" value="1" id="isClassTeacher" {{ old('is_class_teacher', $teacher->is_class_teacher) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isClassTeacher">Designate as Class Teacher</label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Class Assigned as Lead Teacher</label>
                                <select name="class_teacher_of" class="form-select">
                                    <option value="">None / Not Assigned</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_teacher_of', $teacher->class_teacher_of) == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <a href="{{ route('teachers.index') }}" class="btn btn-light me-2">Cancel</a>
            <button type="submit" class="btn btn-crimson px-4 py-2 fw-semibold" data-loading-text="<span class=\"spinner-border spinner-border-sm spinner-border-sm-custom\" role=\"status\" aria-hidden=\"true\"></span> Saving...">
                <i class="bi bi-check-lg me-1"></i> Update Faculty Member
            </button>
        </div>
    </form>
</div>
@endsection
