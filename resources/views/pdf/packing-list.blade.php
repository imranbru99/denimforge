<!DOCTYPE html>
<html lang="bn">
<head><meta charset="utf-8"><style>body{font-family:"Noto Sans Bengali",sans-serif} td,th{padding:4px;border-bottom:1px solid #ccc;text-align:left}</style></head>
<body>
    <h1>Packing list {{ $workOrder->number }}</h1>
    <p>{{ config('denimforge.plant.name') }} · {{ $workOrder->buyer?->name }} · {{ $workOrder->style?->code }}</p>
    <table>
        <tr><th>Bundle</th><th>Size</th><th>Qty</th><th>Trace</th></tr>
        @foreach ($bundles as $bundle)
            <tr>
                <td>{{ $bundle->code }}</td>
                <td>{{ $bundle->size }}</td>
                <td>{{ $bundle->qty }}</td>
                <td>{{ $bundle->trace_code }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
