<?php

namespace App\Policies;

class StyleCostingPolicy extends ModulePolicy
{
    protected string $module = 'finance';

    protected string $permission = 'style_costings';

    protected bool $locked = false;
}
