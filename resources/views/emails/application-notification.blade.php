@component('mail::message')
# {{ $mailSubject }}

Guten Tag {{ $greetingName }},

@foreach($lines as $line)
{{ $line }}

@endforeach
Danke,<br>
Ihr Team von {{ config('app.verein_name') }}
@endcomponent
