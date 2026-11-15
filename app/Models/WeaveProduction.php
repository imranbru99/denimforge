<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class WeaveProduction extends Model
{
    use Auditable;

    protected $fillable = [
        'roll_no',
        'trace_code',
        'work_order_id',
        'machine_id',
        'warp_yarn_lot_id',
        'weft_yarn_lot_id',
        'meters',
        'epi',
        'ppi',
        'width_inch',
        'status',
        'produced_on',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'meters' => 'decimal:2',
            'epi' => 'decimal:1',
            'ppi' => 'decimal:1',
            'width_inch' => 'decimal:2',
            'produced_on' => 'date',
        ];
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    public function warpYarnLot(): BelongsTo
    {
        return $this->belongsTo(YarnLot::class, 'warp_yarn_lot_id');
    }

    public function weftYarnLot(): BelongsTo
    {
        return $this->belongsTo(YarnLot::class, 'weft_yarn_lot_id');
    }

    public function defects(): HasMany
    {
        return $this->hasMany(WeaveDefect::class);
    }
}
