@component('mail::message')
# Einladung von {{ config('app.verein_name') }}

Sie wurden eingeladen, sich bei {{ config('app.verein_name') }} zu registrieren.

@component('mail::button', ['url' => $url])
Jetzt registrieren
@endcomponent

Dieser Link ist nur für Ihre E-Mail-Adresse {{ $invitation->email }} gültig.

Danke,<br>
Ihr Team von {{ config('app.verein_name') }}
<hr>
@include('textimport.mailImpressum')
@endcomponent
