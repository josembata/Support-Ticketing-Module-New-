<?php

namespace App\Http\Controllers;

use App\Models\TicketComment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketCommentController extends Controller
{
public function store(Request $request, Ticket $ticket)
{
    $validated = $request->validate([
        'message' => 'required|string',
        'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

    // Prevent replies on closed tickets
    if ($ticket->status === 'closed') {
        return redirect()->route('tickets.index')
            ->with('error', 'You cannot reply — ticket is already closed. Please create another ticket.');
    }

    // Handle attachment upload
    if ($request->hasFile('attachment')) {
        $path = $request->file('attachment')->store('attachments', 'public');
        $validated['attachment'] = $path;
    }

    $validated['ticket_id'] = $ticket->ticket_id;
    $validated['user_id'] = auth()->id();

    // Create the comment
    $comment = \App\Models\TicketComment::create($validated);

    $ticketCreator = $ticket->creator;
    $assignedAgent = $ticket->assignedAgent;

    $oldStatus = $ticket->status; // Capture old status for history

    // Determine who added the comment
    if (auth()->id() === ($assignedAgent->id ?? null)) {
        // Agent commented → ensure status is in_progress (unless resolved)
        if ($ticket->status !== 'resolved') {
            $ticket->status = 'in_progress';
            $ticket->save();

            // Log history if method exists
            if (method_exists($ticket, 'logHistory')) {
                $ticket->logHistory('comment_added', $oldStatus, $ticket->status, 'Agent replied and ticket moved to in_progress.');
            }
        } else {
            // Log agent comment even if status not changed
            if (method_exists($ticket, 'logHistory')) {
                $ticket->logHistory('comment_added', $oldStatus, $oldStatus, 'Agent replied on a resolved ticket.');
            }
        }

        // Notify customer
        if ($ticketCreator) {
            $ticketCreator->notify(new \App\Notifications\TicketCommentNotification($comment));
        }

    } elseif (auth()->id() === ($ticketCreator->id ?? null)) {
        // Customer commented
        if (method_exists($ticket, 'logHistory')) {
            $ticket->logHistory('comment_added', $oldStatus, $oldStatus, 'Customer replied to ticket.');
        }

        // Notify agent if assigned
        if ($assignedAgent) {
            $assignedAgent->notify(new \App\Notifications\TicketCommentNotification($comment));
        }
    } else {
        // Log comment by other users if any
        if (method_exists($ticket, 'logHistory')) {
            $ticket->logHistory('comment_added', $oldStatus, $oldStatus, 'Other user added a comment.');
        }
    }

    return redirect()->back()->with('success', 'Comment added successfully.');
}



}
