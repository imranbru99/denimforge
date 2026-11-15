<?php

namespace App\Policies;

class ProductionLinePolicy extends ModulePolicy
{
    protected string $module = 'sewing';

    protected string $permission = 'production_lines';

    protected bool $locked = false;
}
