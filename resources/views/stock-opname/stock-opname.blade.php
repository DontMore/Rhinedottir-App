@extends('layout.main')

@section('container')

<!-- baris 1 -->
<div class="row">
    <!-- baris 1 kolom 1 -->
    <div class="col">
        <h1>Stock Opname</h1>
    </div> <!-- End baris 1 kolom 1 -->

    <!-- baris 1 kolom 2 -->
    <div class="col">
        <form action="{{ route('stock.index') }}" method="get" class="form-inline mb-3">
            @csrf
            <label for="month" class="mr-2">Month:</label>
            <select name="month" id="month" class="form-control mr-2">
                <!-- Option untuk bulan, sesuaikan dengan kebutuhan Anda -->
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>

            <label for="year" class="mr-2">Year:</label>
            <input type="number" name="year" id="year" min="1900" max="2099" class="form-control mr-2" placeholder="Enter year">

            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div> <!-- End baris 1 kolom 2 -->
</div> <!-- End baris 1 -->
<hr>

<!-- Baris 2 -->
<div class="row">
    <!-- Baris 2 kolom 1 -->
    <div class="col">
        <h2>Stock Reagen {{ \Carbon\Carbon::createFromDate($year, $month)->format('F Y') }}</h2>
    </div>

    <!-- Baris 2 kolom 2 -->
    <div class="col">
        <!-- Tombol untuk generate laporan PDF -->
        <form action="{{ route('stock.print') }}" method="get" class="form-inline mb-3">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="btn btn-success">Download Form Stock Opname</button>
        </form>
    </div><!-- End Baris 2 kolom 2 -->

    <!-- Baris 2 kolom 3 -->
    <div class="col">
           <?php
           $stockOpnameStatus = $stockOpname->status ?? 0;
           if($stockOpnameStatus == 0){
                echo '<div class="alert alert-warning" role="alert">
                    Stock Opname Not Complate
                </div>';
           }else{
              echo '<div class="alert alert-success" role="alert">
                Stock Opname Complate
              </div>';
           }
           ?>
    </div><!-- End Baris 2 kolom 3 -->

</div> <!-- End Baris 2 -->

<!-- baris 3 -->
<div class="row">
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>No. Catalog</th>
                <th>Name Reagen</th>
                <th>Merk</th>
                <th>Pack Size</th>
                <th>Previous month's stock quantity</th>
                <th>Quantity in</th>
                <th>Quantity out</th>
                <th>Current stock</th>
                <th>Status</th>
                <th>Stock Opname</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($reagens as $reagen)
            @php
                $quantityNow = $reagen->quantity;
                $quantityBefore = $reagen->quantity_before;
                $quantityIn = $reagen->quantity_in;
                $quantityOut = $reagen->quantity_out;
            @endphp

                <tr> 
                    <td>{{ $reagen->noCatalog }}</td>
                    <td>{{ $reagen->reagen->nameReagen }}</td>
                    <td>{{ $reagen->reagen->merk }}</td>
                    <td>{{ $reagen->reagen->packSize }}</td>
                    <td>{{ $quantityBefore }}</td>
                    <td>{{ $reagen->quantity_in }}</td>
                    <td>{{ $reagen->quantity_out }}</td>
                    <td><span id="quantity_now_{{ $reagen->id }}">{{ $reagen->stock_opname ? $reagen->quantity_actual : $quantityNow }}</span></td>
                    <td>{{ $reagen->status }}</td>
                    <td>{{ $reagen->stock_opname ? 'done' : 'not done' }}</td>
                    <td>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-warning btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#exampleModal" data-id="{{ $reagen->id }}">
                        Edit
                        </button>
                    </td>
                </tr>
        @endforeach
    </tbody>
    </table>
</div> <!-- End Baris 3 -->

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detail Reagen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body container">
        <!-- baris 1 modal -->
        <div class="row">
            <div class="col"><p>Catalog Number</div><div class="col"> : <span id="no-catalog"></span></p></div>
        </div><!-- baris 1 modal -->
        <!-- baris 2 modal -->
        <div class="row">
            <div class="col"><p>Reagen Name</div><div class="col"> : <span id="reagen-name"></span></p></div>
        </div><!-- baris 2 modal -->
        <!-- baris 3 modal -->
        <div class="row">
            <div class="col"><p>Current Stock</div><div class="col">: <span id="current-stock"></span></p></div>
        </div><!-- baris 3 modal -->
        <!-- baris 4 modal -->
        <div class="row">
            <div class="col"><p>Quantity In</div><div class="col">: <span id="quantity-in"></span></p></div>
        </div><!-- baris 4 modal -->
        <!-- baris 5 modal -->
        <div class="row">
           <div class="col"><p>Quantity Out</div><div class="col">: <span id="quantity-out"></span></p></div>
        </div><!-- baris 5 modal -->

            <form id="updateForm" method="POST" action="{{ route('stock.update') }}">
            <input type="hidden" id="stock-id" name="stock_id">
            <div class="row">
                    <div class="form-group col-md-6">
                        <label for="recipient-name" class="col-form-label">Quantity Actual</label>
                        <input type="text" class="form-control" id="quantity-actual">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="recipient-name" class="col-form-label">Status</label>
                        <input type="text" class="form-control" id="status">
                    </div>
            </div>
            <div class="form-group">
                <label for="message-text" class="col-form-label">Note</label>
                <textarea class="form-control" id="note"></textarea>
            </div>
            <input type="hidden" id="user" value="{{ auth()->user()->id }}">
            </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" id="btn-update-stock" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>

</body>

<script>
    $(document).ready(function() {
        // fungsi modal
        $('.edit-btn').click(function() {
            var reagenId = $(this).data('id');
            $.get("/get-reagen/" + reagenId, function(data) {
                $('#no-catalog').text(data.reagen.noCatalog);
                $('#reagen-name').text(data.reagen.nameReagen);
                $('#current-stock').text(data.stockHistory.stock_opname ? data.stockHistory.quantity_actual : data.stockHistory.quantity);
                $('#quantity-in').text(data.stockHistory.quantity_in);
                $('#quantity-out').text(data.stockHistory.quantity_out);
                $('#stock-id').val(data.stockHistory.id);
                $('#message-text').val(data.message);
            });
        });

        // ---------------------------------------------------------------------------------------
        // fungsi status stock opname
        $('#quantity-actual').on('input', function() {
        // Mendapatkan nilai quantity actual
        var quantityActual = parseFloat($(this).val());

        // Mendapatkan nilai current stock
        var currentStock = parseFloat($('#current-stock').text());

        // Menghitung hasil pengurangan current stock dengan quantity actual
        var stockDiff = currentStock - quantityActual;

        // Menampilkan hasil pengurangan di input status dan menambahkan kelas sesuai kondisi
        $('#status').val('');
        $('#status').removeClass('text-danger text-success');

        if (stockDiff === 0) {
            $('#status').val('Equal');
        } else if (stockDiff < 0) {
            $('#status').val('Deficient by ' + Math.abs(stockDiff));
            $('#status').addClass('text-danger');
        } else {
            $('#status').val('Excess by ' + stockDiff);
            $('#status').addClass('text-success');
        }
        });

        // -----------------------------------------------------------------------------------------------
        // update stock
        $('#exampleModal').on('click', '#btn-update-stock', function() {
            // Mendapatkan nilai stock-id dan data lainnya
            var stockId = $('#stock-id').val();
            var quantityActual = $('#quantity-actual').val();
            var status = $('#status').val();
            var note = $('#note').val();
            var user = $('#user').val();

            // Mengirim data ke controller menggunakan AJAX
            $.ajax({
                url: "{{ route('stock.update') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: stockId,
                    quantity_actual: quantityActual,
                    status: status,
                    catatan: note,
                    user_id: user
                },
                success: function(response) {
                    // Handle response jika diperlukan
                    console.log(response);
                    
                    // Reset formulir modal
                    $('#exampleModal').find('form')[0].reset();
                    // Tutup modal
                    $('#exampleModal').modal('hide');
                    Swal.fire({
                        title: "Success",
                        text: "Reagent stock successfully updated.",
                        icon: "success"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error: function(xhr) {
                    // Handle error jika diperlukan
                    console.log(xhr.responseText);
                }
            });
        });

    });
</script>


@endsection
