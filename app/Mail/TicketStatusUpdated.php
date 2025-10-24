<?php


namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $oldStatus;

    public function __construct(Ticket $ticket, $oldStatus)
    {
        $this->ticket = $ticket;
        $this->oldStatus = $oldStatus;
    }

    public function build()
    {
        return $this->subject('Your Ticket Status Has Been Updated')
            ->view('emails.ticket_status_updated');
    }
}
