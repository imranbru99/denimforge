<?php

namespace App\Policies;

class GalleryItemPolicy extends ModulePolicy
{
    protected string $module = 'website';

    protected string $permission = 'gallery_items';

    protected bool $locked = false;
}
