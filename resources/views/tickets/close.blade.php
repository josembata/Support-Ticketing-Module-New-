<x-app-layout>
    <div class="max-w-3xl mx-auto py-10 px-6">
        <div class="bg-white shadow-lg rounded-2xl p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 border-b pb-2">
                Close Ticket #{{ $ticket->ticket_id }}
            </h2>

            <div class="mb-6">
                <p class="text-gray-600 mb-1">
                    <span class="font-medium text-gray-800">Title:</span> {{ $ticket->title }}
                </p>
                <p class="text-gray-600">
                    <span class="font-medium text-gray-800">Status:</span>
                    <span class="px-2 py-1 rounded-full text-sm 
                        @if($ticket->status === 'resolved') bg-blue-100 text-blue-800 
                        @elseif($ticket->status === 'closed') bg-gray-100 text-gray-800 
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ ucfirst($ticket->status) }}
                    </span>
                </p>
            </div>

            <form method="POST" action="{{ route('tickets.close', $ticket->ticket_id) }}" class="space-y-6">
                @csrf

                <div>
                    <label for="solution" class="block text-gray-700 font-medium mb-2">
                        Solution Description <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="solution" 
                        name="solution" 
                        rows="5" 
                        required 
                        minlength="10"
                        placeholder="Describe how the issue was resolved..."
                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm p-3 text-gray-700 resize-none"
                    >{{ old('solution') }}</textarea>

                    @error('solution')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('tickets.show', $ticket->ticket_id) }}" 
                       class="bg-gray-200 text-gray-700 hover:bg-gray-300 px-5 py-2 rounded-xl mr-3 transition duration-200">
                        Cancel
                    </a>

                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-xl shadow-md transition duration-200">
                        <i class="fa fa-check-circle mr-1"></i> Submit Solution
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
