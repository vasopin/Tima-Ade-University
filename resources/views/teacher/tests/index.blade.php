@extends('layouts.app')

@section('title', 'Manage Tests — Teacher Portal')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Manage Tests</li>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- ===== PAGE HEADER ===== -->
    <div class="welcome-banner mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <span class="badge bg-white text-dark px-3 py-1 fw-bold rounded-pill">
                        <i class="bi bi-file-text text-primary me-1"></i> Assessment Management
                    </span>
                </div>
                <h2 class="welcome-title mb-1">My Tests & Assessments</h2>
                <p class="welcome-text mb-0">Create, manage, and grade tests for your assigned courses.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('teacher.tests.create') }}" class="btn btn-success shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Create New Test
                </a>
            </div>
        </div>
    </div>

    <!-- ===== STAT CARDS ===== -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card stat-card-students">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Total Tests Created</span>
                        <h3 class="stat-number">{{ $myTests->count() }}</h3>
                        <span class="stat-subtext text-info">
                            <i class="bi bi-file-earmark-text me-1"></i>All assessments
                        </span>
                    </div>
                    <div class="stat-icon-wrapper icon-students">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="stat-card stat-card-teachers">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Published Tests</span>
                        <h3 class="stat-number">{{ $myTests->where('status', 'published')->count() }}</h3>
                        <span class="stat-subtext text-success">
                            <i class="bi bi-check-circle-fill me-1"></i>Active tests
                        </span>
                    </div>
                    <div class="stat-icon-wrapper icon-teachers">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="stat-card stat-card-classes">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Drafts</span>
                        <h3 class="stat-number">{{ $myTests->where('status', 'draft')->count() }}</h3>
                        <span class="stat-subtext text-warning">
                            <i class="bi bi-pencil-square me-1"></i>In progress
                        </span>
                    </div>
                    <div class="stat-icon-wrapper icon-classes">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="stat-card stat-card-fees">
                <div class="stat-card-inner">
                    <div>
                        <span class="stat-label">Submissions</span>
                        <h3 class="stat-number">{{ $myTests->sum(fn($test) => $test->attempts->count()) }}</h3>
                        <span class="stat-subtext text-info">
                            <i class="bi bi-inbox me-1"></i>Student submissions
                        </span>
                    </div>
                    <div class="stat-icon-wrapper icon-fees">
                        <i class="bi bi-inbox"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== FILTERS & SEARCH ===== -->
    <div class="card custom-card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <input type="search" name="search" class="form-control" placeholder="Search tests by title..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== TESTS TABLE ===== -->
    @if($myTests->count() > 0)
        <div class="table-responsive card custom-card">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Test Name</th>
                        <th>Course</th>
                        <th>Questions</th>
                        <th>Status</th>
                        <th>Submissions</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($myTests as $test)
                        <tr>
                            <td>
                                <strong>{{ $test->title }}</strong><br>
                                <small class="text-muted">{{ $test->total_marks }} marks</small>
                            </td>
                            <td>
                                {{ $test->schoolClass->name ?? 'Class' }}<br>
                                <small class="text-muted">{{ $test->subject->name ?? 'Subject' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $test->questions->count() }}</span>
                            </td>
                            <td>
                                @if($test->status === 'draft')
                                    <span class="badge bg-warning text-dark">Draft</span>
                                @elseif($test->status === 'published')
                                    <span class="badge bg-success">Published</span>
                                @elseif($test->status === 'closed')
                                    <span class="badge bg-danger">Closed</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($test->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('teacher.tests.submissions', $test) }}" class="btn btn-sm btn-outline-primary">
                                    {{ $test->attempts->count() }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            </td>
                            <td>
                                <small class="text-muted">{{ $test->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('teacher.tests.show', $test) }}" class="btn btn-sm btn-outline-secondary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($test->status === 'draft')
                                        <a href="{{ route('teacher.tests.edit', $test) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('teacher.tests.destroy', $test) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @elseif($test->status === 'published')
                                        <form action="{{ route('teacher.tests.close', $test) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Close Test">
                                                <i class="bi bi-lock"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="card custom-card text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
            <h5 class="fw-bold text-dark mb-2">No Tests Created Yet</h5>
            <p class="text-muted mb-3">Start creating tests for your assigned courses.</p>
            <a href="{{ route('teacher.tests.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Create Your First Test
            </a>
        </div>
    @endif

</div>
@endsection
