<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Auditable;

class EmployeeSkill extends Model
{
    use Auditable;

    protected $fillable = [
        'employee_id',
        'skill',
        'level',
        'notes',
    ];

    protected function casts(): array
    {
        return [

        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
