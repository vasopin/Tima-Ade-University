@extends('layouts.app')

@section('title', 'Generate Transcript')

@section('content')
<div class="container-fluid px-4 py-6 bg-light min-vh-100">
    <nav class="mb-3"><a href="{{ route('registrar.transcripts.index') }}" class="text-decoration-none">&laquo; Back to Transcripts</a></nav>
    <h1 class="h2 mb-4" style="color:#1F2937;">Generate Transcript</h1>

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-warning rounded-3">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('registrar.transcripts.store') }}">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Student</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">Select student...</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}" @selected(old('student_id') == $s->id)>{{ $s->user->name ?? 'Unknown' }} ({{ $s->admission_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Academic Year</label>
                        <input type="text" name="academic_year" value="{{ old('academic_year') }}" class="form-control" placeholder="e.g. 2025/2026" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Term</label>
                        <input type="text" name="term" value="{{ old('term') }}" class="form-control" placeholder="e.g. First Term" required>
                    </div>
                </div>
                <p class="text-muted small mt-3 mb-0">GPA, credits, and subject summary are computed automatically from this student's real examination marks — no figures are entered manually.</p>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn text-white rounded-3" style="background:#016ED5;">Generate Transcript</button>
                    <a href="{{ route('registrar.transcripts.index') }}" class="btn btn-outline-secondary rounded-3">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
