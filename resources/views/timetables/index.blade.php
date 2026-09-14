@extends('layouts.app')

@section('title', 'Academic Timetable & Schedule')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Timetable</li>
@endsection

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: #1F2937;">Weekly Class Schedule</h3>
            <p class="text-muted small mb-0">Manage course periods, room allocations, teacher assignments, and classroom timetables.</p>
        </div>
        @if(auth()->user()->isAdmin())
            <div>
                <a href="{{ route('timetables.create') }}" class="btn btn-primary shadow-sm" style="background-color: #016ED5; border-color: #016ED5;">
                    <i class="bi bi-calendar-plus-fill me-1"></i> Add Timetable Slot
                </a>
            </div>
        @endif
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('timetables.index') }}" class="row g-3 align-items-center">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold text-muted mb-1">SELECT CLASS</label>
                    <select name="class_id" class="form-select" onchange="this.form.submit()">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }} (Grade {{ $c->grade_level }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-semibold text-muted mb-1">SECTION (OPTIONAL)</label>
                    <select name="section_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Sections</option>
                        @foreach($sections as $sec)
                            <option value="{{ $sec->id }}" {{ $selectedSectionId == $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-dark w-100 mt-md-4">
                        <i class="bi bi-funnel me-1"></i> View Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Timetable Grid -->
    <div class="row g-4">
        @foreach($days as $day)
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-calendar-event me-2 text-primary"></i>{{ $day }}
                        </h6>
                        <span class="badge bg-light text-dark border px-2 py-1">
                            {{ isset($schedules[$day]) ? $schedules[$day]->count() : 0 }} Periods
                        </span>
                    </div>
                    <div class="card-body p-3">
                        @if(isset($schedules[$day]) && $schedules[$day]->isNotEmpty())
                            @foreach($schedules[$day] as $slot)
                                <div class="p-3 mb-2 rounded-3 border bg-light position-relative">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold text-primary mb-0">{{ $slot->subject->name ?? 'Subject' }}</h6>
                                        <span class="badge bg-white text-dark border small">
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}
                                        </span>
                                    </div>
                                    <div class="small text-muted mb-1">
                                        <i class="bi bi-person me-1"></i> {{ $slot->teacher->user->name ?? 'Unassigned Faculty' }}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small text-muted">
                                            <i class="bi bi-geo-alt me-1"></i> Room: {{ $slot->room_number ?? 'Main Room' }}
                                        </span>
                                        @if(auth()->user()->isAdmin())
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('timetables.edit', $slot) }}" class="btn btn-outline-secondary btn-sm py-0 px-2" title="Edit"><i class="bi bi-pencil"></i></a>
                                                <form method="POST" action="{{ route('timetables.destroy', $slot) }}" class="d-inline" onsubmit="return confirm('Remove slot?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="Delete"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4 text-muted small">
                                <i class="bi bi-clock-history d-block mb-1 fs-5"></i>
                                No scheduled periods
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
