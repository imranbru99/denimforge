<?php

namespace App\Policies;

class TechPackPolicy extends ModulePolicy
{
    protected string $module = 'planning';

    protected string $permission = 'tech_packs';

    protected bool $locked = false;
}
