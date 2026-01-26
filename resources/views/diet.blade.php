<!DOCTYPE html>
<html lang="pl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Podgląd Diety</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .day-header {
            background-color: #fcd34d; /* yellow-400 */
            color: black;
            font-size: 16px;
            font-weight: bold;
        }
        .meal-type {
            background-color: #f9fafb; /* gray-50 */
            width: 25%;
            font-weight: bold;
            color: #374151;
        }
        .content {
            background-color: white;
            white-space: pre-wrap;
        }
        .row-alt .meal-type, .row-alt .content {
            background-color: #f9fafb;
        }
        .row-alt .meal-type {
             background-color: #f3f4f6;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h3>{{ $diet['dietDescription'] ?? 'Dieta' }}</h3>
        <p>Data utworzenia: {{ isset($diet['created_at']) ? substr($diet['created_at'], 0, 10) : 'Brak danych' }}</p>
        @if(isset($diet['caloricValue']) && $diet['caloricValue'])
            <p>Kaloryczność: {{ $diet['caloricValue'] }}</p>
        @endif
        @if(isset($diet['trainerName']) && $diet['trainerName'])
            <p>Trener: {{ $diet['trainerName'] }}</p>
        @endif
        @if(isset($diet['menteeName']) && $diet['menteeName'])
            <p>Podopieczny: {{ $diet['menteeName'] }}</p>
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

    @foreach($days as $day_key => $day_name)
        @php $day_diet = $diet[$day_key] ?? []; @endphp
        <table>
            <thead>
                <tr>
                    <th colspan="2" class="day-header">
                        {{ $day_name }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="meal-type">Śniadanie</td>
                    <td class="content">{{ $day_diet['breakfast'] ?? '' }}</td>
                </tr>
                <tr class="row-alt">
                    <td class="meal-type">Drugie śniadanie</td>
                    <td class="content">{{ $day_diet['secondBreakfast'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="meal-type">Obiad</td>
                    <td class="content">{{ $day_diet['lunch'] ?? '' }}</td>
                </tr>
                <tr class="row-alt">
                    <td class="meal-type">Podwieczorek</td>
                    <td class="content">{{ $day_diet['afternoonSnack'] ?? '' }}</td>
                </tr>
                <tr>
                    <td class="meal-type">Kolacja</td>
                    <td class="content">{{ $day_diet['dinner'] ?? '' }}</td>
                </tr>
                <tr class="row-alt">
                    <td class="meal-type">Makro</td>
                    <td class="content">{{ $day_diet['macro'] ?? '' }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach
</div>

</body>
</html>