<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class Inspection extends Model
{
    use Auditable;

    protected $fillable = [
        'inspection_no',
        'stage',
        'work_order_id',
        'style_id',
        'inspector_id',
        'yards',
        'width_inch',
        'total_points',
        'points_per_100',
        'accept_limit',
        'result',
        'inspected_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'yards' => 'decimal:2',
            'width_inch' => 'decimal:2',
            'points_per_100' => 'decimal:2',
            'accept_limit' => 'decimal:2',
            'inspected_at' => 'datetime',
        ];
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class, 'style_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'inspector_id');
    }

    public function defects(): HasMany
    {
        return $this->hasMany(InspectionDefect::class);
    }
}
