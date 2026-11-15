<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('denimforge.plant.name') }}</title>
    <meta name="description" content="{{ $description ?? __('site.home.lead') }}">
    <link rel="stylesheet" href="{{ asset('css/denimforge.css') }}">
    <script>
        const saved = localStorage.getItem('denimforge-theme');
        if (saved) document.documentElement.dataset.theme = saved;
    </script>
</head>
<body>
    @if (config('denimforge.demo'))
        <div class="banner"><div class="wrap">{{ __('site.demo') }} <a href="{{ url('/admin/login') }}">{{ __('site.nav.mill') }}</a></div></div>
    @endif
    <header class="wrap top">
        <a class="mark" href="{{ route('home') }}">DenimForge<small>{{ config('denimforge.plant.location') }}</small></a>
        <nav>
            <a href="{{ route('about') }}">{{ __('site.nav.about') }}</a>
            <a href="{{ route('process') }}">{{ __('site.nav.process') }}</a>
            <a href="{{ route('products') }}">{{ __('site.nav.products') }}</a>
            <a href="{{ route('quality') }}">{{ __('site.nav.quality') }}</a>
            <a href="{{ route('sustainability') }}">{{ __('site.nav.sustainability') }}</a>
            <a href="{{ route('gallery') }}">{{ __('site.nav.gallery') }}</a>
            <a href="{{ route('careers') }}">{{ __('site.nav.careers') }}</a>
            <a href="{{ route('blog') }}">{{ __('site.nav.blog') }}</a>
            <a href="{{ route('contact') }}">{{ __('site.nav.contact') }}</a>
        </nav>
        <div class="tools">
            <a href="{{ route('locale', 'en') }}">EN</a>
            <a href="{{ route('locale', 'bn') }}">বাং</a>
            <button type="button" id="theme">Theme</button>
        </div>
    </header>
    <main class="wrap">
        @yield('content')
    </main>
    <footer class="wrap">
        <div>
            <strong>{{ config('denimforge.plant.name') }}</strong><br>
            {{ config('denimforge.plant.name_bn') }} · {{ config('denimforge.plant.location_bn') }}
        </div>
        <div class="meta"><a href="{{ route('sitemap') }}">Sitemap</a></div>
    </footer>
    <script>
        document.getElementById('theme').addEventListener('click', () => {
            const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            document.documentElement.dataset.theme = next;
            localStorage.setItem('denimforge-theme', next);
        });
    </script>
</body>
</html>
