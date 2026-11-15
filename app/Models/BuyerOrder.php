<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToBuyerPortal;

class BuyerOrder extends Model
{
    use Auditable, BelongsToBuyerPortal;

    protected $fillable = [
        'order_no',
        'buyer_id',
        'style_id',
        'season',
        'qty',
        'uom',
        'ex_factory',
        'destination',
        'status',
        'currency',
        'contract_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'ex_factory' => 'date',
            'contract_price' => 'decimal:2',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class, 'style_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function samples(): HasMany
    {
        return $this->hasMany(Sample::class);
    }
}
