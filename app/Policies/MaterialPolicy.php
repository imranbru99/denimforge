<?php

namespace App\Policies;

class MaterialPolicy extends ModulePolicy
{
    protected string $module = 'inventory';

    protected string $permission = 'materials';

    protected bool $locked = false;
}
