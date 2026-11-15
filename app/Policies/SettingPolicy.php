<?php

namespace App\Policies;

class SettingPolicy extends ModulePolicy
{
    protected string $module = 'core';

    protected string $permission = 'settings';

    protected bool $locked = false;
}
