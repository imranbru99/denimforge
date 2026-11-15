<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn (Model $model) => self::writeAudit($model, 'created'));
        static::updated(fn (Model $model) => self::writeAudit($model, 'updated'));
        static::deleted(fn (Model $model) => self::writeAudit($model, 'deleted'));
    }

    protected static function writeAudit(Model $model, string $event): void
    {
        if ($model instanceof AuditLog) {
            return;
        }

        try {
            if (! Schema::hasTable('audit_logs')) {
                return;
            }
        } catch (\Throwable) {
            return;
        }

        $hidden = ['password', 'remember_token'];
        $new = collect($model->getAttributes())->except($hidden)->all();
        $old = $event === 'created'
            ? []
            : collect($model->getOriginal())->except($hidden)->all();

        $prev = AuditLog::query()->latest('id')->value('hash') ?? 'GENESIS';
        $payload = [
            'event' => $event,
            'type' => $model::class,
            'id' => $model->getKey(),
            'old' => $old,
            'new' => $event === 'deleted' ? [] : $new,
        ];
        $hash = hash('sha256', $prev.'|'.json_encode($payload));

        AuditLog::query()->create([
            'user_id' => auth()->id(),
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'event' => $event,
            'old_values' => $old,
            'new_values' => $event === 'deleted' ? [] : $new,
            'hash' => $hash,
            'prev_hash' => $prev,
            'ip' => request()?->ip(),
            'user_agent' => substr((string) request()?->userAgent(), 0, 255),
        ]);
    }
}
