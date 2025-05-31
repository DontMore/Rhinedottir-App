@extends('layout.main')

@section('container')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Stock Reagen {{ \Carbon\Carbon::createFromDate($year, $month)->format('F Y') }}</h1>
                <div class="flex items-center gap-4">
                    <form id="generateStockForm" action="{{ route('generatedSO') }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                onclick="return confirmGenerate(event)">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Generate Stock
                        </button>
                    </form>
                    <div>
                        @if($stockOpname->status ?? 0)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Stock Opname Complete</span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Stock Opname Not Complete</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hidden form for generate stock -->
            <form id="generateStockForm" action="{{ route('generatedSO') }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
            </form>

            <div class="overflow-hidden">
                <table class="w-full table-fixed">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="w-[8%] px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Catalog</th>
                            <th class="w-[20%] px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name Reagen</th>
                            <th class="w-[10%] px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Merk</th>
                            <th class="w-[10%] px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pack Size</th>
                            <th class="w-[8%] px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Previous</th>
                            <th class="w-[8%] px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">In</th>
                            <th class="w-[8%] px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Out</th>
                            <th class="w-[8%] px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Current</th>
                            <th class="w-[10%] px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="w-[7%] px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">SO</th>
                            <th class="w-[8%] px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
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
                            <td class="px-3 py-2 text-sm text-center text-gray-900">{{ $reagen->noCatalog }}</td>
                            <td class="px-3 py-2 text-sm text-gray-900 break-words">{{ $reagen->reagen->nameReagen }}</td>
                            <td class="px-3 py-2 text-sm text-gray-500">{{ $reagen->reagen->merk }}</td>
                            <td class="px-3 py-2 text-sm text-gray-500">{{ $reagen->reagen->packSize }}</td>
                            <td class="px-3 py-2 text-sm text-center text-gray-900">{{ $quantityBefore }}</td>
                            <td class="px-3 py-2 text-sm text-center text-gray-900">{{ $reagen->quantity_in }}</td>
                            <td class="px-3 py-2 text-sm text-center text-gray-900">{{ $reagen->quantity_out }}</td>
                            <td class="px-3 py-2 text-sm font-medium text-center text-gray-900">
                                @if ($reagen->stock_opname == 1)
                                    {{ $reagen->quantity_actual }}
                                @else
                                    <span id="quantity_now_{{ $reagen->id }}">{{ ($quantityIn + $quantityBefore) - $quantityOut }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-sm text-center text-gray-500">{{ $reagen->status }}</td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ $reagen->stock_opname ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $reagen->stock_opname ? 'Done' : 'Not Done' }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button type="button" 
                                    onclick="openModal({{ $reagen->id }}, {{ $month }}, {{ $year }})"
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Modal -->
            <div id="modal" class="fixed inset-0 z-50 hidden">
                <!-- Background backdrop -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <!-- Modal panel -->
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                            <div class="bg-white">
                                <!-- Modal header -->
                                <div class="flex items-center justify-between border-b px-4 py-3">
                                    <h3 class="text-lg font-medium text-gray-900">Detail Reagen</h3>
                                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeModal()">
                                        <span class="sr-only">Close</span>
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                <!-- Modal body -->
                                <div class="px-4 py-5">
                                    <div class="space-y-3">
                                        <div class="grid grid-cols-3 gap-4 text-sm">
                                            <div class="text-gray-500">Catalog Number</div>
                                            <div class="col-span-2 font-medium text-gray-900" id="no-catalog"></div>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4 text-sm">
                                            <div class="text-gray-500">Reagen Name</div>
                                            <div class="col-span-2 font-medium text-gray-900" id="reagen-name"></div>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4 text-sm">
                                            <div class="text-gray-500">Current Stock</div>
                                            <div class="col-span-2 font-medium text-gray-900" id="current-stock"></div>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4 text-sm">
                                            <div class="text-gray-500">Quantity In</div>
                                            <div class="col-span-2 font-medium text-gray-900" id="quantity-in"></div>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4 text-sm">
                                            <div class="text-gray-500">Quantity Out</div>
                                            <div class="col-span-2 font-medium text-gray-900" id="quantity-out"></div>
                                        </div>
                                    </div>

                                    <form id="updateForm" class="mt-6">
                                        <input type="hidden" id="stock-id" name="stock_id">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Actual</label>
                                                <input type="text" id="quantity-actual" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                                <input type="text" id="status" readonly class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm sm:text-sm">
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                                            <textarea id="note" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                        </div>
                                        <input type="hidden" id="user" value="{{ auth()->user()->id }}">
                                    </form>
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="button" 
                                    id="btn-update-stock" 
                                    class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">
                                    Save Changes
                                </button>
                                <button type="button" 
                                    onclick="closeModal()"
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

</body>

<script>
    function openModal(reagenId, bulan, tahun) {
        document.getElementById('modal').classList.remove('hidden');
        getReagenData(reagenId, bulan, tahun);
    }

    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
        document.getElementById('updateForm').reset();
    }

    // Close modal when clicking outside
    document.getElementById('modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Close modal on escape key press
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    function getReagenData(reagenId, bulan, tahun) {
        $.get("get-reagen/" + reagenId, { bulan: bulan, tahun: tahun }, function(data) {
            $('#no-catalog').text(data.reagen.noCatalog);
            $('#reagen-name').text(data.reagen.nameReagen);
            $('#current-stock').text(data.stockHistory.stock_opname ? data.stockHistory.quantity_actual : data.stockHistory.quantity);
            $('#quantity-in').text(data.stockHistory.quantity_in);
            $('#quantity-out').text(data.stockHistory.quantity_out);
            $('#stock-id').val(data.stockHistory.id);
            $('#message-text').val(data.message);
        });
    }

    $(document).ready(function() {
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
        $('#btn-update-stock').on('click', function() {
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
                    closeModal();
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
                    Swal.fire({
                        title: "Error",
                        text: "Failed to update stock data.",
                        icon: "error"
                    });
                    console.error(xhr.responseText);
                }
            });
        });

    });

    function confirmGenerate(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Generate Stock?',
            text: "This will regenerate stock data for non-completed items. Completed stock opname data will be preserved.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, generate!'
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.form.submit();
            }
        });
        return false;
    }
</script>


@endsection
