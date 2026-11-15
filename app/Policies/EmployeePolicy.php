<?php

namespace App\Policies;

class EmployeePolicy extends ModulePolicy
{
    protected string $module = 'hr';

    protected string $permission = 'employees';

    protected bool $locked = false;
}
