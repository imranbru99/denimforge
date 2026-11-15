<?php

namespace App\Policies;

class InquiryPolicy extends ModulePolicy
{
    protected string $module = 'website';

    protected string $permission = 'inquiries';

    protected bool $locked = false;
}
