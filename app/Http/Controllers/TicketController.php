<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Agent;
use App\Models\TicketHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketStatusUpdatedNotification;
use Carbon\Carbon;
use PDF; 

class TicketController extends Controller
{
    public function index()
    {
            $user = auth()->user();

                // Tickets where the user is the creator or assigned agent
    $tickets = Ticket::with(['creator', 'assignedAgent'])
        ->where(function($query) use ($user) {
            $query->where('created_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
        })
        ->latest()
        ->get();

        return view('tickets.index', compact('tickets'));
    }


public function show(Ticket $ticket)
{
    $ticket->load(['category', 'assignedAgent', 'creator', 'comments.user']);

    return view('tickets.show', compact('ticket'));
}


    public function create()
    {
        $categories = \App\Models\TicketCategory::all();
        $agents = Agent::with('user')->where('status', 'active')->get();
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $statuses = ['open', 'in_progress', 'resolved', 'closed', 'reopened'];
        return view('tickets.create', compact('agents', 'priorities','categories', 'statuses'));
    }

 public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'category_id' => 'nullable|exists:ticket_categories,id',
        'description' => 'required|string',
        'assigned_to' => 'nullable|exists:users,id',
        'priority' => 'required|in:low,medium,high,urgent',
        'department_id' => 'nullable|exists:departments,department_id',
    ]);

    $validated['created_by'] = Auth::id();
    $validated['status'] = 'open';

    $ticket = Ticket::create($validated);
if ($ticket->assignedAgent) {
    $agent = $ticket->assignedAgent;
    if ($agent) {
        $agent->notify(new TicketAssignedNotification($ticket));
    }

    return redirect()->route('tickets.index')->with('success', 'Ticket created successfully.');
}

}
    public function edit(Ticket $ticket)
    {
        $agents = Agent::with('user')->where('status', 'active')->get();
        $priorities = ['low', 'medium', 'high', 'urgent'];
        

        return view('tickets.edit', compact('ticket', 'agents', 'priorities'));
    }





public function update(Request $request, Ticket $ticket)
{
  
}






    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
    }

   







public function resolve(Ticket $ticket)
{
    if (auth()->id() !== optional($ticket->assignedAgent)->id) {
        abort(403);
    }

    $oldStatus = $ticket->status;

    $ticket->status = 'resolved';
    $ticket->resolved_at = now(); 
    $ticket->save();

    // Log history if available
    if (method_exists($ticket, 'logHistory')) {
        $ticket->logHistory('resolved', $oldStatus, 'resolved', 'Ticket marked as resolved by agent.');
    }

    // Notify creator
    if ($ticket->creator) {
        $ticket->creator->notify(new \App\Notifications\TicketStatusUpdatedNotification($ticket, 'in_progress'));
    }

    return redirect()->back()->with('success', 'Ticket marked as resolved.');
}





public function showCloseForm(Ticket $ticket)
{
    // Ensure only agent can close, and 1 hour has passed
    if (auth()->id() !== optional($ticket->assignedAgent)->id) {
        abort(403);
    }

    if (!$ticket->resolved_at || now()->diffInMinutes($ticket->resolved_at) > 3) {
        return redirect()->back()->with('error', 'You can close the ticket only after 3 minutes.');
    }

    return view('tickets.close', compact('ticket'));
}


public function close(Request $request, Ticket $ticket)
{
    //if agent assigned
     if (auth()->id() !== optional($ticket->assignedAgent)->id) abort(403);

    $request->validate([
        'solution' => 'required|string|min:10',
    ]);

    $oldStatus = $ticket->status;  //capture old status

    $ticket->update([
        'solution' => $request->solution,
        'status' => 'closed',
        'closed_at' => now(),
    ]);

    if (method_exists($ticket, 'logHistory')) {
        $ticket->logHistory('closed', $oldStatus, 'closed');
    }

    if ($ticket->creator) {
        $ticket->creator->notify(new \App\Notifications\TicketStatusUpdatedNotification($ticket, $oldStatus));
    }

    return redirect()->route('tickets.show', $ticket->ticket_id)
                     ->with('success', ' Solution added. Ticket closed successfully.');
}


public function reopen(Ticket $ticket)
{
    // only creator can reopen and ticket not closed
    if (auth()->id() !== $ticket->created_by || $ticket->status === 'closed') {
        abort(403, 'You cannot reopen this ticket.');
    }

    // allow only within 3 minutes after resolved
    if (! $ticket->resolved_at || now()->diffInMinutes($ticket->resolved_at) > 3) {
        return back()->with('error', 'Ticket expired.');
    }

    $oldStatus = $ticket->status;

    $ticket->update([
        'status' => 'in_progress',
        'reopened_at' => now(),
        'resolved_at' => null, // clear resolved time
    ]);

    if (method_exists($ticket, 'logHistory')) {
        $ticket->logHistory('reopened', $oldStatus, 'in_progress');
    }

    if ($ticket->assignedAgent) {
        $ticket->assignedAgent->notify(new \App\Notifications\TicketStatusUpdatedNotification($ticket, $oldStatus));
    }

    return back()->with('success', 'Ticket reopened.');
}



public function reportPdf(Request $request)
{
    $query = \App\Models\Ticket::query();

    if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
    if ($request->filled('assigned_to')) $query->where('assigned_to', $request->assigned_to);
    if ($request->filled('status')) $query->where('status', $request->status);
    if ($request->filled('from_date')) $query->whereDate('created_at', '>=', $request->from_date);
    if ($request->filled('to_date')) $query->whereDate('created_at', '<=', $request->to_date);

    $tickets = $query->with(['creator', 'assignedAgent', 'category'])->get();

    // Ensure variables exist for the view
    $fromDate = $request->from_date ?? null;
    $toDate   = $request->to_date ?? null;
     $totalTickets = $tickets->count();

    $pdf = PDF::loadView('tickets.report_pdf', compact('tickets', 'fromDate', 'toDate', 'totalTickets'));
    return $pdf->download('tickets_report_'.now()->format('Ymd_His').'.pdf');
}





}
