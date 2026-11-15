<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class Style extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'name',
        'buyer_id',
        'fabric_product_id',
        'season',
        'wash_type',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [

        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function fabricProduct(): BelongsTo
    {
        return $this->belongsTo(FabricProduct::class, 'fabric_product_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(BuyerOrder::class);
    }

    public function techPacks(): HasMany
    {
        return $this->hasMany(TechPack::class);
    }

    public function costings(): HasMany
    {
        return $this->hasMany(StyleCosting::class);
    }
}
