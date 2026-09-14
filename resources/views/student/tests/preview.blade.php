@extends('layouts.app')
@section('title', 'Quiz Details')
@section('content')
<div class="container py-4"><span class="eyebrow-label">Available Quiz</span><h2 class="dashboard-section-header">{{ $test->title }}</h2><p class="text-muted">{{ $test->instructions }}</p><div class="alert alert-info">This quiz is available for preview. Quiz attempts are not yet enabled.</div>@foreach($test->questions->concat($test->selectedQuestions)->unique('id') as $question)<div class="card custom-card mb-3"><div class="card-body"><strong>{{ $loop->iteration }}. {{ $question->question_text }}</strong><span class="badge bg-light text-dark float-end">{{ $question->pivot->marks ?? $question->marks }} pts</span><ul class="mt-3">@foreach($question->options as $option)<li>{{ $option->option_text }}</li>@endforeach</ul></div></div>@endforeach</div>
@endsection
