@php
    $appName = config('app.name');
@endphp

<x-mail::message>
{{--  Custom Header with Blue Universe Branding --}}
<div style="
    text-align: center; 
    margin-bottom: 30px; 
    background: linear-gradient(90deg, #1E3A8A 0%, #3B82F6 100%);
    padding: 25px 0; 
    border-radius: 10px 10px 0 0;
">
    <img src="{{ $message->embed($logo) }}" 
         alt="{{ $appName }} Logo" 
         style="
            display: block;
            max-width: 180px;
            width: 100%;
            height: auto;
            margin: 0 auto;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,0.3));
         ">
</div>

{{--  Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
# Hello!
@endif

{{--  Intro Lines --}}
@foreach ($introLines as $line)
<p style="font-size: 16px; color: #374151; line-height: 1.6;">{{ $line }}</p>
@endforeach

{{--  Action Button --}}
@isset($actionText)
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
    <tr>
        <td align="center">
            <a href="{{ $actionUrl }}"
               target="_blank"
               style="
                   background: linear-gradient(90deg, #1E3A8A 0%, #3B82F6 100%);
                   color: #ffffff;
                   padding: 14px 30px;
                   text-decoration: none;
                   font-size: 16px;
                   font-weight: 600;
                   border-radius: 8px;
                   display: inline-block;
                   font-family: 'Segoe UI', Arial, sans-serif;
                   box-shadow: 0 4px 10px rgba(30,58,138,0.3);
               ">
               {{ strtoupper($actionText) }}
            </a>
        </td>
    </tr>
</table>
@endisset

{{--  Outro Lines --}}
@foreach ($outroLines as $line)
<p style="font-size: 16px; color: #374151; line-height: 1.6;">{{ $line }}</p>
@endforeach

{{--  Signature --}}
@if (! empty($salutation))
{{ $salutation }}
@else
<p style="margin-top: 30px;">
    With warm regards,<br>
    <strong style="color: #1E3A8A;">{{ $appName }} Team</strong>
</p>
@endif

{{--  Footer --}}
<hr style="border:none; border-top:1px solid #e5e7eb; margin:30px 0;">
<p style="text-align:center; font-size: 13px; color: #6b7280;">
    &copy; {{ date('Y') }} <strong>{{ $appName }}</strong>. All rights reserved.<br>
    Need help? 
    <a href="mailto:info@uatech.co.tz" 
       style="color:#1E3A8A; text-decoration:none; font-weight:500;">
       Contact Support
    </a>
</p>
</x-mail::message>
