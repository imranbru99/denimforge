<?php

namespace App\Policies;

class MaterialCategoryPolicy extends ModulePolicy
{
    protected string $module = 'inventory';

    protected string $permission = 'material_categories';

    protected bool $locked = false;
}
