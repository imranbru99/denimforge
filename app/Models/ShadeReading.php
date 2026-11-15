<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Auditable;

class ShadeReading extends Model
{
    use Auditable;

    protected $fillable = [
        'dye_batch_id',
        'position',
        'l_star',
        'a_star',
        'b_star',
        'delta_e',
        'photo_path',
        'ai_summary',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'l_star' => 'decimal:2',
            'a_star' => 'decimal:2',
            'b_star' => 'decimal:2',
            'delta_e' => 'decimal:3',
            'ai_summary' => 'array',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(DyeBatch::class, 'dye_batch_id');
    }
}
