@extends('layout.main')

@section('container')
        
<!-- Baris 1 -->
<div class=" row d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">New Order Form</h1>
</div><!-- Baris 1 -->

<!-- Baris 2 -->
<div class="row mb-3">
    <!-- baris 2 kolom 1 -->
    <div class="mr-3">
        <a href="/new-order-form"><button class="btn btn-primary">New Order</button></a>
    </div><!-- baris 2 kolom 1 -->

    <!-- baris 2 kolom 2 -->
    <div class="">
        <a href="/eksisting-order-form"><button class="btn btn-info">Eksisting Order</button></a>
    </div><!-- baris 2 kolom 2 -->
</div><!-- Baris 2 -->

<!-- baris 3 -->
<div class="row">
    <!-- baris 3 kolom 1 -->
    <div class="col">
        <form action="{{ route('orders.store') }}" method="POST">
        @csrf

        <div class="form-group row">
            <label for="noCatalog">No Catalog:</label>
            <input type="text" class="form-control" id="noCatalog" name="noCatalog" required>
        </div>

        <div class="form-group row">
            <label for="nameReagen">Name Reagen:</label>
            <input type="text" class="form-control" id="nameReagen" name="nameReagen" required>
        </div>

        <div class="form-group row">
            <label for="merk">Merk:</label>
            <input type="text" class="form-control" id="merk" name="merk" required>
        </div>

        <div class="form-group row">
            <label for="packSize">Pack Size:</label>
            <input type="text" class="form-control" id="packSize" name="packSize" required>
        </div>

        <div class="form-group row">
            <label for="quantity">Quantity:</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>

        <div class="form-group">
            <input type="hidden" class="form-control" id="status" name="status" value="0" readonly>
            <input type="hidden" class="form-control" id="userId" name="userId" value="{{ auth()->user()->id }}" readonly>
        </div>

        <button type="submit" class="btn btn-primary row">Submit</button>
        </form>
    </div>    <!-- baris 3 kolom 1 -->
</div><!-- baris 3 -->



@endsection
