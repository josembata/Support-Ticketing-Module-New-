<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Category
        </h2>
    </x-slot>

    <div class="p-6">
        <form method="POST" action="{{ route('categories.update', $category->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-gray-700 font-semibold">Category Name</label>
                <input type="text" name="name" value="{{ $category->name }}" class="border rounded w-full p-2" required>
                @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-gray-700 font-semibold">Description</label>
                <textarea name="description" class="border rounded w-full p-2">{{ $category->description }}</textarea>
            </div>

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
                Update Category
            </button>
        </form>
    </div>
</x-app-layout>
