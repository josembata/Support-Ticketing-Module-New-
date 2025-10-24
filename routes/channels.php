<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('ticket.{ticketId}', function ($user, $ticketId) {
    return \App\Models\Ticket::where('ticket_id', $ticketId)
        ->where(function($query) use ($user) {
            $query->where('created_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
        })->exists();
});
