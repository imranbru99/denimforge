@extends('site.layout')

@section('content')
    <div class="kicker">{{ $kicker }}</div>
    <h1>{{ $title }}</h1>
    <div class="lead">{!! $html !!}</div>
@endsection
