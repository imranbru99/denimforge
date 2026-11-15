<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Auditable;

class HourlyOutput extends Model
{
    use Auditable;

    protected $fillable = [
        'production_line_id',
        'style_id',
        'work_order_id',
        'hour_start',
        'target',
        'actual',
        'operators',
        'efficiency',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'hour_start' => 'datetime',
            'efficiency' => 'decimal:2',
        ];
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(ProductionLine::class, 'production_line_id');
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class, 'style_id');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }
}
