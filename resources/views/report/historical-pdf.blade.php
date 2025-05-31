<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Historical Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .filters { margin-bottom: 20px; }
        .footer { font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Historical Report</h2>
    </div>

    <div class="filters">
        <p>Period: {{ $start_date }} to {{ $end_date }}</p>
        <p>Reagen Filter: {{ $reagen_filter }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Reagen</th>
                <th>Quantity In</th>
                <th>Recorded By</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($histories as $history)
            <tr>
                <td>{{ Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') }}</td>
                <td>{{ $history->reagen->nameReagen }}</td>
                <td>{{ $history->quantity }}</td>
                <td>{{ $history->user_name }}</td>
                <td>{{ $history->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated: {{ $generated_at }}</p>
    </div>
</body>
</html>
