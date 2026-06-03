@extends('layout.main')
@section('title', 'Reagent Details')
@section('container')
<div class="space-y-6">
    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Reagent Details</h2>
            <p class="text-sm text-gray-500 mt-1">View specifications, hazard information, and stock movement history.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m0 0h12" />
                </svg>
                Back
            </a>
            <a href="{{ route('data.edit', ['guid' => $data->guid]) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Data
            </a>
        </div>
    </div>

    <!-- Details & Hazards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Information Card -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-800">General Information</h3>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Catalog Number</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">{{ $data->noCatalog }}</span>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Reagent Name</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">{{ $data->nameReagen }}</span>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Brand</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">{{ $data->merk }}</span>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Pack Size</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">{{ $data->packSize }}</span>
                </div>

                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Buffer Stock</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">
                        {{ isset($data->buffer_stock) ? number_format((float) $data->buffer_stock, 0, ',', '.') : '-' }}
                    </span>
                </div>

                <!-- ✅ TAMBAHAN: Group & Category -->
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Group</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">{{ $data->group->name ?? '-' }}</span>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Category</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">{{ $data->category->name ?? '-' }}</span>
                </div>

                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Price</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">Rp {{ number_format((float)$data->price, 0, ',', '.') }}</span>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2 items-center">
                    <span class="text-sm text-gray-500">MSDS Document</span>
                    <span class="sm:col-span-2">
                        <a href="{{ $data->msds }}" target="_blank" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors ring-1 ring-inset ring-blue-700/10">
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            View MSDS
                        </a>
                    </span>
                </div>
            </div>

            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <span class="text-sm text-gray-500">Storage Location</span>
                <span class="sm:col-span-2">
                    @if($data->storageLocation)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 ring-1 ring-green-600/20">
                        {{ $data->storageLocation->code }} - {{ $data->storageLocation->name }}
                    </span>
                    @else
                    <span class="text-sm text-gray-400 italic">Not assigned</span>
                    @endif
                </span>
            </div>

            @if($data->recommended_storage_location)
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <span class="text-sm text-gray-500">Recommended Storage</span>
                <span class="sm:col-span-2 text-sm text-blue-700 font-medium">
                    {{ $data->recommended_storage_location }}
                </span>
            </div>
            @endif
        </div>

        <!-- Hazard Symbols Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-800">Hazard Symbols</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach($hazardOptions as $hazard)
                    @php
                    $imagePath = match($hazard) {
                    'Environment' => 'Environmental-Hazard',
                    default => strtolower($hazard)
                    };
                    @endphp
                    <div class="flex flex-col items-center p-3 bg-gray-50 rounded-lg border border-gray-100 hover:shadow-sm transition-shadow">
                        <img src="{{ asset('public/images/' . $imagePath . '.png') }}"
                            alt="{{ $hazard }}"
                            class="w-14 h-14 object-contain"
                            onerror="this.style.display='none'">
                        <span class="mt-2 text-xs font-medium text-gray-600 text-center">{{ $hazard }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Stock History Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-base font-semibold text-gray-800">Stock History</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                Total: {{ $data->reagenIn->count() }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expired Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <!-- ✅ URUTAN DESCENDING -->
                    @forelse($data->reagenIn->sortByDesc('created_at') as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item->batch }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                {{ number_format($item->quantity, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $isExpired = \Carbon\Carbon::parse($item->expiredDate)->isPast();
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isExpired ? 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20' : 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20' }}">
                                {{ \Carbon\Carbon::parse($item->expiredDate)->format('d M Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors open-label-modal" data-id="{{ $item->Id }}" data-expired="{{ \Carbon\Carbon::parse($item->expiredDate)->format('d F Y') }}">
                                    <svg class="-ml-0.5 mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    Label
                                </button>
                                <form action="{{ route('management-stock.delete-stock', $item->Id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition-colors confirm-delete">
                                        <svg class="-ml-0.5 mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                            No stock history available for this reagent.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Label Modal -->
<div id="labelModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" data-close-modal></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Reagent Label</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500 transition-colors" data-close-modal>
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6" id="modalContent">
                <div class="flex flex-col md:flex-row border border-gray-200 rounded-lg bg-white overflow-hidden">
                    <div class="p-6 border-b md:border-b-0 md:border-r border-gray-200 flex items-center justify-center bg-gray-50">
                        <div id="qrcode"></div>
                    </div>
                    <div class="flex-1 flex flex-col">
                        <div class="flex items-center p-4 border-b border-gray-200 bg-gray-50/50">
                            <div class="w-24 px-2">
                                <img src="{{ asset('images/logo_b7.png') }}" alt="Logo" class="w-full h-auto object-contain">
                            </div>
                            <div class="flex-1 text-center font-bold text-lg text-gray-800">LABORATORIUM QC-ANDEV</div>
                        </div>
                        <div class="flex-1 p-6 space-y-4 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="w-full sm:w-32 text-sm text-gray-600">Nama Reagen</span>
                                <span class="text-sm font-medium text-gray-900 sm:flex-1">: {{ $data->nameReagen }}</span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="w-full sm:w-32 text-sm text-gray-600">Expired Date</span>
                                <span class="text-sm font-medium text-gray-900 sm:flex-1">: <span id="modalExpired"></span></span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="w-full sm:w-32 text-sm text-gray-600">Tanggal Buka</span>
                                <span class="text-sm font-medium text-gray-900 sm:flex-1">: ___________________</span>
                            </div>
                        </div>
                        <div class="p-4 text-right text-xs text-gray-500">
                            Distribution List - Lampiran 1:WI-QO-QC-1018.02
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" data-close-modal>Close</button>
                <button type="button" id="downloadLabelBtn" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Label
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('labelModal');
        const closeButtons = document.querySelectorAll('[data-close-modal]');
        const qrContainer = document.getElementById('qrcode');
        let qrCodeInstance = null;

        // Open Modal
        document.querySelectorAll('.open-label-modal').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('modalExpired').textContent = this.dataset.expired;
                const id = this.dataset.id;

                qrContainer.innerHTML = '';
                qrCodeInstance = new QRCode(qrContainer, {
                    text: `https://reagen.onexternal.com/qrcode/${id}`,
                    width: 128,
                    height: 128,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.L
                });

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        });

        // Close Modal
        const closeModal = () => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        };
        closeButtons.forEach(btn => btn.addEventListener('click', closeModal));

        // Download Label
        document.getElementById('downloadLabelBtn').addEventListener('click', function() {
            const element = document.getElementById('modalContent');
            html2canvas(element, {
                scale: 2,
                backgroundColor: '#ffffff',
                logging: false
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = `label_${Date.now()}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        });

        // Delete Confirmation (SweetAlert2)
        document.querySelectorAll('.confirm-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = this.closest('form');
                Swal.fire({
                    title: 'Delete Stock Record?',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    });
</script>
@endpush
@endsection