<?php

namespace App\Policies;

class MachineStoppagePolicy extends ModulePolicy
{
    protected string $module = 'maintenance';

    protected string $permission = 'machine_stoppages';

    protected bool $locked = false;
}
