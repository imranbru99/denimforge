<?php

namespace App\Policies;

class DepartmentPolicy extends ModulePolicy
{
    protected string $module = 'hr';

    protected string $permission = 'departments';

    protected bool $locked = false;
}
