<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class Material extends Model
{
    use Auditable;

    protected $fillable = [
        'sku',
        'name',
        'material_category_id',
        'supplier_id',
        'type',
        'uom',
        'reorder_level',
        'qty_on_hand',
        'unit_cost',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'reorder_level' => 'decimal:3',
            'qty_on_hand' => 'decimal:3',
            'unit_cost' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MaterialCategory::class, 'material_category_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function lots(): HasMany
    {
        return $this->hasMany(InventoryLot::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
