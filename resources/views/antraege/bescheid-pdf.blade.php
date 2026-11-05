<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11pt; color: #111; }
        h1 { font-size: 16pt; margin: 30px 0 20px; }
        .meta { color: #555; font-size: 9pt; }
        .text { white-space: pre-wrap; line-height: 1.5; }
        .answers { margin-top: 20px; border-top: 1px solid #ccc; padding-top: 10px; }
        .answers h2 { font-size: 13pt; margin-bottom: 8px; }
        .answers table { width: 100%; border-collapse: collapse; }
        .answers td { padding: 4px 6px; vertical-align: top; }
        .answers td:first-child { font-weight: bold; width: 40%; color: #333; }
        .impressum { margin-top: 40px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 8pt; color: #777; line-height: 1.4; }
    </style>
</head>
<body>
    <div class="meta">{{ $verein }} · Datum: {{ ($application->decided_at ?? now())->format('d.m.Y') }} · Antrag #{{ $application->id }}</div>
    <h1>{{ $notice['titel'] }}</h1>
    <div class="text">{{ $notice['text'] }}</div>

    @php
        $answers = $application->formattedAnswers();
    @endphp
    @if(!empty($answers))
    <div class="answers">
        <h2>Eingegebene Antragsdaten</h2>
        <table>
            @foreach($answers as $line)
                @php
                    $parts = explode(': ', $line, 2);
                @endphp
                <tr>
                    <td>{{ $parts[0] }}</td>
                    <td>{{ $parts[1] ?? '' }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    @endif

    @if($application->applicationType->reviewer_note)
    <div style="margin-top: 20px; border-top: 1px solid #ccc; padding-top: 10px;">
        <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #333;">Prüfaufgabe</h3>
        <p style="white-space: pre-wrap; font-size: 11pt; color: #555;">{{ $application->applicationType->reviewer_note }}</p>
    </div>
    @endif

    @php($reviewerAnswerLines = $application->formattedReviewerAnswers())
    @if(!empty($reviewerAnswerLines))
    <div style="margin-top: 20px;">
        <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #333;">Prüfer-Antworten</h3>
        <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
            @foreach($reviewerAnswerLines as $line)
                @php($parts = explode(': ', $line, 2))
                <tr>
                    <td style="padding: 4px 8px 4px 0; font-weight: bold; color: #555; width: 40%; vertical-align: top;">{{ $parts[0] }}:</td>
                    <td style="padding: 4px 0 4px 8px; color: #333;">{{ $parts[1] ?? '' }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    @endif

    @if(isset($deciderName) && $deciderName !== '')
    <div style="margin-top: 30px;">
        Mit freundlichen Grüßen<br>
        <strong>{{ $deciderName }}</strong><br>
        Ihr Team von {{ config('app.verein_name') }}
    </div>
    @endif

    <div class="impressum">
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
    </div>
</body>
</html>
