@extends('site.layout', ['title' => 'Denim catalog'])

@section('content')
    <div class="kicker">Cloth</div>
    <h1>Constructions the mill can run.</h1>
    <div class="grid">
        @forelse ($fabrics as $fabric)
            <article class="card">
                <div class="kicker">{{ $fabric->weight_oz }} oz · {{ $fabric->width_inch }}"</div>
                <h3><a href="{{ route('products.show', $fabric) }}">{{ $fabric->name }}</a></h3>
                <p>{{ $fabric->weave }}<br>{{ $fabric->composition }}<br>{{ $fabric->shade }} · {{ $fabric->finish }}</p>
            </article>
        @empty
            <p>The catalog is empty until a cloth is published.</p>
        @endforelse
    </div>
@endsection
