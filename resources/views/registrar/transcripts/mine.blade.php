@extends('layouts.app')

@section('title', 'My Transcripts')

@section('content')
<div class="container-fluid px-4 py-6 bg-light min-vh-100">
    <h1 class="h2 mb-4" style="color:#1F2937;">My Transcripts</h1>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4">Academic Year</th>
                        <th>Term</th>
                        <th>GPA</th>
                        <th>Credits</th>
                        <th class="px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transcripts as $t)
                        <tr>
                            <td class="px-4">{{ $t->academic_year }}</td>
                            <td>{{ $t->term }}</td>
                            <td><span class="badge bg-primary">{{ number_format($t->gpa, 2) }}</span></td>
                            <td>{{ $t->total_credits }}</td>
                            <td class="px-4"><a href="{{ route('registrar.transcripts.show', $t) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">No transcripts have been generated for you yet. Contact the Registrar's office.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
