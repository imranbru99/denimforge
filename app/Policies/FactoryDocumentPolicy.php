<?php

namespace App\Policies;

class FactoryDocumentPolicy extends ModulePolicy
{
    protected string $module = 'documents';

    protected string $permission = 'factory_documents';

    protected bool $locked = false;
}
