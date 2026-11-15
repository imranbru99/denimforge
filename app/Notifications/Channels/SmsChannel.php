<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Gateway-ready SMS channel. Plug a provider in by replacing the log call.
 */
class SmsChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        Log::info('denimforge.sms', $notification->toSms($notifiable));
    }
}
