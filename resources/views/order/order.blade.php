@extends('layout.main')
@section('container')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Order Management</h1>
    <a href="{{ route('order.new') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
        <i class="bi bi-plus-lg"></i> New Order
    </a>
</div>

<!-- Recommendations Section -->
@if($recommendations->count() > 0)
<div class="mb-8">
    <div class="flex items-center gap-2 mb-4">
        <div class="p-1.5 bg-amber-100 rounded-lg">
            <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h2 class="text-lg font-semibold text-gray-800">Recommended to Order</h2>
        <span class="px-2 py-0.5 text-xs font-bold bg-amber-100 text-amber-700 rounded-full">{{ $recommendations->count() }}</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($recommendations as $item)
        <div class="bg-white p-4 rounded-xl border border-amber-200 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-2 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="h-12 w-12 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                </svg>
            </div>
            <div class="flex flex-col h-full">
                <div class="mb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-2 py-0.5 rounded">{{ $item->noCatalog }}</span>
                    <h3 class="font-bold text-gray-900 mt-1 truncate">{{ $item->nameReagen }}</h3>
                    <p class="text-xs text-gray-500">{{ $item->merk }} | {{ $item->packSize }}</p>
                </div>
                
                <div class="flex items-end justify-between mt-auto">
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Current Stock</p>
                        <p class="text-lg font-black text-red-600">{{ $item->stockReagen->quantity ?? 0 }} <span class="text-xs font-normal text-gray-400">/ limit {{ $item->buffer_stock }}</span></p>
                    </div>
                    <a href="{{ route('order.new') }}?catalog={{ $item->noCatalog }}" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition-colors">
                        Order Now
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 border-b border-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">ID</th>
                    <th class="px-4 py-3 text-left font-medium">Catalog</th>
                    <th class="px-4 py-3 text-left font-medium">Name</th>
                    <th class="px-4 py-3 text-left font-medium">Merk</th>
                    <th class="px-4 py-3 text-center font-medium">Pack</th>
                    <th class="px-4 py-3 text-center font-medium">Qty</th>
                    <th class="px-4 py-3 text-left font-medium">User</th>
                    <th class="px-4 py-3 text-center font-medium">Status</th>
                    <th class="px-4 py-3 text-center font-medium">Date</th>
                    <th class="px-4 py-3 text-center font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($orders as $order)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-gray-600">{{ $order->id }}</td>
                    <td class="px-4 py-3 font-mono text-gray-800">{{ $order->noCatalog }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $order->nameReagen }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $order->merk }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $order->packSize }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full">{{ $order->quantity }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $order->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $order->status == 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                            {{ $order->status == 0 ? 'Pending' : 'Done' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-500">{{ $order->created_at->format('d/m/y') }}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('order.view', $order->guid) }}" class="px-1.5 py-0.5 text-[10px] font-medium rounded text-white bg-blue-600 hover:bg-blue-700">View</a>
                            <form action="{{ route('order.delete', $order->guid) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn px-2 py-1 text-xs font-medium text-red-600 bg-red-50 rounded hover:bg-red-100 transition">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-8 text-center text-gray-400">📦 No orders found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (method_exists($orders, 'links'))
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $orders->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.delete-btn').on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
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
</script>
@endpush
@endsection