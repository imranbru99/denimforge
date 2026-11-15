<?php

namespace App\Policies;

class KnowledgeArticlePolicy extends ModulePolicy
{
    protected string $module = 'quality';

    protected string $permission = 'knowledge_articles';

    protected bool $locked = false;
}
