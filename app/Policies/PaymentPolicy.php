<?php

namespace App\Policies;

class PaymentPolicy extends ModulePolicy
{
    protected string $module = 'finance';

    protected string $permission = 'payments';

    protected bool $locked = false;
}
