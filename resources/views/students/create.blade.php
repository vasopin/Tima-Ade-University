@extends('layouts.app')

@section('title', 'Add New Student')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Add Student</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-header-title mb-1">Enroll New Student</h3>
            <p class="text-muted small mb-0">Fill out student credentials, class enrollment, and guardian contact info.</p>
        </div>
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <form method="POST" action="{{ route('students.store') }}" class="needs-validation" novalidate>
        @csrf

        <div class="row g-4">
            <!-- Account & Basic Info -->
            <div class="col-lg-6">
                <div class="card custom-card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-person-fill me-2 text-danger"></i>Basic & Account Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Alexander Vance">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="alex@school.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Minimum 6 chars" value="password">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Student Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+1-555-0000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Blood Group</label>
                                <select name="blood_group" class="form-select">
                                    <option value="">Select Blood Group</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                        <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Residential Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Street, City, Postal Code">{{ old('address') }}</textarea>
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
                                <input type="text" name="roll_number" class="form-control @error('roll_number') is-invalid @enderror" value="{{ old('roll_number') }}" required placeholder="e.g. ROLL-006">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Admission Number</label>
                                <input type="text" name="admission_number" class="form-control @error('admission_number') is-invalid @enderror" value="{{ old('admission_number') }}" required placeholder="e.g. ADM-2024-006">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Class</label>
                                <select name="school_class_id" id="classSelect" class="form-select @error('school_class_id') is-invalid @enderror" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('school_class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Section</label>
                                <select name="section_id" id="sectionSelect" class="form-select @error('section_id') is-invalid @enderror" required>
                                    <option value="">Select Section</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}" data-class="{{ $section->school_class_id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Admission Date</label>
                                <input type="date" name="admission_date" class="form-control" value="{{ old('admission_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required fw-semibold">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="graduated">Graduated</option>
                                    <option value="expelled">Expelled</option>
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
                            <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name') }}" placeholder="Guardian's Full Name">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Parent Phone</label>
                                <input type="text" name="parent_phone" class="form-control" value="{{ old('parent_phone') }}" placeholder="+1-555-0123">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Parent Email</label>
                                <input type="email" name="parent_email" class="form-control" value="{{ old('parent_email') }}" placeholder="parent@email.com">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <a href="{{ route('students.index') }}" class="btn btn-light me-2">Cancel</a>
            <button type="submit" class="btn btn-crimson px-4 py-2 fw-semibold" data-loading-text="<span class=\"spinner-border spinner-border-sm spinner-border-sm-custom\" role=\"status\" aria-hidden=\"true\"></span> Registering...">
                <i class="bi bi-check-lg me-1"></i> Register Student
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('classSelect').addEventListener('change', function() {
    const classId = this.value;
    const sectionSelect = document.getElementById('sectionSelect');
    const options = sectionSelect.querySelectorAll('option[data-class]');
    
    options.forEach(opt => {
        if (!classId || opt.dataset.class === classId) {
            opt.style.display = '';
        } else {
            opt.style.display = 'none';
        }
    });
});
</script>
@endpush
