<?php

namespace App\Policies;

class WashRecipePolicy extends ModulePolicy
{
    protected string $module = 'washing';

    protected string $permission = 'wash_recipes';

    protected bool $locked = false;
}
