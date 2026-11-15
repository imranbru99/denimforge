<?php

namespace App\Policies;

class InspectionDefectPolicy extends ModulePolicy
{
    protected string $module = 'quality';

    protected string $permission = 'inspection_defects';

    protected bool $locked = false;
}
