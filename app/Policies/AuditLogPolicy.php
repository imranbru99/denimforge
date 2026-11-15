<?php

namespace App\Policies;

class AuditLogPolicy extends ModulePolicy
{
    protected string $module = 'core';

    protected string $permission = 'audit_logs';

    protected bool $locked = true;
}
