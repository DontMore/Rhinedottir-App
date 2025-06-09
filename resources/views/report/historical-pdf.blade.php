<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historical Report</title>
    <style>
        @page { 
            size: landscape;
            margin: 1.5cm;
        }
        body { 
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.2;
            margin: 0;
        }
        .header { 
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
        }
        .header h2 {
            font-size: 14px;
            color: #1f2937;
            margin: 0 0 5px 0;
        }
        table { 
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            page-break-inside: auto;
        }
        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
        th, td { 
            border: 0.5px solid #e5e7eb;
            padding: 4px 6px;
            font-size: 8px;
        }
        th { 
            background-color: #f3f4f6;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 8px;
        }
        .type-in { color: #059669; }
        .type-out { color: #dc2626; }
        .footer { 
            position: running(footer);
            font-size: 8px;
            text-align: center;
            padding: 5px;
            border-top: 0.5px solid #e5e7eb;
        }
        @page {
            @bottom-center {
                content: element(footer);
            }
        }
        .summary-section {
            margin-top: 15px;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .summary-section h3 {
            font-size: 11px;
            margin: 0 0 8px 0;
        }
        .summary-table {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Historical Report</h2>
        <p>Period: {{ $start_date }} - {{ $end_date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Reagen</th>
                <th>Quantity</th>
                <th>Recorded By</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($histories as $history)
            <tr>
                <td>{{ Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') }}</td>
                <td class="{{ $history->transaction_type === 'in' ? 'type-in' : 'type-out' }}">
                    {{ $history->transaction_type === 'in' ? 'In' : 'Out' }}
                </td>
                <td>{{ $history->reagen->nameReagen }}</td>
                <td class="{{ $history->transaction_type === 'in' ? 'type-in' : 'type-out' }}">
                    {{ $history->quantity }}
                </td>
                <td>{{ $history->user_name }}</td>
                <td>{{ $history->description ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-section">
        <h3 style="font-size: 11px; margin-bottom: 8px;">Overall Summary</h3>
        <table class="summary-table" style="width: 200px; margin-bottom: 10px;">
            <tr>
                <td style="font-weight: bold;">Total In:</td>
                <td class="type-in">{{ $histories->where('transaction_type', 'in')->sum(function($item) { return (int)$item->quantity; }) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Total Out:</td>
                <td class="type-out">{{ $histories->where('transaction_type', 'out')->sum(function($item) { return (int)$item->quantity; }) }}</td>
            </tr>
        </table>

        @php
            $perReagenSummary = $histories->groupBy('noCatalog')
                ->map(function ($group) {
                    return [
                        'name' => $group->first()->reagen->nameReagen,
                        'in' => $group->where('transaction_type', 'in')
                            ->sum(function($item) { return (int)$item->quantity; }),
                        'out' => $group->where('transaction_type', 'out')
                            ->sum(function($item) { return (int)$item->quantity; })
                    ];
                });
        @endphp

        <h3 style="font-size: 11px; margin-bottom: 8px;">Per Reagen Summary</h3>
        <table class="summary-table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Reagen Name</th>
                    <th>Total In</th>
                    <th>Total Out</th>
                </tr>
            </thead>
            <tbody>
                @foreach($perReagenSummary as $summary)
                <tr>
                    <td>{{ $summary['name'] }}</td>
                    <td class="type-in">{{ $summary['in'] }}</td>
                    <td class="type-out">{{ $summary['out'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y H:i:s') }} | {{ config('app.name') }}</p>
    </div>
</body>
</html>
