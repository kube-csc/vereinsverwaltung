@component('mail::message')
# Einladung zur {{ config('app.verein_name') }}

Sie wurden eingeladen, sich bei der {{ config('app.verein_name') }} zu registrieren.

@component('mail::button', ['url' => $url])
Jetzt registrieren
@endcomponent

Dieser Link ist nur für Ihre E-Mail-Adresse {{ $invitation->email }} gültig.

Danke,<br>
{{ config('app.verein_name') }}
<hr>
@include('textimport.mailImpressum')
@endcomponent
