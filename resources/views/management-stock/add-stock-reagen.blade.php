@extends('layout.main')

@section('container')
<div class="container">
        <h2>Add Stock Reagen</h2>
        <form action="/add-stock-reagen" method="POST">
            @csrf
            <!-- Nomor Katalog -->
            <div class="form-group">
                <label for="noCatalog">Catalog Number</label>
                <input type="text" class="form-control" id="noCatalog" name="noCatalog" value="{{ $reagen->noCatalog }}" readonly>
            </div>

            <!-- Nama Reagen -->
            <div class="form-group">
                <label for="namaReagen">Nama Reagen</label>
                <input type="text" class="form-control" id="namaReagen" value="{{ $reagen->nameReagen }}" readonly>
            </div>

            <!-- Merk -->
            <div class="form-group">
                <label for="merk">Merk</label>
                <input type="text" class="form-control" id="merk" value="{{ $reagen->merk }}" readonly>
            </div>

             <!-- Nomor Batch -->
             <div class="form-group">
                <label for="merk">Nomor Batch</label>
                <input type="text" class="form-control" id="batch" name="batch">
            </div>

            <!-- Jumlah -->
            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" name="quantity" class="form-control" id="jumlah">
            </div>

            <!-- Tanggal Kadaluarsa -->
            <div class="form-group">
                <label for="tanggalKadaluarsa">Tanggal Kadaluarsa</label>
                <input type="date" name="expiredDate" class="form-control" id="tanggalKadaluarsa">
            </div>

            <!-- Catatan -->
            <div class="form-group">
                <label for="catatan">Catatan</label>
                <textarea class="form-control" name="note" id="catatan" rows="3"></textarea>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>

@endsection