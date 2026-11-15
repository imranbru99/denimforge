<?php

namespace App\Policies;

class DyeBatchPolicy extends ModulePolicy
{
    protected string $module = 'dyeing';

    protected string $permission = 'dye_batches';

    protected bool $locked = false;
}
