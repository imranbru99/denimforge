<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class ModuleToggle extends Model
{
    use Auditable;

    protected $fillable = [
        'key',
        'name',
        'description',
        'enabled',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }
}
