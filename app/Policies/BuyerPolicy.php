<?php

namespace App\Policies;

class BuyerPolicy extends ModulePolicy
{
    protected string $module = 'commercial';

    protected string $permission = 'buyers';

    protected bool $locked = false;
}
