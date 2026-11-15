<?php

namespace App\Policies;

class BuyerOrderPolicy extends ModulePolicy
{
    protected string $module = 'planning';

    protected string $permission = 'buyer_orders';

    protected bool $locked = false;
}
