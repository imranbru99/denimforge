<?php

namespace App\Policies;

class InspectionPolicy extends ModulePolicy
{
    protected string $module = 'quality';

    protected string $permission = 'inspections';

    protected bool $locked = false;
}
