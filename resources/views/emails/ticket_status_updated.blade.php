<h2>Ticket Status Update</h2>
<p>Hello {{ $ticket->user->name }},</p>

<p>Your ticket <strong>{{ $ticket->title }}</strong> status has changed:</p>
<ul>
    <li><strong>Old Status:</strong> {{ ucfirst($oldStatus) }}</li>
    <li><strong>New Status:</strong> {{ ucfirst($ticket->status) }}</li>
</ul>

<p>Click below to view your ticket:</p>
<a href="{{ url('/tickets/' . $ticket->ticket_id) }}">View Ticket</a>
<p>Thank you,<br/>Ticketing System</p>