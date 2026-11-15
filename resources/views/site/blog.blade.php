@extends('site.layout', ['title' => 'News'])

@section('content')
    <div class="kicker">Mill notes</div>
    <h1>What the floor learned this week.</h1>
    @forelse ($posts as $post)
        <article>
            <h2><a href="{{ route('blog.show', $post) }}">{{ app()->getLocale() === 'bn' && $post->title_bn ? $post->title_bn : $post->title }}</a></h2>
            <p>{{ app()->getLocale() === 'bn' && $post->excerpt_bn ? $post->excerpt_bn : $post->excerpt }}</p>
        </article>
    @empty
        <p>No notes published.</p>
    @endforelse
@endsection
