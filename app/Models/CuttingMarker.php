<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class CuttingMarker extends Model
{
    use Auditable;

    protected $fillable = [
        'marker_no',
        'style_id',
        'work_order_id',
        'fabric_m',
        'marker_efficiency',
        'lay_count',
        'pcs',
        'consumption_m',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'fabric_m' => 'decimal:2',
            'marker_efficiency' => 'decimal:2',
            'consumption_m' => 'decimal:3',
        ];
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class, 'style_id');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function bundles(): HasMany
    {
        return $this->hasMany(Bundle::class);
    }
}
