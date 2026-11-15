<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class KnowledgeArticle extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'title',
        'title_bn',
        'category',
        'symptoms',
        'causes',
        'checks',
        'remedies',
        'parameters',
    ];

    protected function casts(): array
    {
        return [
            'parameters' => 'array',
        ];
    }
}
