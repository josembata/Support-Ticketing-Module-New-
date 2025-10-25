<x-app-layout>
    <x-slot name="header">
         Ticket: {{ $ticket->title }}

         <!-- error and success -->
@if (session('success'))
    <div class="bg-green-500 border border-green-400 text-white px-4 py-3 rounded relative mb-3" role="alert">
        <strong class="font-bold">Success!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="bg-red-600 border border-red-500 text-white px-4 py-3 rounded relative mb-3" role="alert">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="bg-green-500 border border-yellow-400 text-blue-600 px-4 py-3 rounded relative mb-3">
        <strong class="font-bold">Validation Errors:</strong>
        <ul class="mt-1 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
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
                <p><strong>Category:</strong> {{ $ticket->category->name ?? 'Uncategorized' }}</p>
                <p><strong>Status:</strong> <span class="text-blue-700 font-semibold">{{ ucfirst(str_replace('_',' ', $ticket->status)) }}</span></p>
                <p><strong>Priority:</strong> <span class="capitalize">{{ $ticket->priority }}</span></p>
            </div>

@php
    
    $waitMinutes = 3;
    $waitMillis = $waitMinutes * 60 * 1000;

    $resolvedAt = $ticket->resolved_at ? \Carbon\Carbon::parse($ticket->resolved_at) : null;

    $elapsedMs = $resolvedAt ? (now()->timestamp * 1000) - ($resolvedAt->timestamp * 1000) : null;
    $elapsedMs = $elapsedMs ?? 0;
@endphp

 <!-- AGENT ACTIONS  -->
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
         <!-- If resolved but within wait  show countdown -->
        @php
            $canClose = $resolvedAt && now()->diffInMinutes($resolvedAt) >= $waitMinutes;
        @endphp

        <span id="agent-close-container">
            @if($canClose)
                <a href="{{ route('tickets.close.form', $ticket->ticket_id) }}" class="bg-green-500 text-white px-3 py-1 rounded">
                    Close Ticket
                </a>
            @else
                <button id="agentCountdownBtn" class="bg-green-500 text-gray-700 px-3 py-1 rounded" disabled>
                    Close available in --:-- 
                </button>
            @endif
        </span>
    @endif
@endif

<!-- USER (CREATOR) ACTIONS  -->
@if(Auth::id() === $ticket->created_by && $ticket->status === 'resolved')
    <span id="user-reopen-container">
        @php
            $canReopenServer = false;
            if ($resolvedAt) {
                $minutesSinceResolved = now()->diffInMinutes($resolvedAt);
                $canReopenServer = $minutesSinceResolved < $waitMinutes;
            }
        @endphp

        @if($canReopenServer)
            {{-- Show form with active button (we'll update client-side) --}}
            <form id="reopenForm" action="{{ route('tickets.reopen', $ticket->ticket_id) }}" method="POST" class="inline">
                @csrf
                @method('PUT')
                <button id="userReopenBtn" type="submit" class="bg-green-600 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                    Reopen Ticket
                </button>
            </form>

            <span id="userCountdownText" class="ml-2 text-sm text-gray-600">You can reopen for --:--</span>
        @else
            <button class="bg-blue-600 text-white px-3 py-1 rounded" disabled>
                Reopen period expired
            </button>
        @endif
    </span>
@endif

 <!-- JS Countdown  -->
@if($ticket->status === 'resolved' && $resolvedAt)
<script>
document.addEventListener('DOMContentLoaded', function () {
    // configure waitMinutes 
    const waitMinutes = {{ $waitMinutes }};
    const waitMillis = waitMinutes * 60 * 1000;

    // resolvedAt from server 
    const resolvedAt = new Date("{{ $resolvedAt->toIso8601String() }}").getTime();
    const nowClient = new Date().getTime();
    // start elapse
    let remaining = waitMillis - (nowClient - resolvedAt);

    function formatMs(ms) {
        if (ms <= 0) return '00:00';
        const totalSeconds = Math.floor(ms / 1000);
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;
        return `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
    }

    function tick() {
        remaining = waitMillis - (new Date().getTime() - resolvedAt);

        //  update agentCountdownBtn  to Close button when done
        const agentBtn = document.getElementById('agentCountdownBtn');
        const agentContainer = document.getElementById('agent-close-container');

        if (agentBtn) {
            if (remaining > 0) {
                agentBtn.innerText = 'Close available in ' + formatMs(remaining);
            } else {
                // replace with real Close link
                if (agentContainer) {
                    agentContainer.innerHTML = `<a href="{{ route('tickets.close.form', $ticket->ticket_id) }}" class="bg-green-600 text-white px-3 py-1 rounded">Close Ticket</a>`;
                }
            }
        }

        //  update user countdown text and keep reopen active until remaining <= 0
        const userCountdownText = document.getElementById('userCountdownText');
        const userReopenBtn = document.getElementById('userReopenBtn');
        const reopenForm = document.getElementById('reopenForm');
        const userContainer = document.getElementById('user-reopen-container');

        if (userCountdownText && userReopenBtn) {
            if (remaining > 0) {
                userCountdownText.innerText = 'You can reopen for ' + formatMs(remaining);
                userReopenBtn.disabled = false;
            } else {
                // period expired: remove reopen button  show expired label
                if (userContainer) {
                    userContainer.innerHTML = `<button class="bg-gray-300 text-gray-600 px-3 py-1 rounded" disabled>Reopen period expired</button>`;
                }
            }
        }

        // continue ticking if time remains, otherwise stop
        if (remaining > 0) {
            setTimeout(tick, 1000);
        }
    }

    // start ticking
    tick();
});
</script>
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
