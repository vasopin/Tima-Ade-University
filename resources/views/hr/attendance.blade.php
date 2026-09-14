@extends('layouts.app')

@section('title', 'Staff Attendance')

@section('content')
<div class="container-fluid px-4 py-4 bg-light min-vh-100"><div class="mb-4"><p class="text-uppercase text-muted small fw-semibold mb-1">HR workspace</p><h1 class="h2 mb-1">Staff Attendance &amp; Time Tracking</h1><p class="text-muted mb-0">Monitor daily attendance, absence records, teaching hours, and staff availability.</p></div><div class="card border-0 shadow-sm"><div class="card-body text-center p-5"><i class="bi bi-clock-history fs-1 text-muted" aria-hidden="true"></i><h2 class="h5 mt-3">Staff attendance records are not configured</h2><p class="text-muted mb-0">The current attendance table tracks students and classes only. Staff time logs and teaching-hour records are not present in the current HR schema.</p></div></div></div>
@endsection
