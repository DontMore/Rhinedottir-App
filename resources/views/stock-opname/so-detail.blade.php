@extends('layout.main')

@section('container')
<div class="container-fluid px-4 py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Stock Reagen {{ \Carbon\Carbon::createFromDate($year, $month)->format('F Y') }}</h4>
                <div class="d-flex gap-3 align-items-center">
                    <form action="{{ route('generatedSO') }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-arrow-clockwise"></i> Generate Stock
                        </button>
                    </form>
                    <div class="status-badge">
                        @if($stockOpname->status ?? 0)
                            <span class="badge bg-success px-3 py-2">Stock Opname Complete</span>
                        @else
                            <span class="badge bg-warning px-3 py-2">Stock Opname Not Complete</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">No. Catalog</th>
                            <th>Name Reagen</th>
                            <th>Merk</th>
                            <th>Pack Size</th>
                            <th class="text-center">Previous Stock</th>
                            <th class="text-center">In</th>
                            <th class="text-center">Out</th>
                            <th class="text-center">Current</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">SO Status</th>
                            <th class="text-center">Action</th>
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
                            <td class="text-center">{{ $reagen->noCatalog }}</td>
                            <td>{{ $reagen->reagen->nameReagen }}</td>
                            <td>{{ $reagen->reagen->merk }}</td>
                            <td>{{ $reagen->reagen->packSize }}</td>
                            <td class="text-center">{{ $quantityBefore }}</td>
                            <td class="text-center">{{ $reagen->quantity_in }}</td>
                            <td class="text-center">{{ $reagen->quantity_out }}</td>
                            <td class="text-center fw-bold">
                                @if ($reagen->stock_opname == 1)
                                    {{ $reagen->quantity_actual }}
                                @else
                                    <span id="quantity_now_{{ $reagen->id }}">{{ ($quantityIn + $quantityBefore) - $quantityOut }}</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $reagen->status }}</td>
                            <td class="text-center">
                                <span class="badge {{ $reagen->stock_opname ? 'bg-success' : 'bg-warning' }}">
                                    {{ $reagen->stock_opname ? 'Done' : 'Not Done' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-warning btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#exampleModal" data-id="{{ $reagen->id }}" data-bulan="{{ $month }}" data-tahun="{{ $year }}">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Reagen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="row g-3 mb-2">
                            <div class="col-md-4 text-muted">Catalog Number</div>
                            <div class="col-md-8 fw-bold" id="no-catalog"></div>
                        </div>
                        <div class="row g-3 mb-2">
                            <div class="col-md-4 text-muted">Reagen Name</div>
                            <div class="col-md-8" id="reagen-name"></div>
                        </div>
                        <div class="row g-3 mb-2">
                            <div class="col-md-4 text-muted">Current Stock</div>
                            <div class="col-md-8" id="current-stock"></div>
                        </div>
                        <div class="row g-3 mb-2">
                            <div class="col-md-4 text-muted">Quantity In</div>
                            <div class="col-md-8" id="quantity-in"></div>
                        </div>
                        <div class="row g-3 mb-2">
                            <div class="col-md-4 text-muted">Quantity Out</div>
                            <div class="col-md-8" id="quantity-out"></div>
                        </div>
                    </div>

                    <form id="updateForm">
                        <input type="hidden" id="stock-id" name="stock_id">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Quantity Actual</label>
                                <input type="text" class="form-control" id="quantity-actual">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <input type="text" class="form-control" id="status" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" id="note" rows="3"></textarea>
                        </div>
                        <input type="hidden" id="user" value="{{ auth()->user()->id }}">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="btn-update-stock" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

<script>
    $(document).ready(function() {
        // Fungsi modal
        $('.edit-btn').click(function() {
            // Ambil data dari tombol
            var reagenId = $(this).data('id');
            var bulan = $(this).data('bulan');
            var tahun = $(this).data('tahun');

            // Kirim permintaan GET dengan data bulan dan tahun sebagai parameter query
            $.get("get-reagen/" + reagenId, { bulan: bulan, tahun: tahun }, function(data) {
                // Isi data ke modal
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
