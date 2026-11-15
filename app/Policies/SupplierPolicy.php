<?php

namespace App\Policies;

class SupplierPolicy extends ModulePolicy
{
    protected string $module = 'inventory';

    protected string $permission = 'suppliers';

    protected bool $locked = false;
}
