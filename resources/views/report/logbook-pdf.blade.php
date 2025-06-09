<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
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
        .header h1 {
            font-size: 14px;
            margin: 0 0 5px 0;
        }
        .filters { 
            margin-bottom: 10px;
            font-size: 8px;
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
        .footer { 
            position: running(footer);
            font-size: 8px;
            text-align: center;
            padding: 5px;
            border-top: 0.5px solid #e5e7eb;
            color: #666;
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
        <h1>{{ $title }}</h1>
    </div>

    <div class="filters">
        <p><strong>Period:</strong> {{ $start_date }} to {{ $end_date }}</p>
        <p><strong>Reagen Filter:</strong> {{ $reagen_filter }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Reagen</th>
                <th>Qty Taken</th>
                <th>User</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logbooks as $logbook)
            <tr>
                <td>{{ $logbook->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $logbook->reagen->nameReagen }}</td>
                <td>{{ $logbook->quantity_taken }}</td>
                <td>{{ $logbook->user->name }}</td>
                <td>{{ $logbook->notes }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on: {{ $generated_at }}</p>
    </div>
</body>
</html>
