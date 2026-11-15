<?php

namespace App\Policies;

class FinishBatchPolicy extends ModulePolicy
{
    protected string $module = 'finishing';

    protected string $permission = 'finish_batches';

    protected bool $locked = false;
}
