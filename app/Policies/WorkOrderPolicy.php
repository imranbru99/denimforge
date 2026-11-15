<?php

namespace App\Policies;

class WorkOrderPolicy extends ModulePolicy
{
    protected string $module = 'planning';

    protected string $permission = 'work_orders';

    protected bool $locked = false;
}
