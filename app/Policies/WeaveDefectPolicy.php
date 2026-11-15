<?php

namespace App\Policies;

class WeaveDefectPolicy extends ModulePolicy
{
    protected string $module = 'weaving';

    protected string $permission = 'weave_defects';

    protected bool $locked = false;
}
