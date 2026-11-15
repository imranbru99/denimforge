<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Auditable;

class StyleCosting extends Model
{
    use Auditable;

    protected $fillable = [
        'style_id',
        'currency',
        'fabric_cost',
        'dyeing_cost',
        'weaving_cost',
        'washing_cost',
        'labor_cost',
        'trim_cost',
        'overhead',
        'total',
        'quoted_price',
        'variance',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'fabric_cost' => 'decimal:4',
            'dyeing_cost' => 'decimal:4',
            'weaving_cost' => 'decimal:4',
            'washing_cost' => 'decimal:4',
            'labor_cost' => 'decimal:4',
            'trim_cost' => 'decimal:4',
            'overhead' => 'decimal:4',
            'total' => 'decimal:4',
            'quoted_price' => 'decimal:4',
            'variance' => 'decimal:4',
        ];
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class, 'style_id');
    }
}
