<?php

namespace App\Policies;

class StylePolicy extends ModulePolicy
{
    protected string $module = 'planning';

    protected string $permission = 'styles';

    protected bool $locked = false;
}
