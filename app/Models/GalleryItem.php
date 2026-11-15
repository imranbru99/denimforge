<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;

class GalleryItem extends Model
{
    use Auditable;

    protected $fillable = [
        'title',
        'album',
        'caption',
        'image_path',
    ];

    protected function casts(): array
    {
        return [

        ];
    }
}
