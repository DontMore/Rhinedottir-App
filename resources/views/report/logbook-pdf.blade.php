<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .filters { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .footer { margin-top: 20px; font-size: 12px; color: #666; }
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
