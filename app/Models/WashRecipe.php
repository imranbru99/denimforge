<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class WashRecipe extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'type',
        'temperature_c',
        'time_minutes',
        'liquor_ratio',
        'ph',
        'water_liters_per_kg',
        'chemicals',
        'steps',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'temperature_c' => 'decimal:1',
            'ph' => 'decimal:2',
            'water_liters_per_kg' => 'decimal:2',
            'chemicals' => 'array',
        ];
    }

    public function batches(): HasMany
    {
        return $this->hasMany(WashBatch::class);
    }
}
