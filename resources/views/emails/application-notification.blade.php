@component('mail::message')
# {{ $mailSubject }}

Guten Tag {{ $greetingName }},

@foreach($lines as $line)
{{ $line }}

@endforeach

Mit freundlichen Grüßen
@if($deciderName)
<br><strong>{{ $deciderName }}</strong>
@endif
Ihr Team von {{ config('app.verein_name') }}

<br>
Zu den Sportstätten 5<br>
45711 Datteln<br>
<br>
Geschäftsführender Vorstand:<br>
1. Vorsitzender Detlev Kuhnert<br>
2. Vorsitzender Andreas Schaaf<br>
Geschäftsführerin Tanja Kohlmann<br>
Kassiererin Katrin Fischer<br>
Reg.-Nr.: 627 beim Amtsgericht Recklinghausen<br>
Steuer-Nr.: 340 / 5743 / 0297
@endcomponent
