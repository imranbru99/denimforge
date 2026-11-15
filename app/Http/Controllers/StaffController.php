<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\MillPdf;
use App\Services\ReportExporter;
use App\Services\TraceabilityService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class StaffController extends Controller
{
    public function trace(Request $request, string $code, TraceabilityService $trace): View
    {
        $this->staff($request);
        $record = $trace->findByCode($code);
        abort_unless($record, 404);

        return view('staff.trace', [
            'lineage' => $trace->lineage($record),
        ]);
    }

    public function invoice(Request $request, Invoice $invoice, MillPdf $pdf): Response
    {
        $this->finance($request);

        return $pdf->invoice($invoice);
    }

    public function jobCard(Request $request, WorkOrder $workOrder, MillPdf $pdf): Response
    {
        $this->staff($request);

        return $pdf->jobCard($workOrder);
    }

    public function packingList(Request $request, WorkOrder $workOrder, MillPdf $pdf): Response
    {
        $this->staff($request);

        return $pdf->packingList($workOrder);
    }

    public function report(Request $request, string $report, ReportExporter $exporter): Response
    {
        $user = $this->staff($request);
        abort_unless($user->hasAnyRole(['super-admin', 'factory-manager', 'finance', 'production', 'qc']), 403);
        abort_unless(in_array($report, ['production', 'quality', 'shade', 'cost'], true), 404);

        return $exporter->download($report);
    }

    private function staff(Request $request): User
    {
        $user = $request->user();
        abort_unless($user instanceof User && $user->hasAnyRole(User::MILL_ROLES), 403);

        return $user;
    }

    private function finance(Request $request): void
    {
        $user = $this->staff($request);
        abort_unless($user->can('invoices.view'), 403);
    }
}
