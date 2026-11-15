@extends('admin-panel::layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="page-header">
            <div>
                <h1 class="page-title">DenimForge console</h1>
                <p class="page-subtitle">{{ config('denimforge.plant.name') }} · {{ config('denimforge.plant.location') }}</p>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;">
            <x-admin-panel::card>
                <h2 style="margin-top:0;">Mill ERP</h2>
                <p>Dyeing, weaving, quality, orders, and the rest of the floor live in the Filament panel.</p>
                <a href="{{ url('/admin') }}">Open /admin</a>
            </x-admin-panel::card>
            <x-admin-panel::card>
                <h2 style="margin-top:0;">Public site</h2>
                <p>Catalog, process, careers, and the inquiry form.</p>
                <a href="{{ url('/') }}">Open the website</a>
            </x-admin-panel::card>
            <x-admin-panel::card>
                <h2 style="margin-top:0;">Modules</h2>
                <p>Switch a module off in Module toggles inside the ERP. Core stays on. A disabled module drops out of the menu and its policy refuses access.</p>
            </x-admin-panel::card>
        </div>
    </div>
@endsection
