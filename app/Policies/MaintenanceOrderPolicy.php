<?php

namespace App\Policies;

class MaintenanceOrderPolicy extends ModulePolicy
{
    protected string $module = 'maintenance';

    protected string $permission = 'maintenance_orders';

    protected bool $locked = false;
}
