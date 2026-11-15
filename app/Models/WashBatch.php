<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Auditable;

class WashBatch extends Model
{
    use Auditable;

    protected $fillable = [
        'batch_no',
        'trace_code',
        'wash_recipe_id',
        'work_order_id',
        'qty_pcs',
        'shade_approved',
        'handfeel',
        'shrinkage_length',
        'shrinkage_width',
        'water_m3',
        'chemical_kg',
        'status',
        'washed_on',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'shade_approved' => 'boolean',
            'shrinkage_length' => 'decimal:2',
            'shrinkage_width' => 'decimal:2',
            'water_m3' => 'decimal:3',
            'chemical_kg' => 'decimal:3',
            'washed_on' => 'date',
        ];
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(WashRecipe::class, 'wash_recipe_id');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }
}
