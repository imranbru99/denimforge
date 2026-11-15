<?php

namespace App\Policies;

class StockMovementPolicy extends ModulePolicy
{
    protected string $module = 'inventory';

    protected string $permission = 'stock_movements';

    protected bool $locked = false;
}
