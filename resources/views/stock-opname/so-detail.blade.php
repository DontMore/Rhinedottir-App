@extends('layout.main')
@section('title', 'Stock Opname Detail')

@section('container')
<div class="space-y-6">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Stock Reagen {{ \Carbon\Carbon::createFromDate($year, $month)->format('F Y') }}</h2>
            <p class="text-sm text-gray-500 mt-1">Review and reconcile inventory data for this period.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium ring-1 ring-inset {{ ($stockOpname->status ?? 0) ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-amber-600/20' }}">
                <span class="w-1.5 h-1.5 rounded-full mr-2 {{ ($stockOpname->status ?? 0) ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                {{ ($stockOpname->status ?? 0) ? 'Opname Complete' : 'Opname Pending' }}
            </span>
            <form action="{{ route('generatedSO') }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <button type="submit" id="btn-generate" class="inline-flex items-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Generate Stock
                </button>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Catalog</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pack Size</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Prev</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">In</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Out</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Current</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">SO</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach ($reagens as $reagen)
                    @php
                        $quantityNow = $reagen->stock_opname ? $reagen->quantity_actual : (($reagen->quantity_in + $reagen->quantity_before) - $reagen->quantity_out);
                        $isSO = $reagen->stock_opname ?? false;
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $reagen->noCatalog }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $reagen->reagen->nameReagen ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reagen->reagen->merk ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reagen->reagen->packSize ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700">{{ $reagen->quantity_before }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-emerald-600 font-medium">{{ $reagen->quantity_in }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-red-600 font-medium">{{ $reagen->quantity_out }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-center text-gray-900" id="qty_now_{{ $reagen->id }}">{{ $quantityNow }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">{{ $reagen->status ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $isSO ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-amber-600/20' }}">
                                {{ $isSO ? 'Done' : 'Pending' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button type="button" class="edit-btn inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
                                    data-id="{{ $reagen->id }}"
                                    data-catalog="{{ $reagen->noCatalog }}"
                                    data-name="{{ $reagen->reagen->nameReagen ?? '-' }}"
                                    data-current="{{ $quantityNow }}"
                                    data-in="{{ $reagen->quantity_in }}"
                                    data-out="{{ $reagen->quantity_out }}">
                                <svg class="-ml-0.5 mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" data-close-modal></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Update Stock Opname</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500 transition-colors" data-close-modal>
                    <span class="sr-only">Close</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><p class="text-gray-500 text-xs">Catalog No.</p><p id="m-catalog" class="font-medium text-gray-900"></p></div>
                    <div><p class="text-gray-500 text-xs">Reagent</p><p id="m-name" class="font-medium text-gray-900"></p></div>
                </div>
                <div class="grid grid-cols-3 gap-4 pt-3 border-t border-gray-100">
                    <div class="text-center"><p class="text-gray-500 text-xs">In</p><p id="m-in" class="font-semibold text-emerald-600"></p></div>
                    <div class="text-center"><p class="text-gray-500 text-xs">Out</p><p id="m-out" class="font-semibold text-red-600"></p></div>
                    <div class="text-center"><p class="text-gray-500 text-xs">Current</p><p id="m-current" class="font-semibold text-gray-900"></p></div>
                </div>
                <form id="updateForm" class="space-y-4">
                    <input type="hidden" id="stock-id" name="stock_id">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Actual Quantity <span class="text-red-500">*</span></label>
                        <input type="number" id="quantity-actual" required min="0" class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Calculated Status</label>
                        <input type="text" id="status" readonly class="block w-full rounded-lg border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Note</label>
                        <textarea id="note" rows="2" class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors resize-y"></textarea>
                    </div>
                    <input type="hidden" id="user-id" value="{{ auth()->id() }}">
                </form>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" data-close-modal>Cancel</button>
                <button type="button" id="btn-update-stock" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">Save Changes</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('editModal');
    const closeButtons = document.querySelectorAll('[data-close-modal]');
    const qtyInput = document.getElementById('quantity-actual');
    const statusInput = document.getElementById('status');
    let currentStockValue = 0;

    // Open Modal & Populate Data
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('m-catalog').textContent = this.dataset.catalog;
            document.getElementById('m-name').textContent = this.dataset.name;
            document.getElementById('m-in').textContent = this.dataset.in;
            document.getElementById('m-out').textContent = this.dataset.out;
            document.getElementById('m-current').textContent = this.dataset.current;
            document.getElementById('stock-id').value = this.dataset.id;
            qtyInput.value = '';
            statusInput.value = '';
            document.getElementById('note').value = '';
            currentStockValue = parseFloat(this.dataset.current) || 0;
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            qtyInput.focus();
        });
    });

    // Close Modal
    const closeModal = () => { modal.classList.add('hidden'); document.body.style.overflow = ''; };
    closeButtons.forEach(btn => btn.addEventListener('click', closeModal));

    // Auto-calculate Status
    qtyInput.addEventListener('input', function() {
        const actual = parseFloat(this.value) || 0;
        const diff = currentStockValue - actual;
        statusInput.value = '';
        statusInput.classList.remove('text-emerald-600', 'text-red-600');

        if (diff === 0) {
            statusInput.value = 'Equal';
            statusInput.classList.add('text-emerald-600');
        } else if (diff < 0) {
            statusInput.value = `Deficient by ${Math.abs(diff)}`;
            statusInput.classList.add('text-red-600');
        } else {
            statusInput.value = `Excess by ${diff}`;
            statusInput.classList.add('text-emerald-600');
        }
    });

    // Submit Update via Fetch API
    document.getElementById('btn-update-stock').addEventListener('click', function() {
        if (!qtyInput.value) {
            Swal.fire('Validation Error', 'Please enter actual quantity.', 'warning');
            return;
        }

        fetch("{{ route('stock.update') }}", {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': '{{ csrf_token() }}' 
            },
            body: JSON.stringify({
                id: document.getElementById('stock-id').value,
                quantity_actual: qtyInput.value,
                status: statusInput.value,
                catatan: document.getElementById('note').value,
                user_id: document.getElementById('user-id').value
            })
        })
        .then(res => res.json())
        .then(data => {
            closeModal();
            Swal.fire({ title: 'Success', text: 'Stock opname updated successfully.', icon: 'success' })
                .then(() => location.reload());
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Failed to update stock. Please try again.', 'error');
        });
    });

    // Generate Stock Confirmation
    document.getElementById('btn-generate').addEventListener('click', function(e) {
        e.preventDefault();
        const form = this.closest('form');
        Swal.fire({
            title: 'Generate Stock?',
            text: "This will regenerate stock data for non-completed items. Completed stock opname data will be preserved.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, generate!'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>
@endpush
@endsection