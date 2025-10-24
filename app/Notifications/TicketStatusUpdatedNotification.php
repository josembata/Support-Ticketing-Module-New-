<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Macellan\Twilio\TwilioSmsMessage;

class TicketStatusUpdatedNotification extends Notification 
{


    public $ticket;
    public $oldStatus;

    public function __construct(Ticket $ticket, $oldStatus)
    {
        $this->ticket = $ticket;
        $this->oldStatus = $oldStatus;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }



    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Ticket Status Has Been Updated')
               ->markdown('vendor.notifications.email', [
            'logo' => public_path('images/uat2.jpg'), 
        ])
            ->greeting('Dear. ' . $notifiable->name)
            ->line('Your ticket "' . $this->ticket->title . '" status has changed.')
            ->line('Old Status: ' . ucfirst($this->oldStatus))
            ->line('New Status: ' . ucfirst($this->ticket->status))
            ->action('View Ticket', url('/tickets/' . $this->ticket->ticket_id))
            ->line('Thank you for using our support system.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->ticket->status,
            'message' => 'Ticket status updated from ' . ucfirst($this->oldStatus) . ' to ' . ucfirst($this->ticket->status),
            'url' => url('/tickets/' . $this->ticket->id),
        ];
    }
}
