<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestNotificationEmail extends Notification
{
    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Portfolio: test notification')
            ->line('This is a test from your portfolio admin panel.')
            ->line('If you received it, contact-form notifications will reach this address.')
            ->action('Open the admin panel', route('admin.dashboard'));
    }
}
