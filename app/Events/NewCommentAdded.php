<?php

namespace App\Events;

use App\Models\TicketComment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class NewCommentAdded implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $comment;

    public function __construct(TicketComment $comment)
    {
        // eager-load the user to include their name in the broadcast payload
        $this->comment = $comment->load('user');
    }

    public function broadcastOn()
    {
        return new Channel('ticket.' . $this->comment->ticket_id);
    }

    public function broadcastAs()
    {
        return 'comment.posted';
    }

    // Optional: specify what data is sent to frontend
    public function broadcastWith()
    {
        return [
            'id' => $this->comment->id,
            'message' => $this->comment->message,
            'user' => [
                'id' => $this->comment->user->id,
                'name' => $this->comment->user->name,
            ],
            'ticket_id' => $this->comment->ticket_id,
            'created_at' => $this->comment->created_at->toDateTimeString(),
        ];
    }
}
