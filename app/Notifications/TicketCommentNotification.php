<?php

namespace App\Notifications;

use App\Models\TicketComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TicketCommentNotification extends Notification 
{
    

    public $comment;

    public function __construct(TicketComment $comment)
    {
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Comment on Ticket #' . $this->comment->ticket->ticket_id)
                  ->markdown('vendor.notifications.email', [
            'logo' => public_path('images/uat2.jpg'), 
        ])
            ->greeting('Hello! Dear.  ' . $notifiable->name)
            ->line($this->comment->user->name . ' commented on the ticket: "' . $this->comment->message . '"')
            ->action('View Ticket', url('/tickets/' . $this->comment->ticket->ticket_id))
            ->line('Thank you for your support efforts!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->comment->ticket->ticket_id,
            'comment_id' => $this->comment->comment_id,
            'message' => $this->comment->message,
            'comment_by' => $this->comment->user->name,
            'url' => url('/tickets/' . $this->comment->ticket->ticket_id),
        ];
    }
}
