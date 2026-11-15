<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Auditable;

class FinishBatch extends Model
{
    use Auditable;

    protected $fillable = [
        'batch_no',
        'trace_code',
        'work_order_id',
        'machine_id',
        'process',
        'input_m',
        'output_m',
        'width_inch',
        'weight_gsm',
        'shrinkage_length',
        'shrinkage_width',
        'skew_percent',
        'status',
        'processed_on',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'input_m' => 'decimal:2',
            'output_m' => 'decimal:2',
            'width_inch' => 'decimal:2',
            'weight_gsm' => 'decimal:2',
            'shrinkage_length' => 'decimal:2',
            'shrinkage_width' => 'decimal:2',
            'skew_percent' => 'decimal:2',
            'processed_on' => 'date',
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
}
