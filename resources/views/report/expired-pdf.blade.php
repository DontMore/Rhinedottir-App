<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expired Reagen Report</title>
    <style>
        @page { size: landscape; }
        body { 
            font-family: Arial, sans-serif;
            margin: 2.5cm;
            font-size: 12px;
        }
        .header { 
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
        }
        .header h2 {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 8px;
        }
        .header p {
            color: #4b5563;
            font-size: 14px;
        }
        table { 
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #ffffff;
        }
        th, td { 
            border: 1px solid #e5e7eb;
            padding: 12px;
        }
        th { 
            background-color: #f3f4f6;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            color: #374151;
        }
        td {
            color: #1f2937;
        }
        .text-center { text-align: center; }
        .expired { color: #dc2626; }
        .near-expiry { color: #d97706; }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px;
            font-size: 10px;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Expired Reagen Report</h2>
        <p>Status: {{ ucfirst($status_filter) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Catalog</th>
                <th>Reagen Name</th>
                <th>Batch</th>
                <th>Expired Date</th>
                <th>Status</th>
                <th>Remaining</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reagents as $reagent)
            @php
                $expiredDate = \Carbon\Carbon::parse($reagent->expiredDate);
                $isExpired = $expiredDate->isPast();
                $remainingDays = now()->diffInDays($expiredDate, false);
            @endphp
            <tr>
                <td>{{ $reagent->noCatalog }}</td>
                <td>{{ $reagent->reagen->nameReagen }}</td>
                <td>{{ $reagent->batch }}</td>
                <td class="text-center">{{ $expiredDate->format('d/m/Y') }}</td>
                <td class="text-center {{ $isExpired ? 'expired' : 'near-expiry' }}">
                    {{ $isExpired ? 'Expired' : 'Near Expiry' }}
                </td>
                <td class="text-center {{ $isExpired ? 'expired' : 'near-expiry' }}">
                    {{ $remainingDays }} days
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ $generated_at }} | {{ config('app.name') }}</p>
    </div>
</body>
</html>
