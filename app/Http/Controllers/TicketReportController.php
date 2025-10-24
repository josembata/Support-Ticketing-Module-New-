<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Http\Request;
use PDF;

class TicketReportController extends Controller
{
public function report(Request $request)
{
    // Fetch all categories for filter dropdown
    $categories = \App\Models\TicketCategory::all();

    // Fetch all agents (assigned to at least one ticket)
    $agents = \App\Models\User::whereIn('id', function ($query) {
        $query->select('assigned_to')
              ->from('tickets')
              ->whereNotNull('assigned_to');
    })->get();

    // Define possible statuses for filter dropdown
    $statuses = ['open', 'in_progress', 'resolved', 'closed'];

    //  query for tickets with related category, creator, and assigned agent
    $query = \App\Models\Ticket::with(['category', 'creator', 'assignedAgent']);

    // Filter by category if provided
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // Filter by agent if provided
    if ($request->filled('agent_id')) {
        $query->where('assigned_to', $request->agent_id);
    }

    // Filter by status if provided
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

      // Date filter: from_date and to_date
    if ($request->filled('from_date')) {
        $query->whereDate('created_at', '>=', $request->from_date);
    }
    if ($request->filled('to_date')) {
        $query->whereDate('created_at', '<=', $request->to_date);
    }

    $tickets = $query->latest()->get();

    return view('tickets.report', compact('tickets', 'categories', 'agents', 'statuses'));
}



}
