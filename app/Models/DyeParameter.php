<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Auditable;

class DyeParameter extends Model
{
    use Auditable;

    protected $fillable = [
        'dye_batch_id',
        'recorded_at',
        'station',
        'ph',
        'orp_mv',
        'concentration_gl',
        'temperature_c',
        'speed_mpm',
        'oxidation_seconds',
        'dips',
        'caustic_gl',
        'hydrosulfite_gl',
        'recorded_by_id',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
            'ph' => 'decimal:2',
            'orp_mv' => 'decimal:1',
            'concentration_gl' => 'decimal:2',
            'temperature_c' => 'decimal:1',
            'speed_mpm' => 'decimal:2',
            'caustic_gl' => 'decimal:2',
            'hydrosulfite_gl' => 'decimal:2',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(DyeBatch::class, 'dye_batch_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recorded_by_id');
    }
}
