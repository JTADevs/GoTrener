<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Podgląd Treningu</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

<div class="p-4 sm:p-6 bg-gray-50 min-h-screen">
    <div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg max-w-2xl mx-auto">
        
        <div class="mb-6 border-b pb-4">
            <h1 class="text-2xl font-bold text-gray-800">{{ $training['title'] }}</h1>
        </div>

        <div class="space-y-6">
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <span class="block text-xs font-bold text-gray-500 uppercase mb-1">Data i czas</span>
                    <div class="text-gray-800">
                        {{ $training['date'] }} <br>
                        {{ $training['startTime'] }} - {{ $training['endTime'] }}
                    </div>
                </div>

                <div>
                    <span class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</span>
                    @php
                        $statusColor = match($training['status']) {
                            'Ukończony' => 'text-green-600',
                            'Planowany' => 'text-blue-600',
                            'Anulowany' => 'text-red-600',
                            default => ''
                        };
                    @endphp
                    <div class="font-bold {{ $statusColor }}">
                        {{ $training['status'] }}
                    </div>
                </div>

                @if(!empty($training['description']))
                <div>
                    <span class="block text-xs font-bold text-gray-500 uppercase mb-1">Opis</span>
                    <div class="bg-gray-50 p-4 rounded-lg text-gray-700 whitespace-pre-wrap">{{ $training['description'] }}</div>
                </div>
                @endif

                @if(!empty($training['plan']))
                <div>
                    <span class="block text-xs font-bold text-gray-500 uppercase mb-1">Plan treningowy</span>
                    <div class="bg-gray-50 p-4 rounded-lg text-gray-700 whitespace-pre-wrap">{{ $training['plan'] }}</div>
                </div>
                @endif
            </div>

            @if(isset($training['otherUser']))
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex items-center">
                    <div>
                        <p class="font-bold text-gray-800 text-lg">{{ $training['otherUser']['name'] }}</p>
                        <p class="text-sm text-gray-500">{{ $training['otherUser']['roleLabel'] ?? 'Użytkownik' }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

</body>
</html>
