<?php

namespace App\Policies;

class SamplePolicy extends ModulePolicy
{
    protected string $module = 'planning';

    protected string $permission = 'samples';

    protected bool $locked = false;
}
