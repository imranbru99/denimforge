<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Trace {{ $lineage['record']['label'] }}</title>
    <link rel="stylesheet" href="{{ asset('css/denimforge.css') }}">
</head>
<body>
<main class="wrap">
    <p><a href="{{ url('/admin') }}">Back to the mill</a></p>
    <h1>{{ $lineage['record']['type'] }} {{ $lineage['record']['label'] }}</h1>
    <h2>Upstream</h2>
    <ul>
        @forelse ($lineage['upstream'] as $node)
            <li>{{ $node['type'] }} {{ $node['label'] }} @if($node['via_qty']) · {{ $node['via_qty'] }} {{ $node['uom'] }} @endif</li>
        @empty
            <li>No earlier lot.</li>
        @endforelse
    </ul>
    <h2>Downstream</h2>
    <ul>
        @forelse ($lineage['downstream'] as $node)
            <li>{{ $node['type'] }} {{ $node['label'] }}</li>
        @empty
            <li>Nothing issued onward yet.</li>
        @endforelse
    </ul>
</main>
</body>
</html>
