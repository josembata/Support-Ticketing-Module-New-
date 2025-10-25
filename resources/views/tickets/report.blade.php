<x-app-layout>
<div class="container mx-auto p-6">

    <h2 class="text-xl font-bold mb-4">Ticket Report</h2>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('tickets.report') }}" class="mb-4 flex gap-2">
        <select name="category_id" class="border rounded p-2">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id')==$category->id?'selected':'' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <select name="assigned_to" class="border rounded p-2">
            <option value="">All Agents</option>
            @foreach($agents as $agent)
                <option value="{{ $agent->id }}" {{ request('assigned_to')==$agent->id?'selected':'' }}>
                    {{ $agent->name }}
                </option>
            @endforeach
        </select>

        <select name="status" class="border rounded p-2">
            <option value="">All Status</option>
            <option value="open" {{ request('status')=='open'?'selected':'' }}>Open</option>
            <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>In Progress</option>
            <option value="resolved" {{ request('status')=='resolved'?'selected':'' }}>Resolved</option>
            <option value="closed" {{ request('status')=='closed'?'selected':'' }}>Closed</option>
        </select>

    <!-- date filter -->
     <label for="">From Date: </label>
    <input type="date" name="from_date" class="border rounded p-2" value="{{ request('from_date') }}">
    <label for="">To Date:</label>
    <input type="date" name="to_date" class="border rounded p-2" value="{{ request('to_date') }}">


        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>

        {{-- PDF Button --}}
        <a href="{{ route('tickets.report.pdf', request()->all()) }}" 
           class="bg-green-600 text-white px-4 py-2 rounded ml-2">Download PDF</a>
    </form>

    {{-- Table --}}
    <table class="table-auto w-full text-sm border-collapse border border-gray-300">
        @if(isset($totalTickets))
    <div class="alert alert-info mt-3">
        <strong>Total Tickets:</strong> {{ $totalTickets }}
    </div>
@endif

        <thead class="bg-gray-100">
            <tr>
                <th class="border px-3 py-2">ID</th>
                <th class="border px-3 py-2">Title</th>
                <th class="border px-3 py-2">Category</th>
                <th class="border px-3 py-2">Creator</th>
                <th class="border px-3 py-2">Assigned Agent</th>
                <th class="border px-3 py-2">Status</th>
                <th class="border px-3 py-2">Solution</th>
                <th class="border px-3 py-2">Created At</th>
                <th class="border px-3 py-2">Resolved At</th>
                <th class="border px-3 py-2">Closed At</th>
                <th class="border px-3 py-2">Reopen At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $ticket)
                <tr>
                    <td class="border px-3 py-2">{{ $ticket->ticket_id }}</td>
                    <td class="border px-3 py-2">{{ $ticket->title }}</td>
                    <td class="border px-3 py-2">{{ $ticket->category->name ?? 'Uncategorized' }}</td>
                    <td class="border px-3 py-2">{{ $ticket->creator->name ?? '-' }}</td>
                    <td class="border px-3 py-2">{{ $ticket->assignedAgent->name ?? '-' }}</td>
                    <td class="border px-3 py-2">{{ ucfirst($ticket->status) }}</td>
                    <td class="border px-3 py-2">{{ $ticket->solution ?? '-' }}</td>
                    <td class="border px-3 py-2">{{ $ticket->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td class="border px-3 py-2">{{ $ticket->resolved_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td class="border px-3 py-2">{{ $ticket->closed_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td class="border px-3 py-2">{{ $ticket->reopened_at?->format('Y-m-d H:i') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
</x-app-layout>
