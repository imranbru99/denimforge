@extends('site.layout')

@section('content')
    <section class="hero">
        <div>
            <div class="kicker">{{ __('site.home.kicker') }}</div>
            <h1>{{ __('site.home.title') }}</h1>
            <p class="lead">{{ __('site.home.lead') }}</p>
            <p>
                <a class="btn solid" href="{{ route('products') }}">{{ __('site.home.cta') }}</a>
                <a class="btn" href="{{ route('contact') }}">{{ __('site.home.inquire') }}</a>
            </p>
        </div>
        <div class="rope" role="img" aria-label="Indigo ropes"></div>
    </section>

    <h2>{{ __('site.home.processes') }}</h2>
    <div class="grid">
        <article class="card"><h3>Rope and sheet dye</h3><p>pH, ORP, concentration, speed, dips, and oxidation time on every running set.</p></article>
        <article class="card"><h3>Weave and finish</h3><p>Meters, stoppages, reed marks, sanfor shrinkage, and skew before the knife.</p></article>
        <article class="card"><h3>Cut, sew, wash</h3><p>Marker yield, bundle codes, hourly line output, enzyme recipes, and water.</p></article>
    </div>

    <h2>{{ __('site.home.certs') }}</h2>
    <div class="grid">
        <article class="card"><h3>4-point</h3><p>Points per 100 square yards against the buyer limit.</p></article>
        <article class="card"><h3>Shade card</h3><p>Head, tail, side, and center read as L*a*b* and delta E.</p></article>
        <article class="card"><h3>Lot trace</h3><p>Cotton bale to yarn, dyed warp, cloth, bundle, and wash.</p></article>
    </div>

    @if ($fabrics->isNotEmpty())
        <h2>{{ __('site.nav.products') }}</h2>
        <div class="grid">
            @foreach ($fabrics as $fabric)
                <article class="card">
                    <div class="kicker">{{ $fabric->weight_oz }} oz · {{ $fabric->shade }}</div>
                    <h3><a href="{{ route('products.show', $fabric) }}">{{ app()->getLocale() === 'bn' && $fabric->name_bn ? $fabric->name_bn : $fabric->name }}</a></h3>
                    <p>{{ $fabric->construction }} · {{ $fabric->composition }}</p>
                </article>
            @endforeach
        </div>
    @endif

    @if ($posts->isNotEmpty())
        <h2>{{ __('site.home.news') }}</h2>
        @foreach ($posts as $post)
            <p><a href="{{ route('blog.show', $post) }}">{{ app()->getLocale() === 'bn' && $post->title_bn ? $post->title_bn : $post->title }}</a></p>
        @endforeach
    @endif
@endsection
