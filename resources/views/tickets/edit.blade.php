<x-app-layout>
    <x-slot name="header">Edit Ticket</x-slot>

    <div class="p-6">
        <form action="{{ route('tickets.update', $ticket) }}" method="POST">
            @csrf
            @method('PUT')

            <label>Title:</label>
            <input type="text" name="title" value="{{ $ticket->title }}" class="border rounded w-full p-2" required>

            <label>Description:</label>
            <textarea name="description" class="border rounded w-full p-2" required>{{ $ticket->description }}</textarea>

            <label>Assign to Agent:</label>
            <select name="assigned_to" class="border rounded w-full p-2">
                <option value="">Unassigned</option>
                @foreach ($agents as $agent)
                    <option value="{{ $agent->user_id }}" {{ $ticket->assigned_to == $agent->user_id ? 'selected' : '' }}>
                        {{ $agent->user->name }}
                    </option>
                @endforeach
            </select>

            <label>Priority:</label>
            <select name="priority" class="border rounded w-full p-2">
                @foreach ($priorities as $p)
                    <option value="{{ $p }}" {{ $ticket->priority == $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                @endforeach
            </select>

         

            <button type="submit" class="bg-blue-600 text-blue px-4 py-2 mt-3 rounded">Update</button>
        </form>
    </div>
</x-app-layout>
