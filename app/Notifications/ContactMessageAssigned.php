<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ContactMessageAssigned extends Notification
{
    use Queueable;

    public function __construct(public ContactMessage $message) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Contact Message Assigned to You')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A contact message has been assigned to you.')
            ->line('**From:** ' . $this->message->name . ' (' . $this->message->email . ')')
            ->line('**Subject:** ' . $this->message->subject)
            ->line('**Message:** ' . $this->message->message)
            ->action('View Message', url('/admin/contact-messages/' . $this->message->id . '/edit'))
            ->line('Please respond as soon as possible.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'from_name'  => $this->message->name,
            'from_email' => $this->message->email,
            'subject'    => $this->message->subject,
            'type'       => 'contact_message',
        ];
    }
}