<?php

namespace App\Policies;

class CuttingMarkerPolicy extends ModulePolicy
{
    protected string $module = 'cutting';

    protected string $permission = 'cutting_markers';

    protected bool $locked = false;
}
