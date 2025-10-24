<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add New Category
        </h2>
    </x-slot>

    <div class="p-6">
        <form method="POST" action="{{ route('categories.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-gray-700 font-semibold">Category Name</label>
                <input type="text" name="name" class="border rounded w-full p-2" required>
                @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-gray-700 font-semibold">Description</label>
                <textarea name="description" class="border rounded w-full p-2"></textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                Save Category
            </button>
        </form>
    </div>
</x-app-layout>
