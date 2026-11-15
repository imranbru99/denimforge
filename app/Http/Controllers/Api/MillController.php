<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnalyzeShadeRequest;
use App\Http\Requests\IssueStockRequest;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\Alert;
use App\Models\BuyerOrder;
use App\Models\DyeBatch;
use App\Models\FabricProduct;
use App\Models\Material;
use App\Models\User;
use App\Services\AttendanceBridge;
use App\Services\DocumentIntake;
use App\Services\ReportExporter;
use App\Services\ShadeAnalyzer;
use App\Services\StockService;
use App\Services\TraceabilityService;
use App\Services\WorkOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ImranDevBd\AttendanceHub\Models\AttendanceDevice;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MillController extends Controller
{
    public function catalog(): JsonResponse
    {
        $products = FabricProduct::query()
            ->where('is_published', true)
            ->get(['id', 'name', 'name_bn', 'slug', 'construction', 'weight_oz', 'composition', 'shade', 'finish', 'stretch']);

        return response()->json(['data' => $products]);
    }

    public function catalogItem(FabricProduct $fabricProduct): JsonResponse
    {
        abort_unless($fabricProduct->is_published, 404);

        return response()->json(['data' => $fabricProduct]);
    }

    public function dashboard(AttendanceBridge $attendance): JsonResponse
    {
        $this->millUser();

        return response()->json([
            'plant_time' => $attendance->plantClock(),
            'running_dye_batches' => DyeBatch::query()->where('status', 'running')->count(),
            'open_alerts' => Alert::query()->where('status', 'open')->count(),
            'punches_today' => $attendance->todayCount(),
        ]);
    }

    public function trace(string $code, TraceabilityService $trace): JsonResponse
    {
        $this->millUser();
        $record = $trace->findByCode($code);
        abort_unless($record, 404);

        return response()->json($trace->lineage($record));
    }

    public function analyze(AnalyzeShadeRequest $request, DyeBatch $dyeBatch, ShadeAnalyzer $analyzer): JsonResponse
    {
        return response()->json($analyzer->analyze($dyeBatch, $request->validated('note')));
    }

    public function document(StoreDocumentRequest $request, DocumentIntake $intake): JsonResponse
    {
        $file = $request->file('file');
        $path = $file->store('documents');
        $document = $intake->store($path, [
            'title' => $request->validated('title') ?? $file->getClientOriginalName(),
            'document_type' => $request->validated('document_type') ?? 'other',
        ]);

        return response()->json(['data' => $document], 201);
    }

    public function report(string $report, ReportExporter $exporter): BinaryFileResponse
    {
        $user = $this->millUser();
        abort_unless($user->hasAnyRole(['super-admin', 'factory-manager', 'finance', 'production', 'qc']), 403);

        return $exporter->download($report);
    }

    public function issue(IssueStockRequest $request, StockService $stock): JsonResponse
    {
        $material = Material::query()->findOrFail($request->validated('material_id'));
        $movements = $stock->issue(
            $material,
            (float) $request->validated('qty'),
            $request->validated('reason'),
            $request->validated('reference'),
        );

        return response()->json([
            'material' => $material->fresh(),
            'movements' => $movements,
        ]);
    }

    public function workOrders(BuyerOrder $order, WorkOrderService $planner): JsonResponse
    {
        abort_unless(request()->user()?->can('create', \App\Models\WorkOrder::class), 403);

        return response()->json(['data' => $planner->fromOrder($order)]);
    }

    public function alerts(): JsonResponse
    {
        $this->millUser();

        return response()->json([
            'data' => Alert::query()->latest('id')->limit(50)->get(),
        ]);
    }

    public function syncAttendance(Request $request, AttendanceBridge $attendance): JsonResponse
    {
        $user = $this->millUser();
        abort_unless($user->hasAnyRole(['super-admin', 'factory-manager', 'hr']), 403);
        $device = AttendanceDevice::query()->findOrFail($request->integer('device_id'));

        return response()->json(['synced' => $attendance->sync($device)]);
    }

    private function millUser(): User
    {
        $user = request()->user();
        abort_unless($user instanceof User && $user->hasAnyRole(User::MILL_ROLES), 403);

        return $user;
    }
}
