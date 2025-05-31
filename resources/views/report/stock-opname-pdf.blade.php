<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Opname Report</title>
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
        .text-center { 
            text-align: center;
        }
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
        .success {
            color: #059669;
        }
        .danger {
            color: #dc2626;
        }
        .warning {
            color: #d97706;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Stock Opname Report</h2>
        <p>Period: {{ $month }} {{ $year }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Catalog</th>
                <th>Reagen Name</th>
                <th class="text-center">Previous</th>
                <th class="text-center">In</th>
                <th class="text-center">Out</th>
                <th class="text-center">Current</th>
                <th class="text-center">Actual</th>
                <th>Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stocks as $stock)
            @php
                $expectedStock = ($stock->quantity + $stock->quantity_in) - $stock->quantity_out;
                $difference = $stock->quantity_actual - $expectedStock;
                $status = $difference === 0 ? 'Equal' : ($difference > 0 ? 'Excess' : 'Deficient');
            @endphp
            <tr>
                <td>{{ $stock->noCatalog }}</td>
                <td>{{ $stock->reagen->nameReagen }}</td>
                <td class="text-center">{{ $stock->quantity }}</td>
                <td class="text-center">{{ $stock->quantity_in }}</td>
                <td class="text-center">{{ $stock->quantity_out }}</td>
                <td class="text-center">{{ $expectedStock }}</td>
                <td class="text-center">{{ $stock->quantity_actual }}</td>
                <td class="{{ $status === 'Equal' ? 'success' : ($status === 'Deficient' ? 'danger' : 'warning') }} text-center">
                    {{ $status }}
                </td>
                <td>{{ $stock->catatan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ $generated_at }} | {{ config('app.name') }}</p>
    </div>
</body>
</html>
