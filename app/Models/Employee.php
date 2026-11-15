<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class Employee extends Model
{
    use Auditable;

    protected $fillable = [
        'employee_no',
        'name',
        'name_bn',
        'department_id',
        'shift_id',
        'user_id',
        'designation',
        'skill_group',
        'phone',
        'joined_on',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'joined_on' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(EmployeeSkill::class);
    }
}
