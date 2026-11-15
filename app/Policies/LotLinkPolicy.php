<?php

namespace App\Policies;

class LotLinkPolicy extends ModulePolicy
{
    protected string $module = 'planning';

    protected string $permission = 'lot_links';

    protected bool $locked = false;
}
