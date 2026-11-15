<?php

namespace App\Policies;

class InventoryLotPolicy extends ModulePolicy
{
    protected string $module = 'inventory';

    protected string $permission = 'inventory_lots';

    protected bool $locked = false;
}
