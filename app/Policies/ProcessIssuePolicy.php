<?php

namespace App\Policies;

class ProcessIssuePolicy extends ModulePolicy
{
    protected string $module = 'quality';

    protected string $permission = 'process_issues';

    protected bool $locked = false;
}
