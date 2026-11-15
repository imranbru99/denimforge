<?php

namespace App\Policies;

class DyeParameterPolicy extends ModulePolicy
{
    protected string $module = 'dyeing';

    protected string $permission = 'dye_parameters';

    protected bool $locked = false;
}
