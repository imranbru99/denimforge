<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class Machine extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'name',
        'type',
        'department_id',
        'status',
        'brand',
        'criticality',
        'installed_on',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'installed_on' => 'date',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function stoppages(): HasMany
    {
        return $this->hasMany(MachineStoppage::class);
    }

    public function maintenanceOrders(): HasMany
    {
        return $this->hasMany(MaintenanceOrder::class);
    }
}
