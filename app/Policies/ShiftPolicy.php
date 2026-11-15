<?php

namespace App\Policies;

class ShiftPolicy extends ModulePolicy
{
    protected string $module = 'hr';

    protected string $permission = 'shifts';

    protected bool $locked = false;
}
