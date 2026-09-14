@extends('layouts.app')

@section('title', 'Add Timetable Slot')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('timetables.index') }}">Timetable</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-plus-fill me-2 text-primary"></i>Schedule Period Slot</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('timetables.store') }}">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="school_class_id" class="form-label small fw-semibold text-muted">CLASS</label>
                                <select name="school_class_id" id="school_class_id" class="form-select @error('school_class_id') is-invalid @enderror" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('school_class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                @error('school_class_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="section_id" class="form-label small fw-semibold text-muted">SECTION (OPTIONAL)</label>
                                <select name="section_id" id="section_id" class="form-select @error('section_id') is-invalid @enderror">
                                    <option value="">All Sections</option>
                                    @foreach($sections as $sec)
                                        <option value="{{ $sec->id }}" {{ old('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="subject_id" class="form-label small fw-semibold text-muted">SUBJECT</label>
                                <select name="subject_id" id="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $sub)
                                        <option value="{{ $sub->id }}" {{ old('subject_id') == $sub->id ? 'selected' : '' }}>{{ $sub->name }} ({{ $sub->code }})</option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="teacher_id" class="form-label small fw-semibold text-muted">INSTRUCTOR / TEACHER</label>
                                <select name="teacher_id" id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
                                    <option value="">Select Teacher (Optional)</option>
                                    @foreach($teachers as $tch)
                                        <option value="{{ $tch->id }}" {{ old('teacher_id') == $tch->id ? 'selected' : '' }}>{{ $tch->user->name ?? 'Faculty' }} ({{ $tch->employee_id }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="day_of_week" class="form-label small fw-semibold text-muted">DAY OF WEEK</label>
                                <select name="day_of_week" id="day_of_week" class="form-select @error('day_of_week') is-invalid @enderror" required>
                                    @foreach($days as $day)
                                        <option value="{{ $day }}" {{ old('day_of_week') == $day ? 'selected' : '' }}>{{ $day }}</option>
                                    @endforeach
                                </select>
                                @error('day_of_week')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="start_time" class="form-label small fw-semibold text-muted">START TIME</label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', '09:00') }}" required>
                                @error('start_time')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="end_time" class="form-label small fw-semibold text-muted">END TIME</label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', '10:00') }}" required>
                                @error('end_time')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="room_number" class="form-label small fw-semibold text-muted">ROOM / LAB NUMBER</label>
                                <input type="text" class="form-control @error('room_number') is-invalid @enderror" id="room_number" name="room_number" value="{{ old('room_number') }}" placeholder="e.g. Science Lab B / Room 204">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('timetables.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm" style="background-color: #016ED5; border-color: #016ED5;">
                                <i class="bi bi-save me-1"></i> Save Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
