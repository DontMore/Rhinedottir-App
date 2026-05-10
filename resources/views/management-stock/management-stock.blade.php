@extends('layout.main')
@section('title', 'Management Stock')
@section('container')
<div class="space-y-6">
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Reagent Inventory</h2>
            <p class="text-sm text-gray-500 mt-1">Manage stock, track movements, and organize reagents.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full lg:w-auto">
            <!-- ✅ SEARCH FORM -->
            <form action="{{ route('management-stock.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="keyword" value="{{ request('keyword') }}"
                        placeholder="Search catalog, name, or brand..."
                        class="block w-full pl-10 pr-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-colors">
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-gray-700 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors shadow-sm whitespace-nowrap">
                    Search
                </button>
                @if(request('keyword'))
                <a href="{{ route('management-stock.index') }}" class="inline-flex items-center justify-center px-3 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors whitespace-nowrap" title="Reset Search">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
                @endif
            </form>

            <!-- Action Buttons -->
            <div class="flex gap-2 flex-wrap sm:flex-nowrap w-full sm:w-auto">
                <a href="{{ url('add-reagen') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add New
                </a>
                <a href="reagen-in" class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Stock In
                </a>
                <a href="reagen-out" class="inline-flex items-center justify-center px-4 py-2.5 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600 transition-colors shadow-sm">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                    Stock Out
                </a>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catalog No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reagent Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($reagens as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->noCatalog }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item->nameReagen }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item->merk }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $qty = $item->stockReagen ? $item->stockReagen->quantity : 0;
                            $colorClass = $qty > 10 ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : ($qty > 0 ? 'bg-amber-50 text-amber-700 ring-amber-600/20' : 'bg-red-50 text-red-700 ring-red-600/20');
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $colorClass }}">
                                {{ $qty }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('data.view', ['guid' => $item->guid]) }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">View</a>
                                <a href="{{ route('reagen.addstock', ['guid' => $item->guid]) }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-md transition-colors">Add</a>
                                <a href="{{ route('data.edit', ['guid' => $item->guid]) }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-md transition-colors">Edit</a>
                                <form action="{{ route('data.delete', ['guid' => $item->guid]) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="confirm-button inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition-colors">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                            @if(request('keyword'))
                            No reagents found matching "<strong>{{ request('keyword') }}</strong>". Try a different search term.
                            @else
                            No reagents found. Add your first item to get started.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($reagens->hasPages())
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
            <nav aria-label="Pagination" class="flex items-center justify-between">
                <div class="hidden sm:block text-sm text-gray-500">
                    Showing <span class="font-medium">{{ $reagens->firstItem() }}</span> to <span class="font-medium">{{ $reagens->lastItem() }}</span> of <span class="font-medium">{{ $reagens->total() }}</span> results
                </div>
                <div class="flex-1 flex justify-end sm:justify-center">
                    <!-- ✅ appends(request()->query()) memastikan keyword search tetap ada saat ganti halaman -->
                    {{ $reagens->appends(request()->query())->links() }}
                </div>
                <div class="hidden sm:block w-1/3"></div>
            </nav>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.confirm-button').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const form = this.closest('form');
            swal({
                title: 'Delete Reagent?',
                text: "This action cannot be undone.",
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Cancel",
                        visible: true,
                        className: "swal-button--cancel"
                    },
                    confirm: {
                        text: "Yes, delete it",
                        className: "swal-button--danger"
                    }
                },
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection