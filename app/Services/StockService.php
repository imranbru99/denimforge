<?php

namespace App\Services;

use App\Models\InventoryLot;
use App\Models\Material;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    /**
     * @param  array<string, mixed>  $extra
     */
    public function receive(Material $material, float $qty, string $lotNo, array $extra = []): InventoryLot
    {
        return DB::transaction(function () use ($material, $qty, $lotNo, $extra) {
            $lot = InventoryLot::query()->create([
                'material_id' => $material->id,
                'supplier_id' => $extra['supplier_id'] ?? $material->supplier_id,
                'lot_no' => $lotNo,
                'trace_code' => $extra['trace_code'] ?? $lotNo,
                'qty_received' => $qty,
                'qty_on_hand' => $qty,
                'uom' => $extra['uom'] ?? $material->uom,
                'unit_cost' => $extra['unit_cost'] ?? $material->unit_cost,
                'received_on' => $extra['received_on'] ?? now()->toDateString(),
                'location' => $extra['location'] ?? 'Main store',
                'notes' => $extra['notes'] ?? null,
            ]);

            StockMovement::query()->create([
                'material_id' => $material->id,
                'inventory_lot_id' => $lot->id,
                'direction' => 'in',
                'qty' => $qty,
                'reason' => $extra['reason'] ?? 'receipt',
                'reference' => $lotNo,
                'moved_at' => now(),
            ]);

            $material->increment('qty_on_hand', $qty);

            return $lot;
        });
    }

    /**
     * Issue stock oldest lot first.
     *
     * @return array<int, StockMovement>
     */
    public function issue(Material $material, float $qty, string $reason, ?string $reference = null): array
    {
        if ($qty <= 0) {
            throw new RuntimeException('Issue quantity must be positive.');
        }

        return DB::transaction(function () use ($material, $qty, $reason, $reference) {
            $remaining = $qty;
            $movements = [];

            $lots = InventoryLot::query()
                ->where('material_id', $material->id)
                ->where('qty_on_hand', '>', 0)
                ->orderBy('received_on')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($lots as $lot) {
                if ($remaining <= 0) {
                    break;
                }

                $take = min($remaining, (float) $lot->qty_on_hand);
                $lot->decrement('qty_on_hand', $take);

                $movements[] = StockMovement::query()->create([
                    'material_id' => $material->id,
                    'inventory_lot_id' => $lot->id,
                    'direction' => 'out',
                    'qty' => $take,
                    'reason' => $reason,
                    'reference' => $reference,
                    'moved_at' => now(),
                ]);

                $remaining -= $take;
            }

            if ($remaining > 0.0001) {
                throw new RuntimeException('Not enough stock of '.$material->sku.' to issue '.$qty.' '.$material->uom.'.');
            }

            $material->decrement('qty_on_hand', $qty);

            return $movements;
        });
    }

    public function flagLowStock(): int
    {
        $count = 0;

        Material::query()
            ->where('is_active', true)
            ->whereColumn('qty_on_hand', '<=', 'reorder_level')
            ->where('reorder_level', '>', 0)
            ->each(function (Material $material) use (&$count) {
                app(IssueService::class)->alert(
                    $material,
                    null,
                    'warning',
                    'Low stock: '.$material->name,
                    $material->sku.' is at '.$material->qty_on_hand.' '.$material->uom.' against a reorder level of '.$material->reorder_level.'.',
                    'Raise a purchase request and issue the oldest remaining lot first.',
                );
                $count++;
            });

        return $count;
    }
}
