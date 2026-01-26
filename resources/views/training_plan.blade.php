<!DOCTYPE html>
<html lang="pl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Podgląd Planu Treningowego</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 14px;
        }
        .container {
            width: 100%;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #eee;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .header h3 {
            margin: 0 0 5px 0;
            font-size: 20px;
        }
        .header p {
            margin: 2px 0;
            color: #666;
            font-size: 12px;
        }
        .content-box {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        pre {
            font-family: 'DejaVu Sans', sans-serif;
            white-space: pre-wrap;
            margin: 0;
            color: #1f2937;
            font-size: 14px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h3>{{ $plan['title'] ?? 'Plan Treningowy' }}</h3>
        <p>Data utworzenia: {{ isset($plan['created_at']) ? substr($plan['created_at'], 0, 10) : 'Brak danych' }}</p>
        @if(isset($plan['trainerName']) && $plan['trainerName'])
            <p>Trener: {{ $plan['trainerName'] }}</p>
        @endif
        @if(isset($plan['menteeName']) && $plan['menteeName'])
            <p>Podopieczny: {{ $plan['menteeName'] }}</p>
        @endif
        @if(isset($plan['description']) && $plan['description'])
            <p style="margin-top: 10px;"><strong>Opis:</strong> {{ $plan['description'] }}</p>
        @endif
    </div>

    <div class="content-box">
        <pre>{{ $plan['plan'] ?? '' }}</pre>
    </div>
</div>

</body>
</html>
