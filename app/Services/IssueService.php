<?php

namespace App\Services;

use App\Domain\DenimKnowledge;
use App\Models\Alert;
use App\Models\KnowledgeArticle;
use App\Models\ProcessIssue;
use App\Models\User;
use App\Notifications\MillAlertNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class IssueService
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function open(Model $source, string $code, string $severity, string $message, array $context = []): ProcessIssue
    {
        $library = DenimKnowledge::find($code);
        $article = KnowledgeArticle::query()->where('code', $code)->first();
        $title = $library['title'] ?? str($code)->headline()->toString();
        $action = $library['remedies'] ?? 'Hold the lot and call the department supervisor.';

        $issue = ProcessIssue::query()
            ->where('code', $code)
            ->where('status', '!=', 'resolved')
            ->where('source_type', $source::class)
            ->where('source_id', $source->getKey())
            ->first();

        if (! $issue) {
            $issue = ProcessIssue::query()->create([
                'code' => $code,
                'title' => $title,
                'category' => $library['category'] ?? class_basename($source),
                'severity' => $severity,
                'status' => 'open',
                'recommended_action' => $action,
                'knowledge_article_id' => $article?->id,
                'source_type' => $source::class,
                'source_id' => $source->getKey(),
                'notes' => $message,
            ]);
        }

        $this->alert($source, $issue, $severity, $title, $message, $action, $context);

        return $issue;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function alert(Model $source, ?ProcessIssue $issue, string $severity, string $title, string $message, string $action, array $context = []): Alert
    {
        $alert = Alert::query()
            ->where('status', 'open')
            ->where('title', $title)
            ->where('source_type', $source::class)
            ->where('source_id', $source->getKey())
            ->first();

        $channels = [
            'mail' => (bool) config('denimforge.alerts.mail'),
            'sms' => (bool) config('denimforge.alerts.sms'),
            'whatsapp' => (bool) config('denimforge.alerts.whatsapp'),
            'database' => true,
        ];

        if (! $alert) {
            $alert = Alert::query()->create([
                'module' => $issue->category ?? 'mill',
                'severity' => $severity,
                'title' => $title,
                'message' => $message,
                'recommended_action' => $action,
                'channels' => $channels,
                'status' => 'open',
                'source_type' => $source::class,
                'source_id' => $source->getKey(),
            ]);

            $roles = Role::query()->whereIn('name', ['super-admin', 'factory-manager'])->pluck('name');
            $users = $roles->isEmpty() ? collect() : User::role($roles->all())->get();
            $notification = new MillAlertNotification($alert, $context);

            if ($users->isNotEmpty()) {
                Notification::send($users, $notification);
            }

            $alert->forceFill(['notified_at' => now()])->saveQuietly();
        }

        return $alert;
    }
}
