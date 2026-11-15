@extends('site.layout', ['title' => 'Gallery'])

@section('content')
    <div class="kicker">Floor</div>
    <h1>The rooms the cloth passes through.</h1>
    <div class="grid">
        @forelse ($items as $item)
            <article class="swatch {{ $item->album }}">
                <div>
                    <strong>{{ $item->title }}</strong>
                    <div>{{ $item->caption }}</div>
                </div>
            </article>
        @empty
            <p>No frames yet.</p>
        @endforelse
    </div>
@endsection
