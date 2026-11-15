<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;
use ImranDev\UniversalSlug\Attributes\Slug;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

#[Slug(from: 'name', to: 'slug', skipIfFilled: true, routeKey: true)]
class FabricProduct extends Model
{
    use Auditable, HasUniversalSlug;

    protected $fillable = [
        'name',
        'name_bn',
        'construction',
        'weave',
        'weight_oz',
        'weight_gsm',
        'composition',
        'shade',
        'finish',
        'stretch',
        'width_inch',
        'description',
        'description_bn',
        'sustainability_note',
        'is_published',
        'slug',
    ];

    protected function casts(): array
    {
        return [
            'weight_oz' => 'decimal:2',
            'weight_gsm' => 'decimal:2',
            'stretch' => 'boolean',
            'width_inch' => 'decimal:2',
            'is_published' => 'boolean',
        ];
    }

    public function styles(): HasMany
    {
        return $this->hasMany(Style::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }
}
