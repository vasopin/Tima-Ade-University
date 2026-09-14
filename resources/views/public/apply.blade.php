@extends('layouts.application')

@section('title', 'Apply Now — Tima-Ade University')
@section('meta_description', 'Apply to Tima-Ade University in four clear steps — basic information, education background, documents, and review before secure submission.')
@section('content')
<div class="application-page">
    <section class="application-header"><div class="container"><p class="application-kicker">Tima-Ade University Admissions</p><h1>Apply Now</h1><p>Complete the available application information in four clear steps.</p></div></section>
    <main class="container application-shell">
        <nav class="application-progress" aria-label="Application progress">
            <span class="{{ $step >= 1 ? 'is-current' : '' }} {{ $step > 1 ? 'is-complete' : '' }}"><i class="application-progress-dot" aria-hidden="true">{!! $step > 1 ? '<svg viewBox="0 0 16 16" width="12" height="12" fill="currentColor"><path d="M13.5 3.5 6 11l-3.5-3.5-1 1L6 13l8.5-8.5-1-1z"/></svg>' : '1' !!}</i> <b>Basic Information</b></span>
            <span class="{{ $step >= 2 ? 'is-current' : '' }} {{ $step > 2 ? 'is-complete' : '' }}"><i class="application-progress-dot" aria-hidden="true">{!! $step > 2 ? '<svg viewBox="0 0 16 16" width="12" height="12" fill="currentColor"><path d="M13.5 3.5 6 11l-3.5-3.5-1 1L6 13l8.5-8.5-1-1z"/></svg>' : '2' !!}</i> <b>Education Information</b></span>
            <span class="{{ $step >= 3 ? 'is-current' : '' }} {{ $step > 3 ? 'is-complete' : '' }}"><i class="application-progress-dot" aria-hidden="true">{!! $step > 3 ? '<svg viewBox="0 0 16 16" width="12" height="12" fill="currentColor"><path d="M13.5 3.5 6 11l-3.5-3.5-1 1L6 13l8.5-8.5-1-1z"/></svg>' : '3' !!}</i> <b>Documents</b></span>
            <span class="{{ $step >= 4 ? 'is-current' : '' }}"><i class="application-progress-dot" aria-hidden="true">4</i> <b>Review &amp; Submit</b></span>
        </nav>
        <p class="application-mobile-step">Step {{ $step }} of 4 <strong>{{ ['Basic Information', 'Education Information', 'Documents', 'Review & Submit'][$step - 1] }}</strong></p>
        <div class="application-trust-note"><i class="bi bi-shield-lock-fill" aria-hidden="true"></i> Your information is submitted securely and reviewed only by authorized admissions staff.</div>
        @include('partials._alerts')
        @if($step === 1)
            <section class="application-panel" aria-labelledby="basic-title"><p class="application-kicker">Step 01</p><h2 id="basic-title">Basic Information</h2><p class="application-muted">Tell us how the university can reach you and which academic program you are interested in.</p><form method="POST" action="{{ route('public.apply.basic') }}">@csrf<div class="application-grid"><div><label for="name">Applicant full name</label><input id="name" name="name" value="{{ old('name', $draft['basic']['name'] ?? '') }}" required autocomplete="name">@error('name')<small class="application-error">{{ $message }}</small>@enderror</div><div><label for="email">Email address</label><input id="email" type="email" name="email" value="{{ old('email', $draft['basic']['email'] ?? '') }}" required autocomplete="email">@error('email')<small class="application-error">{{ $message }}</small>@enderror</div><div><label for="phone">Phone number</label><input id="phone" name="phone" value="{{ old('phone', $draft['basic']['phone'] ?? '') }}" required autocomplete="tel">@error('phone')<small class="application-error">{{ $message }}</small>@enderror</div><div><label for="program_id">Program of interest</label><div class="program-picker"><select id="program_id" name="program_id" @required($programs->isNotEmpty())><option value="">{{ $programs->isEmpty() ? 'No programs are currently available' : 'Select a program' }}</option>@foreach($programs as $program)<option value="{{ $program->id }}" @selected((string) old('program_id', $draft['basic']['program_id'] ?? '') === (string) $program->id)>{{ $program->name }} ({{ $program->code }}) — {{ $program->degree_type }}@if($program->department) — {{ $program->department->name }}@endif @if($program->department?->faculty) ({{ $program->department->faculty->name }}) @endif</option>@endforeach</select>            <button type="button" class="program-picker-trigger" role="combobox" aria-haspopup="listbox" aria-expanded="false" aria-controls="program-options">Select a program</button><div id="program-options" class="program-picker-menu" role="listbox" tabindex="-1"></div></div>@if($programs->isEmpty())<small class="application-muted">Applications for academic programs are not yet open. Please check back after the university publishes an active program.</small>@endif @error('program_id')<small class="application-error">{{ $message }}</small>@enderror</div><div class="application-full"><label for="message">Additional notes <span>(optional)</span></label><textarea id="message" name="message" rows="4">{{ old('message', $draft['basic']['message'] ?? '') }}</textarea>@error('message')<small class="application-error">{{ $message }}</small>@enderror</div></div><div class="application-actions"><button class="btn btn-crimson" type="submit" @disabled($programs->isEmpty())>Continue to Education <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i></button></div></form></section>
        @elseif($step === 2)
            <section class="application-panel" aria-labelledby="education-title"><p class="application-kicker">Step 02</p><h2 id="education-title">Education Information</h2><p class="application-muted">These fields are optional because no additional education requirements are configured in the current admissions system.</p><form method="POST" action="{{ route('public.apply.education') }}">@csrf<div class="application-grid"><div><label for="previous_institution">Previous institution <span>(optional)</span></label><input id="previous_institution" name="previous_institution" value="{{ old('previous_institution', $draft['education']['previous_institution'] ?? '') }}"></div><div><label for="qualification">Qualification <span>(optional)</span></label><input id="qualification" name="qualification" value="{{ old('qualification', $draft['education']['qualification'] ?? '') }}"></div><div><label for="field_of_study">Field of study <span>(optional)</span></label><input id="field_of_study" name="field_of_study" value="{{ old('field_of_study', $draft['education']['field_of_study'] ?? '') }}"></div><div><label for="graduation_year">Graduation year <span>(optional)</span></label><input id="graduation_year" type="number" name="graduation_year" min="1900" max="2100" value="{{ old('graduation_year', $draft['education']['graduation_year'] ?? '') }}"></div></div><div class="application-actions"><a href="{{ route('public.apply', ['step' => 1]) }}" class="btn btn-outline-dark">Back</a><button class="btn btn-crimson" type="submit">Continue to Documents <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i></button></div></form></section>
        @elseif($step === 3)
            <section class="application-panel" aria-labelledby="documents-title"><p class="application-kicker">Step 03</p><h2 id="documents-title">Documents</h2><p class="application-muted">Upload up to five supporting documents. Accepted formats are PDF, JPG, and PNG, up to 5 MB each.</p><form method="POST" action="{{ route('public.apply.documents') }}" enctype="multipart/form-data">@csrf<div class="application-grid"><div class="application-full"><label for="documents">Supporting documents <span>(optional)</span></label><input id="documents" type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png">@error('documents.*')<small class="application-error">{{ $message }}</small>@enderror</div></div><div class="application-actions"><a href="{{ route('public.apply', ['step' => 2]) }}" class="btn btn-outline-dark">Back</a><button class="btn btn-crimson" type="submit">Continue to Review <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i></button></div></form></section>
        @else
            @php $reviewBasic = $draft['basic'] ?? []; if (!empty($reviewBasic['program_id'])) { $program = \App\Models\Program::query()->where('is_active', true)->find($reviewBasic['program_id']); if ($program) { $reviewBasic['program_id'] = $program->name; } } @endphp
            <section class="application-panel" aria-labelledby="review-title"><p class="application-kicker">Step 04</p><h2 id="review-title">Review &amp; Submit</h2><p class="application-muted">Review the information below before sending your application to the admissions register.</p><div class="application-review"><section><div><h3>Basic Information</h3><a href="{{ route('public.apply', ['step' => 1]) }}">Edit</a></div><dl>@foreach($reviewBasic as $label => $value)<dt>{{ str_replace('_', ' ', ucfirst($label)) }}</dt><dd>{{ $value ?: 'Not provided' }}</dd>@endforeach</dl></section><section><div><h3>Education Information</h3><a href="{{ route('public.apply', ['step' => 2]) }}">Edit</a></div><dl>@foreach(($draft['education'] ?? []) as $label => $value)<dt>{{ str_replace('_', ' ', ucfirst($label)) }}</dt><dd>{{ $value ?: 'Not provided' }}</dd>@endforeach</dl></section><section><div><h3>Documents</h3><a href="{{ route('public.apply', ['step' => 3]) }}">Edit</a></div><p>No required documents are currently configured.</p></section></div><form method="POST" action="{{ route('public.apply.submit') }}" class="application-confirm">@csrf<label><input type="checkbox" name="confirmation" value="1" required> I confirm that the information provided is accurate and complete.</label>@error('confirmation')<small class="application-error">{{ $message }}</small>@enderror<div class="application-actions"><a href="{{ route('public.apply', ['step' => 3]) }}" class="btn btn-outline-dark">Back</a><button class="btn btn-crimson" type="submit">Submit Application <i class="bi bi-check2 ms-2" aria-hidden="true"></i></button></div></form></section>
        @endif
    </main>
</div>
<script>
(() => {
    const picker = document.querySelector('.program-picker');
    if (!picker) return;
    const select = picker.querySelector('select');
    const trigger = picker.querySelector('.program-picker-trigger');
    const menu = picker.querySelector('.program-picker-menu');
    const options = Array.from(select.options);
    const triggerText = document.createElement('span');
    triggerText.className = 'program-picker-trigger-text';
    trigger.replaceChildren(triggerText);
    options.forEach((option, index) => {
        const item = document.createElement('button');
        item.type = 'button';
        item.className = 'program-picker-option';
        item.id = `program-option-${index}`;
        item.dataset.value = option.value;
        item.setAttribute('role', 'option');
        item.setAttribute('aria-selected', option.selected ? 'true' : 'false');
        item.textContent = option.textContent;
        item.addEventListener('click', () => choose(index));
        menu.appendChild(item);
    });
    const items = () => Array.from(menu.querySelectorAll('.program-picker-option'));
    let highlighted = Math.max(0, select.selectedIndex);
    const sync = () => {
        triggerText.textContent = select.selectedOptions[0]?.textContent || 'Select a program';
        items().forEach((item, index) => item.setAttribute('aria-selected', index === select.selectedIndex ? 'true' : 'false'));
    };
    const highlight = (index, scroll = true) => {
        const list = items();
        highlighted = (index + list.length) % list.length;
        list.forEach((item, itemIndex) => item.classList.toggle('is-highlighted', itemIndex === highlighted));
        if (scroll) list[highlighted]?.scrollIntoView({ block: 'nearest' });
    };
    const close = (restoreFocus = false) => {
        menu.classList.remove('is-open');
        trigger.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
        if (restoreFocus) trigger.focus();
    };
    function choose(index) {
        select.selectedIndex = index;
        select.dispatchEvent(new Event('change', { bubbles: true }));
        sync();
        close(true);
    }
    trigger.addEventListener('click', () => {
        const open = menu.classList.toggle('is-open');
        trigger.classList.toggle('is-open', open);
        trigger.setAttribute('aria-expanded', String(open));
        if (open) { highlighted = Math.max(0, select.selectedIndex); highlight(highlighted, false); }
    });
    trigger.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowDown' || event.key === 'ArrowUp' || event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            if (!menu.classList.contains('is-open')) trigger.click();
            if (event.key === 'ArrowDown') highlight(highlighted + 1);
            if (event.key === 'ArrowUp') highlight(highlighted - 1);
            if (event.key === 'Enter' || event.key === ' ') choose(highlighted);
        } else if (event.key === 'Escape') close();
    });
    menu.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowDown') { event.preventDefault(); highlight(highlighted + 1); }
        if (event.key === 'ArrowUp') { event.preventDefault(); highlight(highlighted - 1); }
        if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); choose(highlighted); }
        if (event.key === 'Escape') close(true);
    });
    document.addEventListener('click', (event) => { if (!picker.contains(event.target)) close(); });
    select.addEventListener('change', sync);
    sync();
})();
</script>
@endsection
