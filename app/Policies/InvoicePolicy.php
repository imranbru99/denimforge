<?php

namespace App\Policies;

class InvoicePolicy extends ModulePolicy
{
    protected string $module = 'finance';

    protected string $permission = 'invoices';

    protected bool $locked = false;
}
