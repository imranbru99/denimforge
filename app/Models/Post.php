<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;
use ImranDev\UniversalSlug\Attributes\Slug;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;

#[Slug(from: 'title', to: 'slug', skipIfFilled: true, routeKey: true)]
class Post extends Model
{
    use Auditable, HasUniversalSlug;

    protected $fillable = [
        'title',
        'title_bn',
        'excerpt',
        'excerpt_bn',
        'body',
        'body_bn',
        'published_at',
        'is_published',
        'slug',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }
}
