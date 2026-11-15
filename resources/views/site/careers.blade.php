@extends('site.layout', ['title' => 'Careers'])

@section('content')
    <div class="kicker">Hiring</div>
    <h1>People who can hold a shade.</h1>
    @forelse ($jobs as $job)
        <article class="card">
            <h3><a href="{{ route('careers.show', $job) }}">{{ app()->getLocale() === 'bn' && $job->title_bn ? $job->title_bn : $job->title }}</a></h3>
            <p>{{ $job->department }} · {{ $job->location }} · {{ str_replace('_', ' ', $job->employment_type) }}</p>
        </article>
    @empty
        <p>No open roles.</p>
    @endforelse
@endsection
