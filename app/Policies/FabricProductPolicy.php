<?php

namespace App\Policies;

class FabricProductPolicy extends ModulePolicy
{
    protected string $module = 'catalog';

    protected string $permission = 'fabric_products';

    protected bool $locked = false;
}
