<?php

namespace App\Policies;

class PostPolicy extends ModulePolicy
{
    protected string $module = 'website';

    protected string $permission = 'posts';

    protected bool $locked = false;
}
