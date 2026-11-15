<!DOCTYPE html>
<html lang="bn">
<head><meta charset="utf-8"><style>body{font-family:"Noto Sans Bengali",sans-serif}</style></head>
<body>
    <p>{{ config('denimforge.plant.name_bn') }}</p>
    <h1>Job card {{ $workOrder->number }}</h1>
    <p>Stage {{ $workOrder->stage }} · {{ $workOrder->qty }} {{ $workOrder->uom }} · {{ $workOrder->status }}</p>
    <p>Style {{ $workOrder->style?->code }} {{ $workOrder->style?->name }}</p>
    <p>Buyer {{ $workOrder->buyer?->name }} · Order {{ $workOrder->order?->order_no }}</p>
    <p>Trace {{ $workOrder->trace_code }}</p>
    <p>{{ $workOrder->notes }}</p>
</body>
</html>
