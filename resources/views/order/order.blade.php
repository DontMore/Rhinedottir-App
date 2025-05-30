@extends('layout.main')

@section('container')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900">Order</h1>
        <a href="/new-order-form" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            New Order
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg">
        <div>
            <table class="w-full table-fixed">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-12 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="w-24 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catalog</th>
                        <th class="w-36 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="w-24 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">Merk</th>
                        <th class="w-16 px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pack</th>
                        <th class="w-12 px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="w-24 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="w-20 px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="w-20 px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="w-24 px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-2 py-2 text-xs text-center text-gray-900">{{ $order->id }}</td>
                        <td class="px-2 py-2 text-xs text-gray-900 break-words">{{ $order->noCatalog }}</td>
                        <td class="px-2 py-2 text-xs text-gray-900 break-words">{{ $order->nameReagen }}</td>
                        <td class="px-2 py-2 text-xs text-gray-500 break-words">{{ $order->merk }}</td>
                        <td class="px-2 py-2 text-xs text-gray-500 text-center">{{ $order->packSize }}</td>
                        <td class="px-2 py-2 text-xs text-gray-900 text-center">{{ $order->quantity }}</td>
                        <td class="px-2 py-2 text-xs text-gray-900 break-words">{{ $order->user->name }}</td>
                        <td class="px-2 py-2 text-center">
                            <span class="px-1.5 py-0.5 text-[10px] font-medium rounded-full {{ $order->status == 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                {{ $order->status == 0 ? 'Pending' : 'Done' }}
                            </span>
                        </td>
                        <td class="px-2 py-2 text-xs text-gray-500 text-center">{{ $order->created_at->format('d/m/y') }}</td>
                        <td class="px-2 py-2 text-center">
                            <div class="flex justify-center gap-1">
                                <a href="{{ route('order.view', ['id' => $order->id]) }}" 
                                   class="px-1.5 py-0.5 text-[10px] font-medium rounded text-white bg-blue-600 hover:bg-blue-700">View</a>
                                <form action="{{ route('order.delete', $order->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-1.5 py-0.5 text-[10px] font-medium rounded text-white bg-red-600 hover:bg-red-700 delete-btn">Del</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if (method_exists($orders, 'links'))
        <div class="px-6 py-4 border-t border-gray-200">
            <nav class="flex justify-center" aria-label="Page navigation">
                <ul class="inline-flex -space-x-px text-sm">
                    {{-- Previous Page Link --}}
                    @if ($orders->onFirstPage())
                        <li>
                            <span class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-400 bg-gray-100 border border-e-0 border-gray-300 rounded-s-lg cursor-not-allowed">Previous</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $orders->previousPageUrl() }}" class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                        @if ($page == $orders->currentPage())
                            <li>
                                <span aria-current="page" class="flex items-center justify-center px-3 h-8 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($orders->hasMorePages())
                        <li>
                            <a href="{{ $orders->nextPageUrl() }}" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                        </li>
                    @else
                        <li>
                            <span class="flex items-center justify-center px-3 h-8 leading-tight text-gray-400 bg-gray-100 border border-gray-300 rounded-e-lg cursor-not-allowed">Next</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
        @endif
    </div>
</div>

<script>
          $(document).ready(function () {
            $('.delete-btn').on('click', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
</script>
@endsection
