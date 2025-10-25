<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tickets Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #1E3A8A; color: #fff; }
        .status-open { color: #2563eb; font-weight: bold; }
        .status-in_progress { color: #f59e0b; font-weight: bold; }
        .status-resolved { color: #10b981; font-weight: bold; }
        .status-closed { color: #ef4444; font-weight: bold; }
    </style>
</head>
<body>
   
   
    <h2 style="text-align: center;">Tickets Report</h2>
 <p  style="text-align: center; font-size: 14px;">Generated at: {{ now()->format('Y-m-d H:i') }}</p>
@if($fromDate || $toDate)
    <p style="text-align: center; font-size: 14px;">
        @if($fromDate) From: {{ \Carbon\Carbon::parse($fromDate)->format('Y-m-d') }} @endif
        @if($toDate) To: {{ \Carbon\Carbon::parse($toDate)->format('Y-m-d') }} @endif
    </p>
@endif
@if(isset($totalTickets))
    <div class="alert alert-info mt-3">
        <strong>Total Tickets:</strong> {{ $totalTickets }}
    </div>
@endif


    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th>
                <th>Creator</th>
                <th>Assigned Agent</th>
                <th>Status</th>
                <th>Solution</th>
                <th>Created At</th>
                <th>Resolved At</th>
                <th>Closed At</th>
                <th>Reopen At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->ticket_id }}</td>
                    <td>{{ $ticket->title }}</td>
                    <td>{{ $ticket->category->name ?? 'Uncategorized' }}</td>
                    <td>{{ $ticket->creator->name ?? '-' }}</td>
                    <td>{{ $ticket->assignedAgent->name ?? '-' }}</td>
                    <td class="status-{{ $ticket->status }}">{{ ucfirst($ticket->status) }}</td>
                    <td>{{ $ticket->solution ?? '-' }}</td>
                    <td>{{ $ticket->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td>{{ $ticket->resolved_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td>{{ $ticket->closed_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td>{{ $ticket->reopen_at?->format('Y-m-d H:i') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
