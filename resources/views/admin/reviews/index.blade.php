@extends('layouts.app')

@section('title', 'Application Reviews')

@section('breadcrumb')
    <li class="breadcrumb-item active">Application Reviews</li>
@endsection

@section('content')
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="page-title mb-1">Application Reviews</h3>
                <p class="text-muted small mb-0">Review submitted applications using the existing admissions records.</p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            @foreach([
                'total' => 'Total Applications to Review',
                'submitted' => 'Pending Reviews',
                'under_review' => 'In Review',
            ] as $key => $label)
                <div class="col-12 col-md-4">
                    <div class="card custom-card h-100">
                        <div class="card-body">
                            <span class="text-muted small d-block">{{ $label }}</span>
                            <strong class="fs-3 text-dark">{{ $reviewStats[$key] ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card custom-card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reviews') }}" class="row g-3">
                    <div class="col-lg-4">
                        <label class="form-label" for="review-search">Search reviews</label>
                        <input id="review-search" type="search" name="search" class="form-control"
                               value="{{ request('search') }}" placeholder="Reference, applicant, email, or phone">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" for="review-status">Application status</label>
                        <select id="review-status" name="status" class="form-select">
                            <option value="">All reviewable applications</option>
                            @foreach(['submitted', 'under_review'] as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" for="review-sort">Sort</label>
                        <select id="review-sort" name="sort" class="form-select">
                            <option value="newest" @selected($sort === 'desc')>Newest first</option>
                            <option value="oldest" @selected($sort === 'asc')>Oldest first</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" for="review-from">From</label>
                        <input id="review-from" type="date" name="submitted_from" class="form-control"
                               value="{{ request('submitted_from') }}">
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label class="form-label" for="review-to">To</label>
                        <input id="review-to" type="date" name="submitted_to" class="form-control"
                               value="{{ request('submitted_to') }}">
                    </div>
                    <div class="col-md-3 col-lg-2 d-flex align-items-end">
                        <button class="btn btn-dark w-100" type="submit">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card custom-card">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Application number</th>
                            <th>Program</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Last updated</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $application->name }}</span>
                                    <small class="d-block text-muted">{{ $application->email }}</small>
                                </td>
                                <td>{{ $application->reference }}</td>
                                <td>{{ $application->grade_interested }}</td>
                                <td>
                                    <span class="badge text-bg-{{ $application->status === 'under_review' ? 'warning' : 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                    </span>
                                </td>
                                <td>{{ $application->created_at->format('d M Y') }}</td>
                                <td>{{ $application->updated_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.applications.show', $application) }}"
                                       class="btn btn-sm btn-outline-primary">Open review</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    No applications are currently available for review.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $applications->links() }}</div>
        </div>
    </div>
@endsection