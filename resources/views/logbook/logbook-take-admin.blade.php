@extends('layout.main')

@section('container')
<div class="container-fluid">
    <!-- Error alert -->
    <!-- Modal -->
    @if(session('errors') && session('errors')->has('quantity_taken'))
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
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
        $(document).ready(function () {
            @if(session('errors') && session('errors')->has('quantity_taken'))
                $('#exampleModalCenter').modal('show');
            @endif
        });
    </script>

    <div class="row mt-4">
        <div class="col">
            <h2>Logbook</h2>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="row mb-2">
                <div class="col-5">Catalog Number:</div>
                <div class="col">{{ $reagen->noCatalog }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-5">Reagent Name:</div>
                <div class="col">{{ $reagen->nameReagen }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-5">Merk:</div>
                <div class="col">{{ $reagen->merk }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-5">Stock:</div>
                <div class="col">{{ optional($reagen->stockReagen)->quantity ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <hr>

    <div class="row mt-3">
        <div class="col-md-12">
            <form action="/take-process" method="post">
                @csrf

                <div class="mb-3">
                    <label for="id" class="form-label">Date:</label>
                    <input type="date" class="form-control" id="id" name="date">
                </div>

                <input type="hidden" value="{{ $reagen->noCatalog }}" name="noCatalog">

                <input type="hidden" value="{{ auth()->user()->id }}" name="user_id">

                <div class="mb-3">
                    <label for="batchSelect" class="form-label">Batch Number:</label>
                    <select class="form-control" id="batchSelect" name="batch">
                        @foreach ($reagen->reagenIn as $stock)
                        <option value="{{ $stock->batch }}">{{ $stock->batch }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Quantity Taken:</label>
                    <input type="number" class="form-control" id="exampleFormControlInput1" name="quantity_taken">
                </div>

                <div class="mb-3">
                    <label for="analis" class="form-label">Analis:</label>
                    <input type="text" class="form-control" id="analis" name="analis">
                </div>

                <div class="mb-3">
                    <label for="exampleFormControlTextarea1" class="form-label">Notes:</label>
                    <textarea class="form-control" id="exampleFormControlTextarea1" name="note" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection
