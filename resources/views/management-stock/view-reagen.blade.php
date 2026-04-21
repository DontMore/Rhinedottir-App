@extends('layout.main')

@section('container')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Reagen Details Card -->
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-xl font-semibold text-gray-900">Reagen Details</h1>
            <div class="flex space-x-2">
                <a href="{{ url()->previous() }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
                <a href="{{ route('data.edit', ['guid' => $data->guid]) }}" 
                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Details List -->
                <div class="space-y-1 divide-y divide-gray-200">
                    <div class="flex justify-between py-3">
                        <span class="text-gray-500">Catalog Number</span>
                        <span class="font-medium text-gray-900">{{ $data->noCatalog }}</span>
                    </div>
                    <div class="flex justify-between py-3">
                        <span class="text-gray-500">Reagen Name</span>
                        <span class="font-medium text-gray-900">{{ $data->nameReagen }}</span>
                    </div>
                    <div class="flex justify-between py-3">
                        <span class="text-gray-500">Brand</span>
                        <span class="font-medium text-gray-900">{{ $data->merk }}</span>
                    </div>
                    <div class="flex justify-between py-3">
                        <span class="text-gray-500">Pack Size</span>
                        <span class="font-medium text-gray-900">{{ $data->packSize }}</span>
                    </div>
                    <div class="flex justify-between py-3">
                        <span class="text-gray-500">MSDS</span>
                        <a href="{{ $data->msds }}" class="inline-flex items-center px-3 py-1 border border-blue-600 text-sm font-medium rounded-md text-blue-600 hover:bg-blue-50" target="_blank">
                            <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            View MSDS
                        </a>
                    </div>
                    <div class="flex justify-between py-3">
                        <span class="text-gray-500">Price</span>
                        <span class="font-medium text-gray-900">Rp {{ number_format((float)$data->price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Hazard Symbols -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Hazard Symbols</h2>
                    <div class="grid grid-cols-3 gap-4">
                        @foreach($hazardOptions as $hazard)
                            <div class="flex flex-col items-center">
                                @php
                                    $imagePath = match($hazard) {
                                        'Environment' => 'Environmental-Hazard',
                                        default => strtolower($hazard)
                                    };
                                @endphp
                                <img src="{{ asset('public/images/' . $imagePath . '.png') }}"
                                     alt="{{ $hazard }}"
                                     class="w-16 h-16 object-contain transition-transform duration-200 hover:scale-110">
                                <span class="mt-2 text-sm text-gray-600">{{ $hazard }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock History Table -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Stock History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expired Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($data->reagenIn as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->batch }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $item->quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ \Carbon\Carbon::parse($item->expiredDate)->isPast() 
                                    ? 'bg-red-100 text-red-800' 
                                    : 'bg-blue-100 text-blue-800' }}">
                                {{ \Carbon\Carbon::parse($item->expiredDate)->format('d M Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button type="button" 
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 mr-2"
                                    onclick="showModal(this)" 
                                    data-expired="{{ \Carbon\Carbon::parse($item->expiredDate)->format('d F Y') }}"
                                    data-id="{{ $item->Id }}">
                                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v-4m6 0h-2"/>
                                </svg>
                                Label
                            </button>
                            <button type="button" 
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                                    onclick="confirmDelete({{ $item->Id }})">
                                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>
                            <form id="delete-form-{{ $item->Id }}" 
                                  action="{{ route('management-stock.delete-stock', $item->Id) }}" 
                                  method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            
        </div>
    </div>
</div>

<!-- Modal (updated) -->
<div id="exampleModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        
        <div class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all max-w-4xl w-full">
            <!-- Modal Header -->
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Label Reagen</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500" data-dismiss="modal">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6" id="modalBody">
                <div class="flex flex-col md:flex-row border border-gray-200 rounded-lg bg-white">
                    <!-- QR Code Section -->
                    <div class="p-6 border-b md:border-b-0 md:border-r border-gray-200 flex items-center justify-center">
                        <div id="qrcode"></div>
                    </div>

                    <!-- Label Content -->
                    <div class="flex-1 flex flex-col">
                        <!-- Company Header -->
                        <div class="flex items-center p-4 border-b border-gray-200">
                            <div class="w-32 px-2">
                                <img src="{{ asset('images/logo_b7.png') }}" alt="Logo" class="w-full h-auto">
                            </div>
                            <div class="flex-1 text-center font-bold text-lg">
                                LABORATORIUM QC-ANDEV
                            </div>
                        </div>

                        <!-- Info Section -->
                        <div class="flex-1 p-6 space-y-4 border-b border-gray-200">
                            <div class="flex">
                                <span class="w-32 text-gray-600">Nama Reagen</span>
                                <span class="flex-1">: {{ $data->nameReagen }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 text-gray-600">Expired Date</span>
                                <span class="flex-1">: <span id="expired"></span></span>
                            </div>
                            <div class="flex">
                                <span class="w-32 text-gray-600">Tanggal Buka</span>
                                <span class="flex-1">: </span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="p-4 text-right text-sm text-gray-500">
                            Distribution List - Lampiran 1:WI-QO-QC-1018.02
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                <button type="button" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50" 
                    data-dismiss="modal">
                    Close
                </button>
                <button type="button" 
                    id="downloadBtn"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Label
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showModal(button) {
    const modal = document.getElementById('exampleModal');
    const expired = button.dataset.expired;
    const id = button.dataset.id;
    
    // Update modal content
    document.getElementById('expired').textContent = expired;
    const qrcodeDiv = document.getElementById('qrcode');
    qrcodeDiv.innerHTML = '';
    
    // Generate QR Code
    var typeNumber = 4;
    var errorCorrectionLevel = 'L';
    var qr = qrcode(typeNumber, errorCorrectionLevel);
    qr.addData("https://reagen.onexternal.com/qrcode/" + id);
    qr.make();
    qrcodeDiv.innerHTML = qr.createImgTag(6);
    
    // Show modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Use SweetAlert2 for delete confirmation
function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

// Close modal when clicking close button or outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('exampleModal');
    
    // Close button click
    document.querySelectorAll('[data-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', () => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        });
    });
    
    // Click outside modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    });
    
    // Download button functionality
    document.getElementById('downloadBtn').addEventListener('click', function() {
        const element = document.getElementById('modalBody');
        html2canvas(element, {
            scale: 2,
            backgroundColor: '#ffffff',
            logging: false
        }).then(canvas => {
            const image = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.download = 'reagen_label.png';
            link.href = image;
            link.click();
        });
    });
});
</script>
@endpush

@endsection
