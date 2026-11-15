@extends('site.layout', ['title' => $job->title])

@section('content')
    <div class="kicker">{{ $job->department }} · {{ $job->location }}</div>
    <h1>{{ app()->getLocale() === 'bn' && $job->title_bn ? $job->title_bn : $job->title }}</h1>
    <p class="lead">{{ app()->getLocale() === 'bn' && $job->description_bn ? $job->description_bn : $job->description }}</p>
    <p><a class="btn" href="{{ route('contact') }}">Write to the mill</a></p>
@endsection
