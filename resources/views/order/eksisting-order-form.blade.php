@extends('layout.main')

@section('container')

<!-- Baris 1 -->
<div class="row d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Eksisting Order Form</h1>
</div>

<!-- baris 2 -->
<div class="row">
    <a href="/new-order-form"><button class="btn btn-primary mr-2">New Order</button></a>
    <a href="/eksisting-order-form"><button class="btn btn-info">Eksisting Order</button></a>
</div><!-- baris 2 -->

<!-- baris 3 -->
<div class="row">
    <!-- baris 3 kolom 1 -->
    <div class="col">
        <form action="{{ route('orders.store') }}" method="POST">
            @csrf

            <!-- Nomor Katalog -->
            <div class="form-group">
                <label for="nomorKatalog">Nomor Katalog</label>
                <select class="form-control" id="nomorKatalog" name="noCatalog">
                    <option value="" selected>Pilih Reagen</option>
                    @foreach($reagens as $reagen)
                        <option value="{{ $reagen->noCatalog }}">{{ $reagen->noCatalog }} - {{ $reagen->nameReagen }}</option>
                    @endforeach
                </select>
            </div>

                <!-- Nama Reagen -->
                <div class="form-group">
                <label for="namaReagen">Nama Reagen</label>
                <input type="text" class="form-control" id="namaReagen" name="nameReagen" readonly>
            </div>

            <!-- Merk -->
            <div class="form-group">
                <label for="merk">Merk</label>
                <input type="text" class="form-control" id="merk" name="merk" readonly>
            </div>

            <div class="form-group">
                <label for="packSize">Pack Size:</label>
                <input type="text" class="form-control" id="packSize" name="packSize" readonly>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
            </div>

            <div class="form-group">
                <input type="hidden" class="form-control" id="status" name="status" value="0" readonly>
                <input type="hidden" class="form-control" id="userId" name="userId" value="{{ auth()->user()->id }}" readonly>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div><!-- baris 3 kolom 1 -->
    
</div><!-- baris 3 -->

        <!-- AJAX Script -->
        <script>
            $(document).ready(function(){
                $('#nomorKatalog').on('change', function(){
                    var noCatalogUtama = $(this).val();
                    if(noCatalogUtama) {
                        $.ajax({
                            url: '/reagen/'+noCatalogUtama,
                            type: "GET",
                            dataType: "json",
                            success:function(data) {
                                $('#namaReagen').val(data.nameReagen);
                                $('#merk').val(data.merk);
                                $('#packSize').val(data.packSize);
                            }
                        });
                    } else {
                        $('#namaReagen').val('');
                        $('#merk').val('');
                        $('#packSize').val('');
                    }
                });
            });
        </script>


@endsection
