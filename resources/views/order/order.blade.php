@extends('layout.main')
@section('container')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Order Management</h1>
    <a href="{{ route('order.new') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
        <i class="bi bi-plus-lg"></i> New Order
    </a>
</div>

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