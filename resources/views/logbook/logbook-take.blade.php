<!-- logbook-take.blade.php -->

@extends('layout.main')

@section('container')
    <div class="container-fluid">
        <!-- Error arlet -->
        <!-- Modal -->
        @if(session('errors') && session('errors')->has('quantity_taken'))
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Insufficient stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            Please recheck the stock and the quantity being taken.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
        </div>
        @endif

        <script>
            // Periksa jika pesan kesalahan 'quantity_taken' ada, tampilkan modal
            $(document).ready(function() {
                @if(session('errors') && session('errors')->has('quantity_taken'))
                    $('#exampleModalCenter').modal('show');
                @endif
            });
        </script>

        @can('admin')
        <!-- tombol take admin -->
        <div>
            <!-- Ubah tombol menjadi tautan dengan parameter $reagen->noCatalog -->
            <a href="{{ route('take-admin', ['noCatalog' => $reagen->noCatalog]) }}" class="btn btn-primary">Take Admin</a>
        </div>
        @endcan

        <div class="row">
            <h2>Logbook</h2>
        </div>
        <hr>

        <div class="row">
            <div class="col">Catalog Number</div>
            <div class="col">: {{ $reagen->noCatalog }}</div>
        </div>

        <div class="row">
            <div class="col">Reagent Name</div>
            <div class="col">: {{ $reagen->nameReagen }}</div>
        </div>

        <div class="row">
            <div class="col">Merk</div>
            <div class="col">: {{ $reagen->merk }}</div>
        </div>

        <div class="row">
            <div class="col">Stock</div>
            <div class="col">: {{ optional($reagen->stockReagen)->quantity ?? 'N/A' }}</div>
        </div>

        <hr>

        <div class="row mt-3">
            <div class="col-md-12">
                <form action="{{ route('take.process', ['noCatalog' => $reagen->noCatalog]) }}" method="post">
                    @csrf
                    <div>
                        <input type="hidden" value="{{ $reagen->noCatalog }}" name="noCatalog">
                    </div>

                    <div>
                        <input type="hidden" value="{{ auth()->user()->id }}" name="user_id" readonly>
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Batch Number</label>
                        <select class="form-control" id="batchSelect" name="batch">
                            @foreach ($reagen->reagenIn as $stock)
                                <option value="{{ $stock->batch }}">{{ $stock->batch }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Quantity Taken</label>
                        <input type="number" class="form-control" name="quantity_taken" id="exampleFormControlInput1">
                    </div>

                    <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">Notes</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" name="note" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>

    </div>


@endsection
