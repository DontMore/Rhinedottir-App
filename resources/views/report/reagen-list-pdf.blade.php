<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reagen List Report</title>
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
            page-break-inside: auto;
            background-color: #ffffff;
        }
        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
        th, td { 
            border: 0.5px solid #e5e7eb;
            padding: 4px 6px;
            font-size: 8px;
        }
        td { color: #1f2937; }
        td.muted { color: #6b7280; }
        th { 
            background-color: #f3f4f6;
            font-weight: 600;
            text-transform: uppercase;
            color: #4b5563;
        }
        .text-center { text-align: center; }
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
        <h2>Reagen List</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Catalog</th>
                <th>Name</th>
                <th>Merk</th>
                <th>Pack Size</th>
                <th class="text-center">Current Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reagens as $reagen)
            <tr>
                <td>{{ $reagen->noCatalog }}</td>
                <td>{{ $reagen->nameReagen }}</td>
                <td class="muted">{{ $reagen->merk }}</td>
                <td class="muted">{{ $reagen->packSize }}</td>
                <td class="text-center">{{ $reagen->total_quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y H:i:s') }} | {{ config('app.name') }}</p>
    </div>
</body>
</html>
