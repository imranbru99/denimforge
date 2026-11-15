<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Concerns\Auditable;

class LotLink extends Model
{
    use Auditable;

    protected $fillable = [
        'origin_type',
        'origin_id',
        'destination_type',
        'destination_id',
        'qty',
        'uom',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:3',
        ];
    }

    public function origin(): MorphTo
    {
        return $this->morphTo();
    }

    public function destination(): MorphTo
    {
        return $this->morphTo();
    }
}
