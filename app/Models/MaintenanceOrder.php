<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Auditable;

class MaintenanceOrder extends Model
{
    use Auditable;

    protected $fillable = [
        'number',
        'machine_id',
        'type',
        'status',
        'symptom',
        'root_cause',
        'action_taken',
        'downtime_minutes',
        'reported_at',
        'completed_at',
        'assigned_employee_id',
    ];

    protected function casts(): array
    {
        return [
            'reported_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class, 'machine_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
    }
}
