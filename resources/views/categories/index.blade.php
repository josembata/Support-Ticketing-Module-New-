<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ticket Categories
        </h2>
    </x-slot>

    <div class="p-6 space-y-4">
        <a href="{{ route('categories.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
           + Add New Category
        </a>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow rounded p-4">
            <table class="w-full border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-left">#</th>
                        <th class="border px-3 py-2 text-left">Name</th>
                        <th class="border px-3 py-2 text-left">Description</th>
                        <th class="border px-3 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $index => $category)
                    <tr>
                        <td class="border px-3 py-2">{{ $index + 1 }}</td>
                        <td class="border px-3 py-2 font-semibold text-gray-700">{{ $category->name }}</td>
                        <td class="border px-3 py-2">{{ $category->description ?? '-' }}</td>
                        <td class="border px-3 py-2">
                            <a href="{{ route('categories.edit', $category->id) }}"
                               class="text-blue-600 hover:underline">Edit</a> |
                            <form action="{{ route('categories.destroy', $category->id) }}"
                                  method="POST"
                                  style="display:inline"
                                  onsubmit="return confirm('Are you sure you want to delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $categories->links() }}
    </div>
</x-app-layout>
