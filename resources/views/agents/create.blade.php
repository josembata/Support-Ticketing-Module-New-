<x-app-layout>
    <x-slot name="header">Create Agent</x-slot>

    <div class="p-6">
        <form action="{{ route('agents.store') }}" method="POST">
            @csrf
            <label>User:</label>
            <select name="user_id" required>
                <option value="">Select User</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>

            <label>Status:</label>
            <select name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
        </form>
    </div>
</x-app-layout>
