@extends('layout.main')

@section('container')
<div class="container mt-4">
    <h1 class="mb-4 text-center">Reagen Keluar</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nomor Catalog</th>
                    <th>Nama Reagen</th>
                    <th>Batch</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                @foreach ($reagenOut as $reagen)
                    <tr>
                        <td><?= $no ?></td>
                        <td>{{ $reagen->created_at }}</td>
                        <td>{{ $reagen->noCatalog }}</td>
                        <td>{{ $reagen->reagen->nameReagen }}</td>
                        <td>{{ $reagen->batch }}</td>
                        <td>{{ $reagen->quantity_taken }}</td>
                    </tr>
                    <?php $no++ ?>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Menampilkan link pagination -->
    <div class="d-flex justify-content-center mt-3">
        {{ $reagenOut->links() }}
    </div>
</div>
@endsection
