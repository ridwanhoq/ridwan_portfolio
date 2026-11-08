<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessage extends Notification
{
    public function __construct(public readonly ContactMessage $message) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New portfolio message: '.$this->message->subject)
            ->replyTo($this->message->email, $this->message->name)
            ->greeting('New message from '.$this->message->name)
            ->line('From: '.$this->message->name.' <'.$this->message->email.'>')
            ->line('Subject: '.$this->message->subject)
            ->line($this->message->message)
            ->action('Open in admin', route('admin.messages.show', $this->message));
    }
}
