@extends('layout.main')

@section('container')
<div class="container-fluid">
    <h1 class="mb-4 text-center">Reagen Masuk</h1>
    <div class="table-responsive">
                <div class="row">
                    <div class="col">Tanggal</div>
                    <div class="col">Nomor Catalog</div>
                    <div class="col">Nama Reagen</div>
                    <div class="col">Batch</div>
                    <div class="col">Quantity</div>
                    <div class="col">Tanggal Kadaluarsa</div>
                </div>
            <div class="col">
                <!-- Loop melalui grup tanggal -->
                @foreach ($reagenIn as $date => $items)
                    <!-- Baris untuk header tanggal -->
                    <div class="row">
                        <div class="col">
                            <h3>{{ $date }}</h3>
                        </div>
                    </div>
                    <!-- Data untuk setiap grup -->
                    @foreach ($items as $reagen)
                        <div class="row">
                            <div class="col">{{ $reagen->created_at->format('Y-m-d') }}</div>
                            <div class="col">{{ $reagen->noCatalog }}</div>
                            <div class="col">{{ $reagen->reagen->nameReagen }}</div>
                            <div class="col">{{ $reagen->batch }}</div>
                            <div class="col">{{ $reagen->quantity }}</div>
                            <div class="col">{{ $reagen->expiredDate }}</div>
                        </div>
                    @endforeach
                @endforeach
    </div>
</div>
@endsection
