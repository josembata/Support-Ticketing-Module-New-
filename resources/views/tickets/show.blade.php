<x-app-layout>
    <x-slot name="header">
         Ticket: {{ $ticket->title }}
          @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    </x-slot>

    <div class="p-6 space-y-6">
        {{-- Ticket Summary --}}
        <div class="bg-white p-6 rounded-2xl shadow border border-blue-100">
            <h2 class="text-2xl font-semibold text-blue-900">{{ $ticket->title }}</h2>
            <p class="text-gray-700 mt-2">{{ $ticket->description }}</p>

            <div class="mt-4 text-sm text-gray-600 space-y-1">
                <p><strong>Created by:</strong> {{ $ticket->creator->name ?? 'N/A' }}</p>
                <p><strong>Assigned to:</strong> {{ $ticket->assignedAgent->name ?? 'Unassigned' }}</p>
                <p><strong>Status:</strong> <span class="text-blue-700 font-semibold">{{ ucfirst(str_replace('_',' ', $ticket->status)) }}</span></p>
                <p><strong>Priority:</strong> <span class="capitalize">{{ $ticket->priority }}</span></p>
            </div>

{{-- Agent Options --}}
@if(Auth::id() === optional($ticket->assignedAgent)->id)
    @if($ticket->status === 'in_progress')
        <form action="{{ route('tickets.resolve', $ticket->ticket_id) }}" method="POST" class="d-inline">
            @csrf
            @method('PUT')
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                Mark Resolved
            </button>
        </form>

    @elseif($ticket->status === 'resolved')
        @php
            // Calculate time since resolved
            $minutesPassed = $ticket->resolved_at ? \Carbon\Carbon::parse($ticket->resolved_at)->diffInMinutes(now()) : 0;
            $remaining = max(0, 3 - $minutesPassed);
            $canClose = $minutesPassed >= 3;
        @endphp

        @if($canClose)
            <a href="{{ route('tickets.close.form', $ticket->ticket_id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                Close Ticket
            </a>
        @else
            <button class="btn btn-secondary" disabled>
                Close available after {{ $remaining }} mins
            </button>
        @endif
    @endif
@endif

{{-- User (creator) Reopen Option --}}
@if(Auth::id() === $ticket->created_by && $ticket->status === 'resolved')
    @php
        $minutesSinceResolved = $ticket->resolved_at ? \Carbon\Carbon::parse($ticket->resolved_at)->diffInMinutes(now()) : 0;
        $canReopen = $minutesSinceResolved <= 3;
    @endphp

    @if($canReopen)
        <form action="{{ route('tickets.reopen', $ticket->ticket_id) }}" method="POST" class="d-inline">
            @csrf
            @method('PUT')
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                Reopen Ticket
            </button>
        </form>
    @else
        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
            Reopen period expired
        </button>
    @endif
@endif





        </div>

        {{-- Conversation Section --}}
        <div class="bg-white p-6 rounded-2xl shadow border border-blue-100">
            <h3 class="text-xl font-semibold text-blue-800 mb-4"> Conversation</h3>

          <div id="comments" class="max-h-[400px] overflow-y-auto p-4 bg-gray-50 rounded-lg space-y-4">
    @foreach ($ticket->comments as $comment)
        @php
            $isSender = $comment->user_id === Auth::id();
        @endphp

        <div class="flex {{ $isSender ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-[70%] p-3 shadow-sm text-center
                {{ $isSender 
                    ? 'bg-blue-600 text-white rounded-xl' 
                    : 'bg-white text-gray-800 border border-gray-200 rounded-xl' }}">
                
                <p class="text-sm font-semibold mb-1 text-left">{{ $comment->user->name }}</p>
                <p class="text-sm text-center">{{ $comment->message }}</p>

                @if ($comment->attachment)
                    @php
                        $extension = pathinfo($comment->attachment, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($extension), ['jpg','jpeg','png','gif','webp']);
                    @endphp
                    <div class="mt-2">
                        @if ($isImage)
                            <a href="{{ asset('storage/' . $comment->attachment) }}" target="_blank">
                                <img src="{{ asset('storage/' . $comment->attachment) }}" 
                                    class="h-24 w-24 object-cover rounded-lg border border-gray-300 hover:scale-105 transition-transform mx-auto">
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $comment->attachment) }}" class="text-blue-300 underline text-sm" target="_blank">View Attachment</a>
                        @endif
                    </div>
                @endif

                <p class="text-xs mt-2 {{ $isSender ? 'text-blue-200' : 'text-gray-400' }}">{{ $comment->created_at->diffForHumans() }}</p>
            </div>
        </div>
    @endforeach
</div>


            {{-- Comment Input --}}
            <form action="{{ route('ticket_comments.store', $ticket->ticket_id) }}" method="POST" enctype="multipart/form-data" class="mt-6">
                @csrf
                <div class="flex items-center space-x-3">
                    <textarea name="message" class="border border-gray-300 rounded-xl w-full p-3 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Write your reply..." required></textarea>
                </div>

                <div class="flex items-center justify-between mt-3">
                    <input type="file" name="attachment" class="text-sm text-gray-600">
                    <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded-lg shadow transition-all">Send</button>
                </div>
            </form>
        </div>

        {{-- Ticket History --}}
        <div class="bg-white p-6 rounded-2xl shadow border border-gray-200">
           <h3 class="text-lg font-semibold mt-8 mb-3">Ticket Activity Log</h3>
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm border-collapse">
        <thead class="bg-blue-50 text-gray-700 uppercase">
            <tr>
                <th class="border px-4 py-2 text-left">Date</th>
                <th class="border px-4 py-2 text-left">User</th>
                <th class="border px-4 py-2 text-left">Action</th>
                <th class="border px-4 py-2 text-left">Old Value</th>
                <th class="border px-4 py-2 text-left">New Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ticket->histories as $history)
                <tr class="hover:bg-blue-50">
                    <td class="border px-4 py-2">{{ $history->created_at->format('Y-m-d H:i') }}</td>
                    <td class="border px-4 py-2">{{ $history->user->name ?? 'System' }}</td>
                    <td class="border px-4 py-2 font-medium text-blue-600">{{ ucfirst($history->action) }}</td>
                    <td class="border px-4 py-2 text-gray-600">{{ $history->old_value ?? '-' }}</td>
                    <td class="border px-4 py-2 text-gray-800 font-semibold">{{ $history->new_value ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-gray-500 py-4">No activity yet</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


        <a href="{{ route('tickets.index') }}" class="text-blue-700 underline font-medium hover:text-blue-900">← Back to Tickets</a>
    </div>

</x-app-layout>
