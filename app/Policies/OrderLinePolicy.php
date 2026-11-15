<?php

namespace App\Policies;

class OrderLinePolicy extends ModulePolicy
{
    protected string $module = 'planning';

    protected string $permission = 'order_lines';

    protected bool $locked = false;
}
