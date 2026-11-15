<?php

namespace App\Policies;

class InvoiceLinePolicy extends ModulePolicy
{
    protected string $module = 'finance';

    protected string $permission = 'invoice_lines';

    protected bool $locked = false;
}
