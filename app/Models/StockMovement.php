<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Auditable;

class StockMovement extends Model
{
    use Auditable;

    protected $fillable = [
        'material_id',
        'inventory_lot_id',
        'direction',
        'qty',
        'reason',
        'reference',
        'moved_at',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:3',
            'moved_at' => 'datetime',
        ];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(InventoryLot::class, 'inventory_lot_id');
    }
}
