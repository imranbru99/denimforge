<?php

namespace App\Policies;

class HourlyOutputPolicy extends ModulePolicy
{
    protected string $module = 'sewing';

    protected string $permission = 'hourly_outputs';

    protected bool $locked = false;
}
