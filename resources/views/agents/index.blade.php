<x-app-layout>
    <x-slot name="header">Agent List</x-slot>

    <div class="p-6">
        <a href="{{ route('agents.create') }}" class="bg-blue-800 text-white px-4 py-2 rounded">Add Agent</a>
        <table class="min-w-full mt-4 border">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($agents as $agent)
                    <tr>
                        <td>{{ $agent->agent_id }}</td>
                        <td>{{ $agent->user->name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($agent->status) }}</td>
                        <td>
                            <a href="{{ route('agents.edit', $agent) }}" class="text-blue-600">Edit</a> |
                            <form action="{{ route('agents.destroy', $agent) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
