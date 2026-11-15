@extends('site.layout', ['title' => __('site.contact.title')])

@section('content')
    <div class="kicker">Commercial desk</div>
    <h1>{{ __('site.contact.title') }}</h1>
    @if (session('sent'))
        <p class="ok">{{ __('site.contact.sent') }}</p>
    @endif
    <form method="post" action="{{ route('contact.store') }}">
        @csrf
        <label>Name <input name="name" value="{{ old('name') }}" required></label>
        <label>Company <input name="company" value="{{ old('company') }}"></label>
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>Phone <input name="phone" value="{{ old('phone') }}"></label>
        <label>Cloth
            <select name="fabric_product_id">
                <option value="">General</option>
                @foreach ($fabrics as $fabric)
                    <option value="{{ $fabric->id }}" @selected(old('fabric_product_id') == $fabric->id)>{{ $fabric->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Subject <input name="subject" value="{{ old('subject') }}" required></label>
        <label>Message <textarea name="message" rows="6" required>{{ old('message') }}</textarea></label>
        @if ($errors->any())
            <p>{{ $errors->first() }}</p>
        @endif
        <button class="btn solid" type="submit">Send</button>
    </form>
@endsection
