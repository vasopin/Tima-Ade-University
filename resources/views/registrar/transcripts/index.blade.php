@extends('layouts.app')

@section('title', 'Transcripts')

@section('content')
<div class="container-fluid px-4 py-6 bg-light min-vh-100">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h2 mb-1" style="color:#1F2937;">Student Transcripts</h1>
            <p class="text-muted mb-0">Official transcripts generated from real examination records</p>
        </div>
        <a href="{{ route('registrar.transcripts.create') }}" class="btn text-white rounded-3" style="background:#016ED5;">
            <i class="bi bi-plus-lg me-1"></i> Generate Transcript
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by student name...">
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-secondary w-100">Search</button>
        </div>
    </form>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4">Student</th>
                        <th>Academic Year</th>
                        <th>Term</th>
                        <th>GPA</th>
                        <th>Credits</th>
                        <th>Generated</th>
                        <th class="px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transcripts as $t)
                        <tr>
                            <td class="px-4">{{ $t->student->user->name ?? 'N/A' }}</td>
                            <td>{{ $t->academic_year }}</td>
                            <td>{{ $t->term }}</td>
                            <td><span class="badge bg-primary">{{ number_format($t->gpa, 2) }}</span></td>
                            <td>{{ $t->total_credits }}</td>
                            <td>{{ $t->generated_at?->format('M d, Y') }}</td>
                            <td class="px-4">
                                <a href="{{ route('registrar.transcripts.show', $t) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No transcripts have been generated yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $transcripts->links() }}</div>
</div>
@endsection
