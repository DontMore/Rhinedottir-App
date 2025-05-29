@extends('layout.main')

@section('container')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Management Stock</h1>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <form action="{{ route('management-stock.index') }}" method="GET" class="w-full sm:w-72">
                <div class="relative">
                    <input type="text" 
                        name="keyword" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" 
                        placeholder="Search by Name or Brand">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap gap-3 mb-6">
              <a href="add-reagen" class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 transition">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add New
            </a>
        <a href="reagen-in" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-green-600 text-white hover:bg-green-700 transition">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Reagen In
        </a>
        <a href="reagen-out" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-yellow-500 text-white hover:bg-yellow-600 transition">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
            </svg>
            Reagen Out
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catalog Number</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reagent Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Brand</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($reagens as $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->noCatalog }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->nameReagen }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->merk }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ ($item->stockReagen && $item->stockReagen->quantity > 0) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $item->stockReagen ? $item->stockReagen->quantity : '0' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('data.view', ['noCatalog' => $item->noCatalog]) }}" class="px-3 py-1 rounded bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs font-medium transition">View</a>
                            <a href="{{ route('reagen.addstock', ['noCatalog' => $item->noCatalog]) }}" class="px-3 py-1 rounded bg-green-100 text-green-700 hover:bg-green-200 text-xs font-medium transition">Add</a>
                            <a href="{{ route('data.edit', ['noCatalog' => $item->noCatalog]) }}" class="px-3 py-1 rounded bg-yellow-100 text-yellow-700 hover:bg-yellow-200 text-xs font-medium transition">Edit</a>
                            <form action="{{ route('data.delete', ['noCatalog' => $item->noCatalog]) }}" method="POST" class="inline">
                                @csrf
                                <button type="button" class="confirm-button px-3 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200 text-xs font-medium transition">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6 px-4 py-3 flex items-center justify-center border-t border-gray-200 sm:px-6 bg-white rounded-b-lg">
        @if ($reagens->hasPages())
        <nav aria-label="Page navigation example">
            <ul class="inline-flex -space-x-px text-base h-10">
                {{-- Previous Page Link --}}
                @if ($reagens->onFirstPage())
                    <li>
                        <span class="flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-400 bg-gray-100 border border-e-0 border-gray-300 rounded-s-lg cursor-not-allowed">Previous</span>
                    </li>
                @else
                    <li>
                        <a href="{{ $reagens->previousPageUrl() }}" class="flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($reagens->links()->elements[0] as $page => $url)
                    @if ($page === '...')
                        <li>
                            <span class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300">...</span>
                        </li>
                    @elseif ($page == $reagens->currentPage())
                        <li>
                            <a aria-current="page" class="flex items-center justify-center px-4 h-10 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700">{{ $page }}</a>
                        </li>
                    @else
                        <li>
                            <a href="{{ $url }}" class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($reagens->hasMorePages())
                    <li>
                        <a href="{{ $reagens->nextPageUrl() }}" class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                    </li>
                @else
                    <li>
                        <span class="flex items-center justify-center px-4 h-10 leading-tight text-gray-400 bg-gray-100 border border-gray-300 rounded-e-lg cursor-not-allowed">Next</span>
                    </li>
                @endif
            </ul>
        </nav>
        @endif
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
<script type="text/javascript">
    $('.confirm-button').click(function(event) {
        var form =  $(this).closest("form");
        event.preventDefault();
        swal({
            title: `Are you sure you want to delete this?`,
            text: "It will gone forevert",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });
    });
</script>
@endsection
