<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Concerns\Auditable;

class ProcessIssue extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'title',
        'category',
        'severity',
        'status',
        'recommended_action',
        'knowledge_article_id',
        'source_type',
        'source_id',
        'assigned_employee_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [

        ];
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(KnowledgeArticle::class, 'knowledge_article_id');
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
    }
}
