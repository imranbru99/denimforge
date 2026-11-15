<?php

namespace App\Policies;

class BundlePolicy extends ModulePolicy
{
    protected string $module = 'sewing';

    protected string $permission = 'bundles';

    protected bool $locked = false;
}
