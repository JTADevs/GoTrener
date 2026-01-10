<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Podgląd Planu Treningowego</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .page-break {
            page-break-after: always;
        }
        body { font-family: DejaVu Sans, sans-serif; }
    </style>
</head>
<body class="bg-gray-50">

<div class="p-4 sm:p-6 bg-gray-50 min-h-screen">
    <div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg max-w-2xl mx-auto">
        <div class="mb-4 border-b pb-4">
            <h3 class="font-bold text-xl text-gray-800">{{ $plan['title'] ?? 'Plan Treningowy' }}</h3>
            <p class="text-sm text-gray-600">Data utworzenia: {{ isset($plan['created_at']) ? substr($plan['created_at'], 0, 10) : 'Brak danych' }}</p>
            @if(isset($plan['trainerName']) && $plan['trainerName'])
                <p class="text-sm text-gray-600">Trener: {{ $plan['trainerName'] }}</p>
            @endif
            @if(isset($plan['menteeName']) && $plan['menteeName'])
                <p class="text-sm text-gray-600">Podopieczny: {{ $plan['menteeName'] }}</p>
            @endif
            @if(isset($plan['description']) && $plan['description'])
                <p class="text-sm text-gray-600 mt-2"><strong>Opis:</strong> {{ $plan['description'] }}</p>
            @endif
        </div>

        <div class="space-y-6 mt-6">
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <pre class="font-mono text-sm whitespace-pre-wrap text-gray-800" style="font-family: inherit;">{{ $plan['plan'] ?? '' }}</pre>
            </div>
        </div>
    </div>
</div>

</body>
</html>
