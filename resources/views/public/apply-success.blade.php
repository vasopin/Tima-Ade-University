@extends('layouts.application')
@section('title', 'Application Submitted — Tima-Ade University')
@section('meta_description', 'Your Tima-Ade University application has been received. Review your confirmation reference and next steps.')
@section('content')
<div class="application-page"><section class="application-success container"><i class="bi bi-check2-circle" aria-hidden="true"></i><p class="application-kicker">Tima-Ade University Admissions</p><h1>Application Submitted</h1><p>Your application has been successfully received and is now available for authorized admissions review.</p><div class="application-reference"><span>Application reference</span><strong>{{ $application->reference }}</strong><small>Status: {{ ucfirst(str_replace('_', ' ', $application->status)) }}</small></div>
    <div class="application-next-steps" aria-labelledby="next-steps-title">
        <h2 id="next-steps-title">What happens next</h2>
        <ol>
            <li><i class="bi bi-inbox-fill" aria-hidden="true"></i><div><strong>Application received</strong><span>Your submission is now in the admissions review queue.</span></div></li>
            <li><i class="bi bi-search" aria-hidden="true"></i><div><strong>Admissions review</strong><span>An authorized member of the admissions team will review the information provided.</span></div></li>
            <li><i class="bi bi-envelope-check-fill" aria-hidden="true"></i><div><strong>You will be contacted</strong><span>The university will reach out using the email or phone number provided in your application.</span></div></li>
        </ol>
    </div>
    <div class="d-flex flex-wrap justify-content-center gap-3"><a href="{{ route('public.admissions') }}" class="btn btn-crimson">Return to Admissions</a><a href="{{ route('public.contact') }}" class="btn btn-outline-dark">Contact the university</a></div>
</section></div>
@endsection
