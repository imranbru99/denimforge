<?php

namespace App\Policies;

class JobOpeningPolicy extends ModulePolicy
{
    protected string $module = 'website';

    protected string $permission = 'job_openings';

    protected bool $locked = false;
}
