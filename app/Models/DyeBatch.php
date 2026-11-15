<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class DyeBatch extends Model
{
    use Auditable;

    protected $fillable = [
        'batch_no',
        'trace_code',
        'method',
        'shade_card_id',
        'style_id',
        'work_order_id',
        'machine_id',
        'yarn_lot_id',
        'operator_id',
        'set_length_m',
        'actual_length_m',
        'target_concentration_gl',
        'status',
        'started_at',
        'ended_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'set_length_m' => 'decimal:2',
            'actual_length_m' => 'decimal:2',
            'target_concentration_gl' => 'decimal:2',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function shadeCard(): BelongsTo
    {
        return $this->belongsTo(ShadeCard::class, 'shade_card_id');
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class, 'style_id');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    public function yarnLot(): BelongsTo
    {
        return $this->belongsTo(YarnLot::class, 'yarn_lot_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'operator_id');
    }

    public function parameters(): HasMany
    {
        return $this->hasMany(DyeParameter::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(ShadeReading::class);
    }
}
