@extends('site.layout', ['title' => $post->title, 'description' => $post->excerpt])

@section('content')
    <div class="kicker">{{ optional($post->published_at)->format('d M Y') }}</div>
    <h1>{{ app()->getLocale() === 'bn' && $post->title_bn ? $post->title_bn : $post->title }}</h1>
    <p class="lead">{{ app()->getLocale() === 'bn' && $post->body_bn ? $post->body_bn : $post->body }}</p>
@endsection
