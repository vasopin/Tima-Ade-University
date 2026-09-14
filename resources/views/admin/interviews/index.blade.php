@extends('layouts.app')

@section('title', 'Interviews')

@section('breadcrumb')
    <li class="breadcrumb-item active">Interviews</li>
@endsection

@section('content')
    <div class="container-fluid px-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="page-title mb-1">Interviews</h3>
                <p class="text-muted small mb-0">Manage applicant interviews using the existing admissions records.</p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            @foreach([
                'total' => 'Total Interviews',
                'scheduled' => 'Scheduled',
                'today' => 'Today',
                'upcoming' => 'Upcoming',
                'completed' => 'Completed',
                'pending' => 'Pending',
                'cancelled' => 'Cancelled',
            ] as $key => $label)
                <div class="col-6 col-lg-3">
                    <div class="card custom-card h-100">
                        <div class="card-body">
                            <span class="text-muted small d-block">{{ $label }}</span>
                            <strong class="fs-3 text-dark">{{ $interviewStats[$key] ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card custom-card">
            <div class="card-body py-5 text-center">
                <i class="bi bi-calendar2-event fs-1 text-muted"></i>
                <h5 class="mt-3">No interview records</h5>
                <p class="text-muted mb-0">
                    Interview scheduling is not currently part of the admissions data model.
                </p>
            </div>
        </div>
    </div>
@endsection
