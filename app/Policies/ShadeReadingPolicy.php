<?php

namespace App\Policies;

class ShadeReadingPolicy extends ModulePolicy
{
    protected string $module = 'dyeing';

    protected string $permission = 'shade_readings';

    protected bool $locked = false;
}
