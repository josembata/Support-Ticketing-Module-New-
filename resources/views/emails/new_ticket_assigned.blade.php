@component('mail::message')
# New Ticket Assigned

A new ticket has been assigned to you.

**Title:** {{ $ticket->title }}  
**Description:** {{ $ticket->description }}  
**Priority:** {{ ucfirst($ticket->priority) }}

@component('mail::button', ['url' => url('/tickets/'.$ticket->id)])
View Ticket
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
