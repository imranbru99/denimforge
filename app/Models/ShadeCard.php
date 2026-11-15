<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class ShadeCard extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'name',
        'style_id',
        'method',
        'standard_l',
        'standard_a',
        'standard_b',
        'ph_min',
        'ph_max',
        'orp_min',
        'orp_max',
        'temp_min',
        'temp_max',
        'concentration_gl',
        'dips',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'standard_l' => 'decimal:2',
            'standard_a' => 'decimal:2',
            'standard_b' => 'decimal:2',
            'ph_min' => 'decimal:2',
            'ph_max' => 'decimal:2',
            'orp_min' => 'decimal:1',
            'orp_max' => 'decimal:1',
            'temp_min' => 'decimal:1',
            'temp_max' => 'decimal:1',
            'concentration_gl' => 'decimal:2',
        ];
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class, 'style_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(DyeBatch::class);
    }
}
