<?php

namespace App\Policies;

class AlertPolicy extends ModulePolicy
{
    protected string $module = 'automation';

    protected string $permission = 'alerts';

    protected bool $locked = false;
}
