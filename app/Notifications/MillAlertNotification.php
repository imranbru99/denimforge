<?php

namespace App\Notifications;

use App\Models\Alert;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MillAlertNotification extends Notification
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(public Alert $alert, public array $context = []) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (config('denimforge.alerts.mail')) {
            $channels[] = 'mail';
        }

        if (config('denimforge.alerts.sms')) {
            $channels[] = SmsChannel::class;
        }

        if (config('denimforge.alerts.whatsapp')) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('['.strtoupper($this->alert->severity).'] '.$this->alert->title)
            ->line($this->alert->message)
            ->line('Recommended action: '.$this->alert->recommended_action);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'alert_id' => $this->alert->id,
            'title' => $this->alert->title,
            'severity' => $this->alert->severity,
            'message' => $this->alert->message,
            'action' => $this->alert->recommended_action,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toSms(object $notifiable): array
    {
        return $this->gatewayPayload($notifiable, 'sms');
    }

    /**
     * @return array<string, mixed>
     */
    public function toWhatsApp(object $notifiable): array
    {
        return $this->gatewayPayload($notifiable, 'whatsapp');
    }

    /**
     * @return array<string, mixed>
     */
    private function gatewayPayload(object $notifiable, string $channel): array
    {
        return [
            'channel' => $channel,
            'to' => $notifiable->email ?? null,
            'text' => $this->alert->title.' — '.$this->alert->message,
            'context' => $this->context,
        ];
    }
}
