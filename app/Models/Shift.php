<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class Shift extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'starts_at',
        'ends_at',
        'is_night',
    ];

    protected function casts(): array
    {
        return [
            'is_night' => 'boolean',
        ];
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
