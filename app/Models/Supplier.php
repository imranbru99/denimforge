<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class Supplier extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'name',
        'category',
        'country',
        'phone',
        'email',
        'lead_time_days',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function lots(): HasMany
    {
        return $this->hasMany(InventoryLot::class);
    }
}
