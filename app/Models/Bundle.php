<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Auditable;

class Bundle extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'trace_code',
        'cutting_marker_id',
        'style_id',
        'size',
        'color',
        'qty',
        'status',
        'qr_payload',
    ];

    protected function casts(): array
    {
        return [

        ];
    }

    public function marker(): BelongsTo
    {
        return $this->belongsTo(CuttingMarker::class, 'cutting_marker_id');
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class, 'style_id');
    }
}
