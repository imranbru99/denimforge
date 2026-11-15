<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Gateway-ready WhatsApp channel. The payload is the message a provider would send.
 */
class WhatsAppChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        Log::info('denimforge.whatsapp', $notification->toWhatsApp($notifiable));
    }
}
