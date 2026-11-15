<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Concerns\Auditable;

class Alert extends Model
{
    use Auditable;

    protected $fillable = [
        'module',
        'severity',
        'title',
        'message',
        'recommended_action',
        'channels',
        'status',
        'source_type',
        'source_id',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'channels' => 'array',
            'notified_at' => 'datetime',
        ];
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
