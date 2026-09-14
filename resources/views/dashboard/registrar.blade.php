@extends('layouts.app')

@section('title', 'Registrar Dashboard')

@section('content')
<div class="container-fluid px-4 py-6 bg-light min-vh-100">
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="h2 mb-2">Registrar Dashboard</h1>
            <p class="text-muted">Manage academic records and transcripts</p>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Students</p>
                            <h3 class="mb-0">{{ $stats['total_students'] }}</h3>
                        </div>
                        <span class="badge bg-primary">👥</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Transcripts Generated</p>
                            <h3 class="mb-0">{{ $stats['transcripts_generated'] }}</h3>
                        </div>
                        <span class="badge bg-info">📄</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Graduation Eligible</p>
                            <h3 class="mb-0">{{ $stats['graduation_eligible'] }}</h3>
                        </div>
                        <span class="badge bg-success">✓</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Graduated</p>
                            <h3 class="mb-0">{{ $stats['graduated'] }}</h3>
                        </div>
                        <span class="badge bg-primary">🎓</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Recent Transcripts</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Student Name</th>
                                <th>Academic Year</th>
                                <th>Term</th>
                                <th>GPA</th>
                                <th>Generated At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTranscripts as $transcript)
                                <tr>
                                    <td class="px-4">{{ $transcript->student->user->name ?? 'N/A' }}</td>
                                    <td>{{ $transcript->academic_year ?? '-' }}</td>
                                    <td>{{ $transcript->term ?? '-' }}</td>
                                    <td>{{ $transcript->gpa ?? '-' }}</td>
                                    <td>{{ $transcript->generated_at?->format('M d, Y') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No transcripts generated.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12 col-md-6 col-xl-3"><a href="{{ route('students.index') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-body"><div class="card-body"><i class="bi bi-person-vcard text-primary fs-4" aria-hidden="true"></i><h2 class="h6 mt-3">Student records</h2><p class="small text-muted mb-0">Manage enrollment status and student details.</p></div></a></div>
        <div class="col-12 col-md-6 col-xl-3"><a href="{{ route('registrar.graduation.index') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-body"><div class="card-body"><i class="bi bi-check2-square text-success fs-4" aria-hidden="true"></i><h2 class="h6 mt-3">Graduation clearance</h2><p class="small text-muted mb-0">Review eligibility and approve graduation records.</p></div></a></div>
        <div class="col-12 col-md-6 col-xl-3"><a href="{{ route('timetables.index') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-body"><div class="card-body"><i class="bi bi-calendar3 text-info fs-4" aria-hidden="true"></i><h2 class="h6 mt-3">Schedules and catalog</h2><p class="small text-muted mb-0">Open course, class, and timetable management.</p></div></a></div>
        <div class="col-12 col-md-6 col-xl-3"><a href="{{ route('registrar.transfer-credits') }}" class="card border-0 shadow-sm h-100 text-decoration-none text-body"><div class="card-body"><i class="bi bi-arrow-left-right text-warning fs-4" aria-hidden="true"></i><h2 class="h6 mt-3">Transfer credits</h2><p class="small text-muted mb-0">Review external credit equivalencies.</p></div></a></div>
    </div>
</div>
@endsection
