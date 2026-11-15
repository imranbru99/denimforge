<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Auditable;

class YarnLot extends Model
{
    use Auditable;

    protected $fillable = [
        'lot_no',
        'trace_code',
        'inventory_lot_id',
        'supplier_id',
        'count_ne',
        'twist_tpi',
        'strength_rkm',
        'cv_percent',
        'composition',
        'end_use',
        'cones',
        'kg',
        'received_on',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'count_ne' => 'decimal:2',
            'twist_tpi' => 'decimal:2',
            'strength_rkm' => 'decimal:2',
            'cv_percent' => 'decimal:2',
            'kg' => 'decimal:3',
            'received_on' => 'date',
        ];
    }

    public function inventoryLot(): BelongsTo
    {
        return $this->belongsTo(InventoryLot::class, 'inventory_lot_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
