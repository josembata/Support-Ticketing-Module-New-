<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TicketAssignedNotification extends Notification 
{


    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // send both email + save to DB
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Ticket Assigned to You')
              ->markdown('vendor.notifications.email', [
            'logo' => public_path('images/uat2.jpg'), 
        ])
            ->greeting('Dear.  ' . $notifiable->name)
            ->line('A new ticket has been assigned to you.')
            ->line('Title: ' . $this->ticket->title)
            ->line('Priority: ' . ucfirst($this->ticket->priority))
           ->action('View Ticket', url('/tickets/' . $this->ticket->ticket_id))
            ->line('Thank you for your support efforts!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'priority' => $this->ticket->priority,
            'message' => 'A new ticket has been assigned to you.',
              'url' => url('/tickets/' . $this->ticket->ticket_id),
        ];
    }
}
