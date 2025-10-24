<x-app-layout>
    <x-slot name="header">Create Ticket</x-slot>

    <div class="p-6">
         @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
        <form action="{{ route('tickets.store') }}" method="POST">
            @csrf

            <label>Title:</label>
            <input type="text" name="title" class="border rounded w-full p-2" required>

            <label for="category_id" class="block text-sm font-medium text-gray-700">Ticket Category</label>
             <select name="category_id" id="category_id" class="border rounded w-full p-2">
             <option value="">Select Category</option>
             @foreach($categories as $category)
             <option value="{{ $category->id }}">{{ $category->name }}</option>
             @endforeach
             </select>


            <label>Description:</label>
            <textarea name="description" class="border rounded w-full p-2" required></textarea>

            <label>Assign to Agent:</label>
        <select name="assigned_to" class="border rounded w-full p-2">
    <option value="">-- Select Agent --</option>
    @foreach($agents as $agent)
        <option value="{{ $agent->user->id }}">{{ $agent->user->name }}</option>
    @endforeach
</select><br>

            <label>Priority:</label>
            <select name="priority" class="border rounded w-full p-2">
                @foreach ($priorities as $p)
                    <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                @endforeach
            </select>

           

            <button type="submit" class="bg-green-500 text-white px-4 py-2 mt-3 rounded">Submit</button>
        </form>
    </div>
</x-app-layout>
