<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Auditable;

class WeaveDefect extends Model
{
    use Auditable;

    protected $fillable = [
        'weave_production_id',
        'defect',
        'root_cause',
        'remedy',
        'meters',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'meters' => 'decimal:2',
        ];
    }

    public function production(): BelongsTo
    {
        return $this->belongsTo(WeaveProduction::class, 'weave_production_id');
    }
}
