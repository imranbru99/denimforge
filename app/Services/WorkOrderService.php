<?php

namespace App\Services;

use App\Models\BuyerOrder;
use App\Models\WorkOrder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WorkOrderService
{
    /**
     * @return Collection<int, WorkOrder>
     */
    public function fromOrder(BuyerOrder $order): Collection
    {
        return DB::transaction(function () use ($order) {
            $existing = WorkOrder::query()->where('buyer_order_id', $order->id)->get();

            if ($existing->isNotEmpty()) {
                return $existing;
            }

            $meters = round($order->qty * 1.45, 2);
            $stages = [
                ['dyeing', $meters, 'm'],
                ['weaving', $meters, 'm'],
                ['finishing', $meters, 'm'],
                ['cutting', $order->qty, 'pcs'],
                ['sewing', $order->qty, 'pcs'],
                ['washing', $order->qty, 'pcs'],
                ['packing', $order->qty, 'pcs'],
            ];

            $parent = null;
            $created = collect();

            foreach ($stages as $index => [$stage, $qty, $uom]) {
                $orderNo = $order->order_no.'-'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
                $workOrder = WorkOrder::query()->create([
                    'number' => $orderNo,
                    'buyer_order_id' => $order->id,
                    'buyer_id' => $order->buyer_id,
                    'style_id' => $order->style_id,
                    'parent_id' => $parent?->id,
                    'stage' => $stage,
                    'qty' => $qty,
                    'uom' => $uom,
                    'status' => 'planned',
                    'planned_start' => now()->addDays($index * 2)->toDateString(),
                    'planned_end' => now()->addDays($index * 2 + 2)->toDateString(),
                    'trace_code' => 'WO-'.$orderNo,
                    'notes' => 'Generated from '.$order->order_no,
                ]);
                $parent = $workOrder;
                $created->push($workOrder);
            }

            if ($order->status === 'draft') {
                $order->forceFill(['status' => 'confirmed'])->saveQuietly();
            }

            return $created;
        });
    }
}
