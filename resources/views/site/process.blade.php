@extends('site.layout', ['title' => 'Manufacturing process'])

@section('content')
    <div class="kicker">From bale to bundle</div>
    <h1>The cloth has a path, and the path has a lot number.</h1>
    <div class="steps">
        <article><h3>Cotton and yarn</h3><p>Bales are received FIFO. Yarn lots carry count Ne, twist, strength RKM, and CV%. Warp and weft are different lots.</p></article>
        <article><h3>Warping</h3><p>Rope or sheet. Tension is part of shade. A slack rope becomes a streak after the wash.</p></article>
        <article><h3>Indigo</h3><p>Boxes hold pH about 11.2–11.8 and ORP about −760 to −860 mV for rope indigo. Concentration is fed, not bucketed. Oxidation time is part of the depth.</p></article>
        <article><h3>Weaving</h3><p>Looms record meters and stops. Broken ends, double picks, reed marks, and slubs get a cause and a remedy.</p></article>
        <article><h3>Finishing</h3><p>Singe, desize, mercerize, sanforize, straighten skew. Length and width shrinkage are written down.</p></article>
        <article><h3>Cutting</h3><p>Marker efficiency and consumption metres per piece. Lays become bundles with a code.</p></article>
        <article><h3>Sewing</h3><p>Line target against the hour. Operator count sits next to the output.</p></article>
        <article><h3>Wash and pack</h3><p>Recipe, shade approval, hand feel, water, and chemicals. Then a carton, a packing list, and an invoice.</p></article>
    </div>
@endsection
