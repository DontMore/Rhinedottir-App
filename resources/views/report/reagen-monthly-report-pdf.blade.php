<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reagen In/Out & Stock Actual per Bulan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; margin: 8px; }
        h2 { margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #e5e7eb; padding: 2px 1px; text-align: center; vertical-align: middle; word-wrap: break-word; }
        th { background: #f3f4f6; font-weight: 700; }
        td.left, th.left { text-align: left; }
        .in-cell { background: #8ed0a4 !important; }
        .out-cell { background: #ff9999 !important; }
    </style>
</head>
<body>
    <h2>Reagen In/Out & Stock Actual per Bulan</h2>
    <div>Year: {{ $year }}</div>
    <div style="margin-bottom: 12px;">Generated at: {{ $generated_at ?? $year }}</div>

    @php
        $bulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];
    @endphp

    <table>
        <colgroup>
            <col style="width:40px;">
            <col style="width:120px;">
            <col style="width:180px;">
            @for($m=1;$m<=12;$m++)
                <col style="width:35px;">
                <col style="width:35px;">
                <col style="width:45px;">
            @endfor
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2" style="width: 40px;">No</th>
                <th rowspan="2" style="width: 100px;">No. Catalog</th>
                <th rowspan="2">Reagen</th>
                @for($m=1;$m<=12;$m++)
                    <th colspan="3">{{ $bulan[$m] }}</th>
                @endfor
            </tr>
            <tr>
                @for($m=1;$m<=12;$m++)
                    <th style="background:#8ed0a4 !important;">In</th>
                    <th style="background:#ff9999 !important;">Out</th>
                    <th>Stock</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($data as $idx => $row)
                @php
                    $reagen = $row['reagen'];
                @endphp
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $reagen->noCatalog ?? '' }}</td>
                    <td class="left">{{ $reagen->nameReagen }}</td>

                    @for($m=1;$m<=12;$m++)
                        @php
                            $cell = $row['months'][$m] ?? ['in'=>0,'out'=>0,'stock'=>0];
                        @endphp
                        <td class="in-cell">{{ $cell['in'] }}</td>
                        <td class="out-cell">{{ $cell['out'] }}</td>
                        <td>{{ $cell['stock'] }}</td>
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
