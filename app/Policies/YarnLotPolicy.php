<?php

namespace App\Policies;

class YarnLotPolicy extends ModulePolicy
{
    protected string $module = 'yarn';

    protected string $permission = 'yarn_lots';

    protected bool $locked = false;
}
