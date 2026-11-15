<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Concerns\Auditable;

class FactoryDocument extends Model
{
    use Auditable;

    protected $fillable = [
        'title',
        'document_type',
        'path',
        'status',
        'extracted',
        'source_type',
        'source_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'extracted' => 'array',
        ];
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
