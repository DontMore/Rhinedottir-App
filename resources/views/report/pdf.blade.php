<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <!-- Tambahkan Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
    <!-- CSS -->
    <style>
        /* Menetapkan ukuran font untuk tabel menjadi lebih kecil */
        table {
            font-size: 8px; /* Atur font lebih kecil di sini */
        }
    </style>
<body>
    <div class="container-fluid mt-4">
        <h1 class="text-center">{{ $title }}</h1>
        <p class="mb-4 text-center">Month {{ $month }} {{ $year }}</p>
        <hr>

        <table class="table table-bordered">
            <thead class="text-center">
                <tr>
                    <th>No. Catalog</th>
                    <th>Name Reagen</th>
                    <th>Merk</th>
                    <th>Pack Size</th>
                    <th>Quantity</th>
                    <th>Quantity In</th>
                    <th>Quantity Out</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reagens as $reagen)
                    <tr>
                        <td>{{ $reagen->noCatalog }}</td>
                        <td>{{ $reagen->reagen->nameReagen }}</td>
                        <td class="text-center">{{ $reagen->reagen->merk }}</td>
                        <td class="text-center">{{ $reagen->reagen->packSize }}</td>
                        <td class="text-center">{{ $reagen->quantity }}</td>
                        <td class="text-center">{{ $reagen->quantity_in }}</td>
                        <td class="text-center">{{ $reagen->quantity_out }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Tambahkan Bootstrap JS (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
