<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Auditable;

class InspectionDefect extends Model
{
    use Auditable;

    protected $fillable = [
        'inspection_id',
        'classification',
        'defect_code',
        'length_inch',
        'points',
        'location',
        'photo_path',
        'ai_detection',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'length_inch' => 'decimal:2',
            'ai_detection' => 'array',
        ];
    }

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }
}
