<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class MaterialCategory extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'type',
    ];

    protected function casts(): array
    {
        return [

        ];
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }
}
