<?php

namespace App\Policies;

class EmployeeSkillPolicy extends ModulePolicy
{
    protected string $module = 'hr';

    protected string $permission = 'employee_skills';

    protected bool $locked = false;
}
