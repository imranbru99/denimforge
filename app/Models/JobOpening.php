<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;
use ImranDev\UniversalSlug\Attributes\Slug;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

#[Slug(from: 'title', to: 'slug', skipIfFilled: true, routeKey: true)]
class JobOpening extends Model
{
    use Auditable, HasUniversalSlug;

    protected $fillable = [
        'title',
        'title_bn',
        'department',
        'location',
        'employment_type',
        'description',
        'description_bn',
        'is_open',
        'slug',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
        ];
    }
}
