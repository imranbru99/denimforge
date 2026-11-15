<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class Buyer extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'name',
        'country',
        'contact_name',
        'email',
        'phone',
        'incoterm',
        'payment_terms',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(BuyerOrder::class);
    }
}
