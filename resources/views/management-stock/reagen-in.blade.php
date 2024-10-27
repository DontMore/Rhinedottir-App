@extends('layout.main')

@section('container')
<div class="container mt-4">
    <h1 class="mb-4 text-center">Reagen Masuk</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Nomor Catalog</th>
                    <th>Nama Reagen</th>
                    <th>Batch</th>
                    <th>Quantity</th>
                    <th>Tanggal Kadaluarsa</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reagenIn as $reagen)
                    <tr>
                        <td>{{ $reagen->Id }}</td>
                        <td>{{ $reagen->created_at }}</td>
                        <td>{{ $reagen->noCatalog }}</td>
                        <td>{{ $reagen->reagen->nameReagen }}</td>
                        <td>{{ $reagen->batch }}</td>
                        <td>{{ $reagen->quantity }}</td>
                        <td>{{ $reagen->expiredDate }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Menampilkan link pagination -->
    <div class="d-flex justify-content-center mt-3">
        {{ $reagenIn->links() }}
    </div>
</div>
@endsection
