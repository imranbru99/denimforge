<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\WorkOrder;
use ImranDev\UnicodePdf\Facades\UnicodePdf;
use Symfony\Component\HttpFoundation\Response;

class MillPdf
{
    public function invoice(Invoice $invoice): Response
    {
        $invoice->load('buyer', 'lines', 'order');

        return UnicodePdf::view('pdf.invoice', ['invoice' => $invoice])
            ->preset('bengali')
            ->locale(app()->getLocale())
            ->title('Invoice '.$invoice->number)
            ->download($invoice->number.'.pdf');
    }

    public function jobCard(WorkOrder $workOrder): Response
    {
        $workOrder->load('order', 'style', 'buyer', 'parent', 'children');

        return UnicodePdf::view('pdf.job-card', ['workOrder' => $workOrder])
            ->preset('bengali')
            ->locale(app()->getLocale())
            ->title('Job card '.$workOrder->number)
            ->download($workOrder->number.'.pdf');
    }

    public function packingList(WorkOrder $workOrder): Response
    {
        $workOrder->load('style', 'buyer', 'order');

        return UnicodePdf::view('pdf.packing-list', [
            'workOrder' => $workOrder,
            'bundles' => \App\Models\Bundle::query()->where('style_id', $workOrder->style_id)->get(),
        ])
            ->preset('bengali')
            ->title('Packing list '.$workOrder->number)
            ->download('packing-'.$workOrder->number.'.pdf');
    }
}
