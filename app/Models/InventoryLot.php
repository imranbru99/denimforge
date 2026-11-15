<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class InventoryLot extends Model
{
    use Auditable;

    protected $fillable = [
        'material_id',
        'supplier_id',
        'lot_no',
        'trace_code',
        'qty_received',
        'qty_on_hand',
        'uom',
        'unit_cost',
        'received_on',
        'expires_on',
        'location',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'qty_received' => 'decimal:3',
            'qty_on_hand' => 'decimal:3',
            'unit_cost' => 'decimal:2',
            'received_on' => 'date',
            'expires_on' => 'date',
        ];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
