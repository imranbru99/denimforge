<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: "Noto Sans Bengali", sans-serif; color: #1c1712; }
        h1 { font-size: 22px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border-bottom: 1px solid #ccc; text-align: left; padding: 6px; }
    </style>
</head>
<body>
    <p>{{ config('denimforge.plant.name_bn') }}</p>
    <h1>Commercial invoice {{ $invoice->number }}</h1>
    <p>{{ $invoice->buyer?->name }} · {{ optional($invoice->invoice_date)->format('d M Y') }} · {{ $invoice->currency }}</p>
    <table>
        <tr><th>Description</th><th>Qty</th><th>Price</th><th>Amount</th></tr>
        @foreach ($invoice->lines as $line)
            <tr>
                <td>{{ $line->description }}</td>
                <td>{{ $line->qty }}</td>
                <td>{{ $line->unit_price }}</td>
                <td>{{ $line->amount }}</td>
            </tr>
        @endforeach
    </table>
    <p>Subtotal {{ $invoice->subtotal }} · Freight {{ $invoice->freight }} · Total {{ $invoice->total }}</p>
    <p>{{ $invoice->notes }}</p>
</body>
</html>
