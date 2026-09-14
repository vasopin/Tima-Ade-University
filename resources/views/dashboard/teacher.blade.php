@extends('layouts.app')

@section('title', 'University Portal — ' . auth()->user()->name)

@section('breadcrumb')
    <li class="breadcrumb-item active">Faculty Dashboard</li>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- ===== TEACHER WELCOME BANNER ===== -->
    <div class="welcome-banner mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <span class="badge bg-white text-dark px-3 py-1 fw-bold rounded-pill">
                        <i class="bi bi-mortarboard-fill text-primary me-1"></i> University Portal
                    </span>
                    @if($teacher && $teacher->employee_id)
                        <span class="text-white-50 small"><i class="bi bi-person-badge me-1"></i>ID: <strong>{{ $teacher->employee_id }}</strong></span>
                    @endif
                    @if($assignedClass)
                        <span class="badge bg-warning text-dark fw-semibold">
                            <i class="bi bi-star-fill me-1"></i>Class Teacher: {{ $assignedClass->name }}
                        </span>
                    @endif
                </div>
                <h2 class="welcome-title mb-1">Welcome back, {{ auth()->user()->name }}! 👩‍🏫</h2>
                <p class="welcome-text mb-0">
                    {{ $teacher->specialization ?? 'Faculty Member' }} | {{ $teacher->qualification ?? 'Educator' }}
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    <a href="{{ route('attendance.create') }}" class="btn btn-crimson shadow-sm">
                        <i class="bi bi-calendar-check me-1"></i> Mark Attendance
                    </a>
                    <a href="{{ route('exams.create') }}" class="btn btn-navy shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Create Test
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SEARCH COMPONENT ===== -->
    <!-- ===== TEACHER KPI CARDS ===== -->
    <div class="row g-3 mb-4">
        <!-- My Students -->
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card stat-card-students">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Assigned Students</span>
                        <h3 class="stat-number">{{ $teacherStats['my_students_count'] }}</h3>
                        <span class="stat-subtext text-success">
                            <i class="bi bi-building me-1"></i>{{ $assignedClass ? $assignedClass->name : 'All Classes' }}
                        </span>
                    </div>
                    <div class="stat-icon-wrapper icon-students">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Roll Call -->
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card stat-card-classes">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Today's Roll Call</span>
                        <h3 class="stat-number">
                            @if($isAttendanceMarked)
                                <span class="text-success fs-4"><i class="bi bi-check-circle-fill me-1"></i>Marked</span>
                            @else
                                <span class="text-warning fs-4"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                            @endif
                        </h3>
                        <span class="stat-subtext {{ $isAttendanceMarked ? 'text-success' : 'text-danger' }}">
                            {{ $teacherStats['present_today'] }} Present / {{ $teacherStats['absent_today'] }} Absent
                        </span>
                    </div>
                    <div class="stat-icon-wrapper icon-classes">
                        <i class="bi bi-calendar2-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Exams -->
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card stat-card-teachers">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Active Examinations</span>
                        <h3 class="stat-number">{{ $teacherStats['my_exams_count'] }}</h3>
                        <span class="stat-subtext text-info">
                            <i class="bi bi-journal-text me-1"></i>Tests & Assignments
                        </span>
                    </div>
                    <div class="stat-icon-wrapper icon-teachers">
                        <i class="bi bi-journal-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Rate -->
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card stat-card-fees">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Attendance Rate</span>
                        <h3 class="stat-number">{{ $teacherStats['attendance_rate'] }}%</h3>
                        <span class="stat-subtext text-primary">
                            <i class="bi bi-graph-up-arrow me-1"></i>Class Presence
                        </span>
                    </div>
                    <div class="stat-icon-wrapper icon-fees">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="eyebrow-label">Virtual Classroom</span>
                <h4 class="dashboard-section-header mb-1">Live Classes</h4>
                <p class="mb-0 text-muted">Manage scheduled, live, and ended virtual sessions for your assigned courses.</p>
            </div>
            <a href="{{ route('teacher.live-classes.index') }}" class="btn btn-primary">
                <i class="bi bi-camera-video me-1"></i> Manage Live Classes
            </a>
        </div>
    </div>

    <!-- ===== TESTS & ASSESSMENTS SECTION ===== -->
    @if(isset($myTests) && $myTests->count() > 0)
    <div class="card custom-card mb-4 bg-white shadow-sm border-0">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="eyebrow-label">Assessment Management</span>
                <h4 class="dashboard-section-header mb-1">My Tests & Online Assessments</h4>
                <p class="mb-0 text-muted">Create, manage, and grade online tests for your assigned courses.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('teacher.tests.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i> Create New Test
                </a>
                <a href="{{ route('teacher.tests.index') }}" class="btn btn-primary">
                    <i class="bi bi-list-check me-1"></i> Manage All Tests
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Tests -->
    <div class="lms-quick-nav mb-4" id="my-tests">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
                <span class="eyebrow-label">Recent Activity</span>
                <h4 class="dashboard-section-header mb-0">Recently Created Tests</h4>
            </div>
            <span class="badge bg-info text-white px-3 py-2">{{ $myTests->count() }} Total</span>
        </div>
        <div class="row g-3">
            @forelse($myTests->sortByDesc('created_at')->take(3) as $test)
                <div class="col-lg-4 col-md-6">
                    <div class="course-portfolio-card h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="course-chip">{{ $test->schoolClass->name ?? 'Class' }}</span>
                            @if($test->status === 'draft')
                                <span class="badge bg-warning text-dark">Draft</span>
                            @elseif($test->status === 'published')
                                <span class="badge bg-success">Published</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($test->status) }}</span>
                            @endif
                        </div>
                        <h6 class="fw-bold mb-2">{{ $test->title }}</h6>
                        <p class="small text-muted mb-3">{{ $test->subject->name ?? 'Subject' }} • {{ $test->total_marks }} marks</p>
                        <div class="mini-stat-grid">
                            <span><i class="bi bi-question-circle me-1"></i>{{ $test->questions->count() }} questions</span>
                            <span><i class="bi bi-inbox me-1"></i>{{ $test->attempts->count() }} submissions</span>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            <a href="{{ route('teacher.tests.show', $test) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            @if($test->status === 'published' && $test->attempts->count() > 0)
                                <a href="{{ route('teacher.tests.submissions', $test) }}" class="btn btn-sm btn-outline-success flex-grow-1">
                                    <i class="bi bi-inbox me-1"></i>Grade
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
    </div>
    @endif

    <div class="lms-quick-nav mb-4" id="my-classes">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
                <span class="eyebrow-label">Learning Portfolio</span>
                <h4 class="dashboard-section-header mb-0">My Classes & Course Coverage</h4>
            </div>
            <a href="{{ route('attendance.create') }}" class="btn btn-sm btn-crimson">
                <i class="bi bi-calendar-check me-1"></i> Mark attendance
            </a>
        </div>
        <div class="row g-3">
            @forelse($teacherClasses as $class)
                <div class="col-lg-4 col-md-6">
                    <div class="course-portfolio-card h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="course-chip">{{ $class->name ?? 'Class Room' }}</span>
                            <span class="badge bg-light text-dark">{{ $class->students()->count() }} students</span>
                        </div>
                        <h6 class="fw-bold mb-2">{{ $class->name }}</h6>
                        <p class="small text-muted mb-3">{{ $class->description ?? 'Core teaching schedule and classroom management.' }}</p>
                        <div class="mini-stat-grid">
                            <span><i class="bi bi-journal-bookmark me-1"></i>{{ $class->subjects->count() }} courses</span>
                            <span><i class="bi bi-play-circle me-1"></i>{{ optional($class->lectureVideos())->count() ?? 0 }} videos</span>
                        </div>
                        <div class="mt-3 d-flex flex-wrap gap-2">
                            @foreach($class->subjects->take(3) as $subject)
                                <span class="subject-pill">{{ $subject->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-lms-state">
                        <i class="bi bi-building me-2"></i>
                        No class assignments are currently available. Assignments will appear here once your class roster is linked.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- ===== CLASSROOM QUICK ACTIONS HUB ===== -->
    <div class="card custom-card mb-4 bg-white shadow-sm border-0">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <span class="fw-bold text-dark small text-uppercase">
                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Classroom Quick Actions:
                </span>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('attendance.create') }}" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-check2-square me-1"></i> Take Roll Call
                    </a>
                    <a href="{{ route('attendance.report') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-calendar-range me-1"></i> Attendance Log
                    </a>
                    <a href="{{ route('exams.index') }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-pencil-square me-1"></i> Enter Exam Marks
                    </a>
                    <a href="{{ route('exams.results') }}" class="btn btn-sm btn-outline-warning text-dark">
                        <i class="bi bi-file-earmark-bar-graph me-1"></i> Student Grade Sheets
                    </a>
                    <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-people me-1"></i> Full Student Roster
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== LEARNING MANAGEMENT PORTAL ===== -->
    <div class="row g-3 mb-4" id="my-courses">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stat-card stat-card-classes">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">My Classes</span>
                        <h3 class="stat-number">{{ $teacherClasses->count() }}</h3>
                        <span class="stat-subtext text-primary">University cohorts</span>
                    </div>
                    <div class="stat-icon-wrapper icon-classes"><i class="bi bi-building"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stat-card stat-card-students">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">My Courses</span>
                        <h3 class="stat-number">{{ $teacherClasses->flatMap->subjects->count() }}</h3>
                        <span class="stat-subtext text-success">Assigned subjects</span>
                    </div>
                    <div class="stat-icon-wrapper icon-students"><i class="bi bi-journal-bookmark-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stat-card stat-card-teachers">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Uploaded Videos</span>
                        <h3 class="stat-number">{{ $teacherStats['uploaded_videos'] }}</h3>
                        <span class="stat-subtext text-info">Lecture content</span>
                    </div>
                    <div class="stat-icon-wrapper icon-teachers"><i class="bi bi-play-circle-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stat-card stat-card-fees">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Course Materials</span>
                        <h3 class="stat-number">{{ $teacherStats['uploaded_materials'] }}</h3>
                        <span class="stat-subtext text-warning">PDF resources</span>
                    </div>
                    <div class="stat-icon-wrapper icon-fees"><i class="bi bi-file-earmark-pdf-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stat-card stat-card-classes">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Assignments</span>
                        <h3 class="stat-number">{{ $teacherStats['assignments_count'] }}</h3>
                        <span class="stat-subtext text-danger">Posted tasks</span>
                    </div>
                    <div class="stat-icon-wrapper icon-classes"><i class="bi bi-pencil-square"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stat-card stat-card-students">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Attendance</span>
                        <h3 class="stat-number">{{ $teacherStats['attendance_rate'] }}%</h3>
                        <span class="stat-subtext text-success">{{ $teacherStats['present_today'] }} present</span>
                    </div>
                    <div class="stat-icon-wrapper icon-students"><i class="bi bi-calendar-check-fill"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4" id="video-library">
        <div class="col-lg-8">
            <div class="card custom-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-cloud-upload-fill me-2 text-primary"></i>Learning Content Upload Center</h5>
                    <span class="badge bg-primary-subtle text-primary">Teacher LMS</span>
                </div>
                <div class="card-body">
                    @php
                        $editVideo = request()->filled('edit_video') ? \App\Models\LectureVideo::where('teacher_id', $teacher->id ?? 0)->find(request('edit_video')) : null;
                        $editMaterial = request()->filled('edit_material') ? \App\Models\CourseMaterial::where('teacher_id', $teacher->id ?? 0)->find(request('edit_material')) : null;
                        $editAssignment = request()->filled('edit_assignment') ? \App\Models\Assignment::where('teacher_id', $teacher->id ?? 0)->find(request('edit_assignment')) : null;
                    @endphp

                    @if($editVideo)
                        <div class="alert alert-info small mb-3">
                            <strong>Editing video:</strong> {{ $editVideo->title }}
                        </div>
                        <form method="POST" action="{{ route('teacher.learning.video.store') }}" enctype="multipart/form-data" class="mb-3">
                            @csrf
                            <input type="hidden" name="video_id" value="{{ $editVideo->id }}">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3"><label class="form-label small">Title</label><input type="text" name="title" value="{{ $editVideo->title }}" class="form-control form-control-sm" required></div>
                                <div class="col-md-2"><label class="form-label small">Class</label><select name="school_class_id" class="form-select form-select-sm" required>@foreach($teacherClasses as $class)<option value="{{ $class->id }}" {{ $editVideo->school_class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>@endforeach</select></div>
                                <div class="col-md-2"><label class="form-label small">Course</label><select name="subject_id" class="form-select form-select-sm" required>@foreach($teacherClasses->flatMap->subjects as $subject)<option value="{{ $subject->id }}" {{ $editVideo->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>@endforeach</select></div>
                                <div class="col-md-3"><label class="form-label small">Description</label><input type="text" name="description" value="{{ $editVideo->description }}" class="form-control form-control-sm"></div>
                                <div class="col-md-2"><label class="form-label small">Replace file</label><input type="file" name="video" class="form-control form-control-sm" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo"></div>
                            </div>
                            <div class="mt-2"><button type="submit" class="btn btn-primary btn-sm">Save changes</button></div>
                        </form>
                    @endif

                    @if($editMaterial)
                        <div class="alert alert-success small mb-3">
                            <strong>Editing PDF:</strong> {{ $editMaterial->title }}
                        </div>
                        <form method="POST" action="{{ route('teacher.learning.material.store') }}" enctype="multipart/form-data" class="mb-3">
                            @csrf
                            <input type="hidden" name="material_id" value="{{ $editMaterial->id }}">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3"><label class="form-label small">Title</label><input type="text" name="title" value="{{ $editMaterial->title }}" class="form-control form-control-sm" required></div>
                                <div class="col-md-2"><label class="form-label small">Class</label><select name="school_class_id" class="form-select form-select-sm" required>@foreach($teacherClasses as $class)<option value="{{ $class->id }}" {{ $editMaterial->school_class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>@endforeach</select></div>
                                <div class="col-md-2"><label class="form-label small">Course</label><select name="subject_id" class="form-select form-select-sm" required>@foreach($teacherClasses->flatMap->subjects as $subject)<option value="{{ $subject->id }}" {{ $editMaterial->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>@endforeach</select></div>
                                <div class="col-md-3"><label class="form-label small">Description</label><input type="text" name="description" value="{{ $editMaterial->description }}" class="form-control form-control-sm"></div>
                                <div class="col-md-2"><label class="form-label small">Replace PDF</label><input type="file" name="material" class="form-control form-control-sm" accept="application/pdf"></div>
                            </div>
                            <div class="mt-2"><button type="submit" class="btn btn-success btn-sm">Save changes</button></div>
                        </form>
                    @endif

                    @if($editAssignment)
                        <div class="alert alert-warning small mb-3">
                            <strong>Editing assignment:</strong> {{ $editAssignment->title }}
                        </div>
                        <form method="POST" action="{{ route('teacher.learning.assignment.store') }}" enctype="multipart/form-data" class="mb-3">
                            @csrf
                            <input type="hidden" name="assignment_id" value="{{ $editAssignment->id }}">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3"><label class="form-label small">Title</label><input type="text" name="title" value="{{ $editAssignment->title }}" class="form-control form-control-sm" required></div>
                                <div class="col-md-2"><label class="form-label small">Class</label><select name="school_class_id" class="form-select form-select-sm" required>@foreach($teacherClasses as $class)<option value="{{ $class->id }}" {{ $editAssignment->school_class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>@endforeach</select></div>
                                <div class="col-md-2"><label class="form-label small">Course</label><select name="subject_id" class="form-select form-select-sm" required>@foreach($teacherClasses->flatMap->subjects as $subject)<option value="{{ $subject->id }}" {{ $editAssignment->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>@endforeach</select></div>
                                <div class="col-md-2"><label class="form-label small">Due date</label><input type="date" name="due_date" value="{{ optional($editAssignment->due_date)->format('Y-m-d') }}" class="form-control form-control-sm"></div>
                                <div class="col-md-2"><label class="form-label small">Available from</label><input type="datetime-local" name="available_from" value="{{ optional($editAssignment->available_from)?->format('Y-m-d\TH:i') }}" class="form-control form-control-sm"></div>
                                <div class="col-md-1"><label class="form-label small">Attempts</label><input type="number" name="max_attempts" min="1" max="20" value="{{ $editAssignment->max_attempts }}" class="form-control form-control-sm"></div>
                                <div class="col-md-1"><label class="form-label small">Points</label><input type="number" name="max_points" min="0" step="0.01" value="{{ $editAssignment->max_points }}" class="form-control form-control-sm"></div>
                                <div class="col-md-2"><label class="form-label small">Status</label><select name="status" class="form-select form-select-sm"><option value="published" @selected($editAssignment->status === 'published')>Published</option><option value="draft" @selected($editAssignment->status === 'draft')>Draft</option></select></div>
                                <div class="col-md-2"><label class="form-label small">Replace file</label><input type="file" name="assignment_file" class="form-control form-control-sm" accept="application/pdf,.doc,.docx"></div>
                            </div>
                            <div class="mt-2"><button type="submit" class="btn btn-warning btn-sm text-dark">Save changes</button></div>
                        </form>
                    @endif

                    <div class="row g-3">
                        <div class="col-lg-4">
                            <h6 class="fw-bold text-dark mb-3">Upload lecture video</h6>
                            <form method="POST" action="{{ route('teacher.learning.video.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small">Title</label>
                                    <input type="text" name="title" class="form-control form-control-sm" placeholder="Introduction to Computer Science" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Class</label>
                                    <select name="school_class_id" class="form-select form-select-sm" required>
                                        <option value="">Select class</option>
                                        @foreach($teacherClasses as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Course</label>
                                    <select name="subject_id" class="form-select form-select-sm" required>
                                        <option value="">Select course</option>
                                        @foreach($teacherClasses->flatMap->subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Description</label>
                                    <textarea name="description" rows="2" class="form-control form-control-sm" placeholder="Summarize the lesson objective"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">Video file</label>
                                    <input type="file" name="video" class="form-control form-control-sm" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-upload me-1"></i>Upload video</button>
                            </form>
                        </div>
                        <div class="col-lg-4">
                            <h6 class="fw-bold text-dark mb-3">Upload PDF material</h6>
                            <form method="POST" action="{{ route('teacher.learning.material.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small">Title</label>
                                    <input type="text" name="title" class="form-control form-control-sm" placeholder="Lecture Notes Week 3" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Class</label>
                                    <select name="school_class_id" class="form-select form-select-sm" required>
                                        <option value="">Select class</option>
                                        @foreach($teacherClasses as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Course</label>
                                    <select name="subject_id" class="form-select form-select-sm" required>
                                        <option value="">Select course</option>
                                        @foreach($teacherClasses->flatMap->subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Description</label>
                                    <textarea name="description" rows="2" class="form-control form-control-sm" placeholder="Brief overview of the learning material"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">PDF file</label>
                                    <input type="file" name="material" class="form-control form-control-sm" accept="application/pdf" required>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100"><i class="bi bi-file-earmark-pdf me-1"></i>Upload PDF</button>
                            </form>
                        </div>
                        <div class="col-lg-4">
                            <h6 class="fw-bold text-dark mb-3">Post assignment</h6>
                            <form method="POST" action="{{ route('teacher.learning.assignment.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small">Title</label>
                                    <input type="text" name="title" class="form-control form-control-sm" placeholder="Research Assignment 1" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Class</label>
                                    <select name="school_class_id" class="form-select form-select-sm" required>
                                        <option value="">Select class</option>
                                        @foreach($teacherClasses as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Course</label>
                                    <select name="subject_id" class="form-select form-select-sm" required>
                                        <option value="">Select course</option>
                                        @foreach($teacherClasses->flatMap->subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Due date</label>
                                    <input type="date" name="due_date" class="form-control form-control-sm">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Available from</label>
                                    <input type="datetime-local" name="available_from" class="form-control form-control-sm">
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6"><label class="form-label small">Max attempts</label><input type="number" name="max_attempts" min="1" max="20" value="1" class="form-control form-control-sm"></div>
                                    <div class="col-6"><label class="form-label small">Max points</label><input type="number" name="max_points" min="0" step="0.01" value="100" class="form-control form-control-sm"></div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Status</label>
                                    <select name="status" class="form-select form-select-sm"><option value="published">Publish now</option><option value="draft">Save as draft</option></select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">Assignment file (optional)</label>
                                    <input type="file" name="assignment_file" class="form-control form-control-sm" accept="application/pdf,.doc,.docx">
                                </div>
                                <button type="submit" class="btn btn-warning btn-sm w-100 text-dark"><i class="bi bi-clipboard-check me-1"></i>Post assignment</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card custom-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-activity me-2 text-success"></i>Recent Activity</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($recentActivity as $activity)
                            <li class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $activity['title'] }}</div>
                                        <small class="text-muted">{{ $activity['type'] }} • {{ $activity['meta'] }}</small>
                                    </div>
                                    <span class="badge bg-light text-dark small">{{ $activity['time'] }}</span>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item p-4 text-center text-muted">No learning activity yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4" id="material-library">
        <div class="col-lg-7">
            <div class="card custom-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-collection-play-fill me-2 text-danger"></i>My Learning Materials</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Course</th>
                                    <th>Class</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $allResources = collect($teacherVideos)->merge($teacherMaterials)->merge($teacherAssignments);
                                @endphp
                                @forelse($allResources as $resource)
                                    @php
                                        $resourceType = $resource instanceof \App\Models\LectureVideo ? 'video' : ($resource instanceof \App\Models\CourseMaterial ? 'material' : 'assignment');
                                        $deleteRoute = $resourceType === 'video' ? route('teacher.learning.video.delete', $resource) : ($resourceType === 'material' ? route('teacher.learning.material.delete', $resource) : route('teacher.learning.assignment.delete', $resource));
                                    @endphp
                                    <tr>
                                        <td><span class="badge bg-light text-dark">{{ $resourceType === 'video' ? 'Video' : ($resourceType === 'material' ? 'PDF' : 'Assignment') }}</span></td>
                                        <td>{{ $resource->title }}</td>
                                        <td>{{ $resource->subject->name ?? '—' }}</td>
                                        <td>{{ $resource->schoolClass->name ?? '—' }}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="?edit_{{ $resourceType }}={{ $resource->id }}" class="btn btn-outline-primary">Edit</a>
                                                <form method="POST" action="{{ $deleteRoute }}" onsubmit="return confirm('Delete this resource?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No learning resources uploaded yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card custom-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-calendar-week me-2 text-primary"></i>Upcoming Classes</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($teacherClasses as $class)
                            <li class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $class->name }}</div>
                                        <small class="text-muted">{{ $class->subjects->take(3)->pluck('name')->implode(', ') ?: 'Course schedule pending' }}</small>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary">{{ $class->students->count() }} students</span>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item p-4 text-center text-muted">No classes assigned yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4" id="attendance-overview">
        <div class="col-lg-7">
            <div class="card custom-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-calendar-check me-2 text-success"></i>Attendance Overview</h5>
                    <span class="badge bg-success-subtle text-success">{{ $teacherStats['attendance_rate'] }}% attendance</span>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3 col-6">
                            <div class="border rounded p-3 text-center">
                                <div class="small text-muted">Present</div>
                                <div class="fw-bold fs-4 text-success">{{ $teacherStats['present_today'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="border rounded p-3 text-center">
                                <div class="small text-muted">Absent</div>
                                <div class="fw-bold fs-4 text-danger">{{ $teacherStats['absent_today'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="border rounded p-3 text-center">
                                <div class="small text-muted">Unmarked</div>
                                <div class="fw-bold fs-4 text-warning">{{ $teacherStats['unmarked_today'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="border rounded p-3 text-center">
                                <div class="small text-muted">Students</div>
                                <div class="fw-bold fs-4 text-primary">{{ $myStudents->count() }}</div>
                            </div>
                        </div>
                    </div>

                    @if($todayAttendance->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Class</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayAttendance->take(8) as $record)
                                        <tr>
                                            <td>{{ optional($record->student)->user->name ?? 'Student' }}</td>
                                            <td>{{ optional($record->schoolClass)->name ?? 'Class' }}</td>
                                            <td>
                                                <span class="badge {{ $record->status === 'present' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                                    {{ ucfirst($record->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-lms-state small">
                            <i class="bi bi-calendar-x me-2"></i>
                            No attendance has been recorded for this class today yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card custom-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-people-fill me-2 text-primary"></i>Class Snapshot</h5>
                </div>
                <div class="card-body">
                    @forelse($teacherClasses->take(4) as $class)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold text-dark">{{ $class->name }}</div>
                                <small class="text-muted">{{ $class->students->count() }} enrolled students</small>
                            </div>
                            <span class="badge bg-light text-dark rounded-pill">{{ $class->subjects->count() }} courses</span>
                        </div>
                    @empty
                        <div class="empty-lms-state small">
                            <i class="bi bi-building me-2"></i>
                            No classes have been assigned to you yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- ===== TWO-COLUMN DATA GRID ===== -->
    <div class="row g-3 mb-4">
        <!-- My Class Students -->
        <div class="col-lg-7">
            <div class="card custom-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-mortarboard-fill me-2 text-danger"></i>
                        Students in {{ $assignedClass ? $assignedClass->name : 'My Class' }} ({{ $myStudents->count() }})
                    </h5>
                    <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Roll No</th>
                                <th>Student Name</th>
                                <th>Section</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myStudents as $s)
                                <tr>
                                    <td><span class="badge bg-light text-dark">{{ $s->roll_number }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $s->user->avatar_url }}" class="rounded-circle me-2" width="32" height="32" alt="">
                                            <div>
                                                <a href="{{ route('students.show', $s) }}" class="fw-semibold text-dark text-decoration-none">
                                                    {{ $s->user->name }}
                                                </a>
                                                <div class="small text-muted">{{ $s->admission_number }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-info-subtle text-info fw-semibold">{{ $s->section->name ?? 'A' }}</span></td>
                                    <td>{!! $s->status_badge !!}</td>
                                    <td class="text-end">
                                        <a href="{{ route('students.show', $s) }}" class="btn btn-sm btn-outline-primary" title="Student Profile">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No students assigned to your class yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Class Examinations & Quick Grading -->
        <div class="col-lg-5">
            <div class="card custom-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-journal-check me-2 text-primary"></i>Tests & Mark Sheets</h5>
                    <a href="{{ route('exams.create') }}" class="btn btn-sm btn-crimson">+ Schedule</a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($myExams as $exam)
                            <li class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $exam->name }}</div>
                                        <small class="text-muted">
                                            {{ $exam->subject->name ?? 'General' }} | {{ $exam->exam_date->format('M d, Y') }}
                                        </small>
                                    </div>
                                    {!! $exam->status_badge !!}
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                    <span class="small text-muted">Max: <strong>{{ $exam->total_marks }}</strong> / Pass: {{ $exam->pass_marks }}</span>
                                    <a href="{{ route('exams.marks', $exam) }}" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-pencil-square me-1"></i>Enter Marks
                                    </a>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item p-4 text-center text-muted">No examinations scheduled.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== CAMPUS NOTICES FOR FACULTY ===== -->
    <div class="card custom-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bi bi-megaphone me-2 text-danger"></i>Campus Notices & Staff Circulars</h5>
            <a href="{{ route('public.notices') }}" target="_blank" class="btn btn-sm btn-outline-secondary">Public Board</a>
        </div>
        <div class="card-body p-0">
            <div class="row g-0">
                @forelse($notices as $notice)
                    <div class="col-md-6 p-3 border-bottom border-end">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="badge bg-light text-dark text-uppercase small">{{ $notice->type }}</span>
                            <small class="text-muted">{{ $notice->created_at->diffForHumans() }}</small>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">{{ $notice->title }}</h6>
                        <p class="text-muted small mb-2">{{ Str::limit($notice->content, 110) }}</p>
                        <a href="{{ route('public.notices.single', $notice->slug ?? $notice->id) }}" target="_blank" class="small fw-semibold text-danger text-decoration-none">
                            Read Full Notice <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted w-100">No active circulars posted.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ===== MONTHLY CALENDAR ===== -->
    <div class="mt-8 mb-6">
    </div>

</div>
@endsection
