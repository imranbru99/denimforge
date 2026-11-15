<?php

namespace App\Policies;

class ShadeCardPolicy extends ModulePolicy
{
    protected string $module = 'dyeing';

    protected string $permission = 'shade_cards';

    protected bool $locked = false;
}
