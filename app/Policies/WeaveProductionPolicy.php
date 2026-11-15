<?php

namespace App\Policies;

class WeaveProductionPolicy extends ModulePolicy
{
    protected string $module = 'weaving';

    protected string $permission = 'weave_productions';

    protected bool $locked = false;
}
