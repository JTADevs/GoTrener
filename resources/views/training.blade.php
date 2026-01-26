<!DOCTYPE html>
<html lang="pl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Podgląd Treningu</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #1f2937;
            background-color: #f9fafb;
        }
        .container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        .header {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            color: #1f2937;
        }
        .section {
            margin-bottom: 20px;
        }
        .label {
            display: block;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .value {
            font-size: 14px;
            color: #1f2937;
        }
        .status {
            font-weight: bold;
        }
        .text-green-600 { color: #059669; }
        .text-blue-600 { color: #2563eb; }
        .text-red-600 { color: #dc2626; }
        
        .box {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            white-space: pre-wrap;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .user-info p {
            margin: 0;
        }
        .user-name {
            font-weight: bold;
            font-size: 16px;
        }
        .user-role {
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>{{ $training['title'] }}</h1>
    </div>

    <div class="section">
        <span class="label">Data i czas</span>
        <div class="value">
            {{ $training['date'] }} <br>
            {{ $training['startTime'] }} - {{ $training['endTime'] }}
        </div>
    </div>

    <div class="section">
        <span class="label">Status</span>
        @php
            $statusColor = match($training['status']) {
                'Ukończony' => 'text-green-600',
                'Planowany' => 'text-blue-600',
                'Anulowany' => 'text-red-600',
                default => ''
            };
        @endphp
        <div class="value status {{ $statusColor }}">
            {{ $training['status'] }}
        </div>
    </div>

    @if(!empty($training['description']))
    <div class="section">
        <span class="label">Opis</span>
        <div class="box">{{ $training['description'] }}</div>
    </div>
    @endif

    @if(!empty($training['plan']))
    <div class="section">
        <span class="label">Plan treningowy</span>
        <div class="box">{{ $training['plan'] }}</div>
    </div>
    @endif

    @if(isset($training['otherUser']))
    <div class="footer">
        <div class="user-info">
            <p class="user-name">{{ $training['otherUser']['name'] }}</p>
            <p class="user-role">{{ $training['otherUser']['roleLabel'] ?? 'Użytkownik' }}</p>
        </div>
    </div>
    @endif
</div>

</body>
</html>
