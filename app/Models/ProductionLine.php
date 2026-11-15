<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class ProductionLine extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'name',
        'target_per_hour',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function outputs(): HasMany
    {
        return $this->hasMany(HourlyOutput::class);
    }
}
