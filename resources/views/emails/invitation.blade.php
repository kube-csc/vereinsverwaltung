@component('mail::message')
# Einladung zur {{ config('app.name') }}

Sie wurden eingeladen, sich bei der {{ config('app.name') }} zu registrieren.

@component('mail::button', ['url' => $url])
Jetzt registrieren
@endcomponent

Dieser Link ist nur für Ihre E-Mail-Adresse {{ $invitation->email }} gültig.

Danke,<br>
{{ config('app.name') }}
<hr>
@include('textimport.mailImpressum')
@endcomponent
