<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class Department extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'name',
        'name_bn',
    ];

    protected function casts(): array
    {
        return [

        ];
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function machines(): HasMany
    {
        return $this->hasMany(Machine::class);
    }
}
