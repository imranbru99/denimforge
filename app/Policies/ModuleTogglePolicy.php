<?php

namespace App\Policies;

class ModuleTogglePolicy extends ModulePolicy
{
    protected string $module = 'core';

    protected string $permission = 'module_toggles';

    protected bool $locked = false;
}
