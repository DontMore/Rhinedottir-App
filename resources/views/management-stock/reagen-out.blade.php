@extends('layout.main')

@section('container')
<div class="" id="page-content">
    <div class="">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="mb-4">Reagen Keluar</h1>
                <div class="timeline p-4 block mb-4">
                    <div class="row">
                        <div class="col ps-5">Nama Reagen</div>
                        <div class="col">Nomor Catalog</div>
                        <div class="col">Batch</div>
                        <div class="col">Quantity</div>
                        <div class="col">Nama Analis</div>
                    </div>
                    @foreach ($paginatedData as $date => $items)
                    <div class="tl-item">
                        <div class="tl-dot b-danger"></div>
                        <div class="tl-content col-md-12">
                            <div class=""><h5>{{ $date }}</h5></div>
                            @foreach ($items as $reagen)
                            <a href="{{ route('data.history', ['noCatalog' => $reagen->noCatalog]) }}" class="text-decoration-none">
                                <div class="row text-muted">
                                    <div class="col">{{ $reagen->reagen->nameReagen }}</div>
                                    <div class="col">{{ $reagen->noCatalog }}</div>
                                    <div class="col">{{ $reagen->batch }}</div>
                                    <div class="col">{{ $reagen->quantity_taken }}</div>
                                    <div class="col">{{ $reagen->user->name }}</div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Menampilkan Pagination Links -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $paginatedData->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
