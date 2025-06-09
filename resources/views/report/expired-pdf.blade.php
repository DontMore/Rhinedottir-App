<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expired Reagen Report</title>
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
        .header p {
            color: #4b5563;
            font-size: 9px;
            margin: 0;
        }
        table { 
            width: 100%;
            border-collapse: collapse;
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
        }
        .text-center { text-align: center; }
        .expired { color: #dc2626; }
        .near-expiry { color: #d97706; }
        .footer { 
            position: running(footer);
            font-size: 8px;
            text-align: center;
            padding: 5px;
            border-top: 0.5px solid #e5e7eb;
            color: #6b7280;
        }
        @page {
            @bottom-center {
                content: element(footer);
            }
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
