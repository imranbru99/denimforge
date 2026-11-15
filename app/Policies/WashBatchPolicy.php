<?php

namespace App\Policies;

class WashBatchPolicy extends ModulePolicy
{
    protected string $module = 'washing';

    protected string $permission = 'wash_batches';

    protected bool $locked = false;
}
