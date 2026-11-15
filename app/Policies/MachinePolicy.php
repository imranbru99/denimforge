<?php

namespace App\Policies;

class MachinePolicy extends ModulePolicy
{
    protected string $module = 'maintenance';

    protected string $permission = 'machines';

    protected bool $locked = false;
}
