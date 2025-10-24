<x-app-layout>
    <x-slot name="header">Tickets List</x-slot>

    <div class="p-6">
         @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
        <a href="{{ route('tickets.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Create Ticket</a>
        <table class="min-w-full mt-4 border text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border">#</th>
                    <th class="px-3 py-2 border">Title</th>
                    <th class="px-3 py-2 border">Created By</th>
                    <th class="px-3 py-2 border">Assigned Agent</th>
                    <th class="px-3 py-2 border">Priority</th>
                    <th class="px-3 py-2 border">Status</th>
                    <th class="px-3 py-2 border text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tickets as $ticket)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-3 py-2">{{ $ticket->ticket_id }}</td>
                        <td class="px-3 py-2">{{ $ticket->title }}</td>
                        <td class="px-3 py-2">{{ $ticket->creator->name ?? 'N/A' }}</td>
                        <td class="px-3 py-2">{{ $ticket->assignedAgent->name ?? 'Unassigned' }}</td>
                        <td class="px-3 py-2">
                            <span class="
                                px-2 py-1 rounded text-white 
                                @if($ticket->priority == 'urgent') bg-red-600
                                @elseif($ticket->priority == 'high') bg-green-600
                                @elseif($ticket->priority == 'medium') bg-green-500
                                @else bg-green-500 @endif
                            ">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td class="px-3 py-2">
                            <span class="
                                px-2 py-1 rounded 
                                @if($ticket->status == 'open') bg-blue-100 text-blue-800
                                @elseif($ticket->status == 'in_progress') bg-yellow-100 text-yellow-800
                                @elseif($ticket->status == 'resolved') bg-green-100 text-green-800
                                @elseif($ticket->status == 'closed') bg-gray-200 text-gray-800
                                @else bg-purple-100 text-purple-800 @endif
                            ">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 flex gap-2 justify-center">
                            {{-- View/Comment Button --}}
                            <a href="{{ route('tickets.show', $ticket->ticket_id) }}" 
                               class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">
                                Reply
                            </a>
   
                            {{-- Edit --}}
                            <a href="{{ route('tickets.edit', $ticket->ticket_id) }}" 
                               class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                                 Edit
                            </a>

                            {{-- Delete --}}
                            <form action="{{ route('tickets.destroy', $ticket->ticket_id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                                     Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
