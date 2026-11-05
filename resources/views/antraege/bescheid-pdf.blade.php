<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11pt; color: #111; }
        h1 { font-size: 16pt; margin: 30px 0 20px; }
        .meta { color: #555; font-size: 9pt; }
        .text { white-space: pre-wrap; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="meta">{{ $verein }} · Datum: {{ ($application->decided_at ?? now())->format('d.m.Y') }} · Antrag #{{ $application->id }}</div>
    <h1>{{ $notice['titel'] }}</h1>
    <div class="text">{{ $notice['text'] }}</div>
</body>
</html>
