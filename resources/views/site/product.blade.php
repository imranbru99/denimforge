@extends('site.layout', ['title' => $fabric->name, 'description' => $fabric->description])

@section('content')
    <div class="kicker">{{ $fabric->shade }} · {{ $fabric->weight_oz }} oz</div>
    <h1>{{ app()->getLocale() === 'bn' && $fabric->name_bn ? $fabric->name_bn : $fabric->name }}</h1>
    <p class="lead">{{ app()->getLocale() === 'bn' && $fabric->description_bn ? $fabric->description_bn : $fabric->description }}</p>
    <div class="grid">
        <article class="card"><h3>Construction</h3><p>{{ $fabric->construction }}<br>{{ $fabric->weave }}</p></article>
        <article class="card"><h3>Fiber</h3><p>{{ $fabric->composition }}<br>{{ $fabric->stretch ? 'Stretch weft' : 'Rigid' }} · {{ $fabric->width_inch }}"</p></article>
        <article class="card"><h3>Finish</h3><p>{{ $fabric->finish }}<br>{{ $fabric->weight_gsm }} gsm</p></article>
    </div>
    @if ($fabric->sustainability_note)
        <h2>Water and chemistry</h2>
        <p>{{ $fabric->sustainability_note }}</p>
    @endif
    <p><a class="btn solid" href="{{ route('contact') }}">Ask for this cloth</a></p>
@endsection
