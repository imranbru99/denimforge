<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Auditable;

class Inquiry extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'subject',
        'message',
        'fabric_product_id',
        'status',
    ];

    protected function casts(): array
    {
        return [

        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(FabricProduct::class, 'fabric_product_id');
    }
}
