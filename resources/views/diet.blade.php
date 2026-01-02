<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Podgląd Diety</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

<div class="p-4 sm:p-6 bg-gray-50 min-h-screen">
    <div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg max-w-2xl mx-auto">
        <div class="mb-4 border-b pb-4">
            <h3 class="font-bold text-xl text-gray-800">{{ $diet['dietDescription'] ?? 'Dieta' }}</h3>
            <p class="text-sm text-gray-600">Data utworzenia: {{ isset($diet['created_at']) ? substr($diet['created_at'], 0, 10) : 'Brak danych' }}</p>
            @if(isset($diet['caloricValue']) && $diet['caloricValue'])
                <p class="text-sm text-gray-600">Kaloryczność: {{ $diet['caloricValue'] }}</p>
            @endif
            @if(isset($diet['trainerName']) && $diet['trainerName'])
                <p class="text-sm text-gray-600">Trener: {{ $diet['trainerName'] }}</p>
            @endif
            @if(isset($diet['menteeName']) && $diet['menteeName'])
                <p class="text-sm text-gray-600">Podopieczny: {{ $diet['menteeName'] }}</p>
            @endif
        </div>

        @php
            $days = [
                'monday' => 'Poniedziałek',
                'tuesday' => 'Wtorek',
                'wednesday' => 'Środa',
                'thursday' => 'Czwartek',
                'friday' => 'Piątek',
                'saturday' => 'Sobota',
                'sunday' => 'Niedziela'
            ];
        @endphp

        <div class="space-y-6 mt-6">
            @foreach($days as $day_key => $day_name)
                @php $day_diet = $diet[$day_key] ?? []; @endphp
                <div class="overflow-x-auto rounded-lg shadow-sm pt-6" style="page-break-inside: avoid;">
                    <table class="min-w-full text-sm">
                        <thead class="bg-yellow-400 text-black">
                            <tr>
                                <th colspan="2" class="px-4 py-3 text-lg font-bold tracking-wider">
                                    {{ $day_name }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="bg-white">
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 w-1/5">Śniadanie</th>
                                <td class="p-2 text-gray-700 whitespace-pre-wrap">{{ $day_diet['breakfast'] ?? '' }}</td>
                            </tr>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Drugie śniadanie</th>
                                <td class="p-2 text-gray-700 whitespace-pre-wrap">{{ $day_diet['secondBreakfast'] ?? '' }}</td>
                            </tr>
                            <tr class="bg-white">
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Obiad</th>
                                <td class="p-2 text-gray-700 whitespace-pre-wrap">{{ $day_diet['lunch'] ?? '' }}</td>
                            </tr>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Podwieczorek</th>
                                <td class="p-2 text-gray-700 whitespace-pre-wrap">{{ $day_diet['afternoonSnack'] ?? '' }}</td>
                            </tr>
                            <tr class="bg-white">
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Kolacja</th>
                                <td class="p-2 text-gray-700 whitespace-pre-wrap">{{ $day_diet['dinner'] ?? '' }}</td>
                            </tr>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Makro</th>
                                <td class="p-2 text-gray-700 whitespace-pre-wrap">{{ $day_diet['macro'] ?? '' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
</div>

</body>
</html>