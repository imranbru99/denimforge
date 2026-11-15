<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToBuyerPortal;

class WorkOrder extends Model
{
    use Auditable, BelongsToBuyerPortal;

    protected $fillable = [
        'number',
        'buyer_order_id',
        'buyer_id',
        'style_id',
        'parent_id',
        'stage',
        'qty',
        'uom',
        'status',
        'planned_start',
        'planned_end',
        'trace_code',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:2',
            'planned_start' => 'date',
            'planned_end' => 'date',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(BuyerOrder::class, 'buyer_order_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class, 'style_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
