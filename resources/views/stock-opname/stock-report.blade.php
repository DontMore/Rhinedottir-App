<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <!-- Add Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Add Custom CSS -->
    <style>
        /* Set font size for the table to 12px */
        table {
            font-size: 8px;
        }

        /* Add styling for different status classes */
        .kurang {
            background-color: #f8d7da; /* Bootstrap's danger background color */
        }

        .lebih {
            background-color: #d4edda; /* Bootstrap's success background color */
        }

        .sesuai {
            background-color: #cce5ff; /* Bootstrap's info background color */
        }
    </style>
</head>
<body>
    <div class="text-center">
        <h2>Stock Reagen {{ \Carbon\Carbon::parse("{$year}-{$month}-01")->format('F Y') }}</h2>
        <hr>
    </div>

    <!-- Row 3 -->
    <div class="row mt-3">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Catalog Number</th>
                    <th>Reagent Name</th>
                    <th>Brand</th>
                    <th>Pack Size</th>
                    <th>Previous Month Quantity</th>
                    <th>Total Quantity In</th>
                    <th>Total Quantity Taken</th>
                    <th>Current Month Quantity</th>
                    <th>Actual Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reagens as $reagen)
                    @php
                        $quantityNow = $reagen->quantity;
                        $quantityBefore = $reagen->quantity_before;
                        $quantityIn = $reagen->quantity_in;
                        $quantityOut = $reagen->quantity_out;
                    @endphp

                    <tr>
                        <td>{{ $reagen->noCatalog }}</td>
                        <td>{{ $reagen->reagen->nameReagen }}</td>
                        <td>{{ $reagen->reagen->merk }}</td>
                        <td>{{ $reagen->reagen->packSize }}</td>
                        <td>{{ $quantityBefore }}</td>
                        <td>{{ $reagen->quantity_in }}</td>
                        <td>{{ $reagen->quantity_out }}</td>
                        <td><span id="quantity_now_{{ $reagen->id }}">{{ $quantityNow }}</span></td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div> <!-- End Row 3 -->

    <!-- Add Bootstrap JS (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
