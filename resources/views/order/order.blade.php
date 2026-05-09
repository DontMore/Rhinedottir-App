@extends('layout.main')
@section('container')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Order Management</h1>
            <p class="text-sm text-gray-500 mt-1">Monitor stock levels, track recommendations, and manage purchase orders.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('order.new') }}" class="inline-flex items-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                New Order
            </a>
        </div>
    </div>

    <!-- ✅ RECOMMENDATIONS PANEL -->
    @if($recommendations->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-100 rounded-lg text-amber-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Recommended to Order</h2>
                    <p class="text-xs text-gray-500">Items with low stock or expiring batches.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-500 bg-white px-3 py-1.5 rounded-md border border-gray-200">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Updated: {{ now()->format('d M Y, H:i') }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Catalog No.</th>
                        <th class="px-6 py-3">Reagent Name</th>
                        <th class="px-6 py-3 text-center">Stock / Buffer</th>
                        <th class="px-6 py-3">Reason</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recommendations as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-mono font-medium text-gray-900">{{ $item->noCatalog }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $item->nameReagen }}</div>
                            <div class="text-xs text-gray-400">{{ $item->merk }} | {{ $item->packSize }}</div>
                        </td>
                        <!-- Menjadi ini (menggunakan null coalescing ?? agar aman) -->
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($item->stockReagen->quantity ?? 0) == 0 ? 'bg-red-50 text-red-700 ring-1 ring-red-600/20' : 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20' }}">
                                {{ $item->stockReagen->quantity ?? 0 }} / {{ $item->buffer ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                <svg class="w-3 h-3 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $item->recommendation_reason }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('order.eksisting') }}?catalog={{ $item->noCatalog }}"
                                class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                                Order Now
                                <i class="bi bi-arrow-right ml-1"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <!-- Empty State for Recommendations -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
        <div class="mx-auto w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mb-4">
            <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900">All Stock Levels are Healthy</h3>
        <p class="text-sm text-gray-500 mt-1">No items require reordering at this time.</p>
    </div>
    @endif

    <!-- ✅ ORDER HISTORY PANEL -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Order History</h3>
            <span class="text-xs text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">{{ $orders->count() }} Total</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">Catalog</th>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3 text-center">Qty</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-center">Date</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-mono text-gray-800">{{ $order->noCatalog }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $order->nameReagen }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-0.5 text-xs font-semibold bg-blue-50 text-blue-700 rounded-full ring-1 ring-blue-600/20">{{ $order->quantity }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $order->status == 0 ? 'bg-yellow-50 text-yellow-700 ring-1 ring-yellow-600/20' : 'bg-green-50 text-green-700 ring-1 ring-green-600/20' }}">
                                {{ $order->status == 0 ? 'Pending' : 'Done' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('order.view', $order->guid) }}" class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition">View</a>
                                <form action="{{ route('order.delete', $order->guid) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="delete-btn px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                            <svg class="mx-auto h-10 w-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            No orders found. Create your first order to get started.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                Swal.fire({
                    title: 'Delete Order?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    });
</script>
@endpush
@endsection