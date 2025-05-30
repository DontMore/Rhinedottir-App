@extends('layout.main')

@section('container')
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div class="flex items-center gap-4">
            <button class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Generate Stock
            </button>
            <h2 class="text-2xl font-bold text-gray-900">Stock Reagen {{ \Carbon\Carbon::createFromDate($year, $month)->format('F Y') }}</h2>
        </div>
        
        <div>
            @if($stockOpname->status ?? 0)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    Stock Opname Complete
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    Stock Opname Not Complete
                </span>
            @endif
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Catalog</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name Reagen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Merk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pack Size</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Previous</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">In</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Out</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Current</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">SO</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($reagens as $reagen)
                    @php
                        $quantityNow = $reagen->quantity;
                        $quantityBefore = $reagen->quantity_before;
                        $quantityIn = $reagen->quantity_in;
                        $quantityOut = $reagen->quantity_out;
                    @endphp

                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reagen->noCatalog }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reagen->reagen->nameReagen }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $reagen->reagen->merk }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $reagen->reagen->packSize }}</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-900">{{ $quantityBefore }}</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-900">{{ $reagen->quantity_in }}</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-900">{{ $reagen->quantity_out }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-center text-gray-900">
                                <span id="quantity_now_{{ $reagen->id }}">{{ $reagen->stock_opname ? $reagen->quantity_actual : $quantityNow }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-center text-gray-500">{{ $reagen->status }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ $reagen->stock_opname ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $reagen->stock_opname ? 'Done' : 'Not Done' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" 
                                    class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-white bg-amber-500 rounded hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 edit-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#exampleModal"
                                    data-id="{{ $reagen->id }}">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </button>
                            </td>
                        </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="exampleModal" class="modal fade fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">Detail Reagen</h3>
                            
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

                            <form id="updateForm" class="mt-4">
                                <input type="hidden" id="stock-id" name="stock_id">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Quantity Actual</label>
                                        <input type="text" id="quantity-actual" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <input type="text" id="status" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Note</label>
                                    <textarea id="note" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                </div>
                                <input type="hidden" id="user" value="{{ auth()->user()->id }}">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" id="btn-update-stock" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">Save changes</button>
                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" data-bs-dismiss="modal">Cancel</button>
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
