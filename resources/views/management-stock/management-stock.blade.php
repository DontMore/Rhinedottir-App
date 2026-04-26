@extends('layout.main')

@section('title', 'Management Stock')

@section('container')
<div class="space-y-6">
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Reagent Inventory</h2>
            <p class="text-sm text-gray-500 mt-1">Manage stock, track movements, and organize reagents.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="add-reagen" class="inline-flex items-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add New
            </a>
            <a href="reagen-in" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Stock In
            </a>
            <a href="reagen-out" class="inline-flex items-center px-4 py-2.5 bg-amber-500 text-white text-sm font-medium rounded-lg hover:bg-amber-600 transition-colors shadow-sm">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                Stock Out
            </a>
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
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="confirm-button inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition-colors">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                            No reagents found. Add your first item to get started.
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
                    <ul class="inline-flex -space-x-px rounded-md shadow-sm">
                        @if ($reagens->onFirstPage())
                            <li><span class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 rounded-l-md cursor-not-allowed">Previous</span></li>
                        @else
                            <li><a href="{{ $reagens->previousPageUrl() }}" class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-l-md">Previous</a></li>
                        @endif

                        @foreach ($reagens->links()->elements[0] as $page => $url)
                            @if ($page === '...')
                                <li><span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span></li>
                            @elseif ($page == $reagens->currentPage())
                                <li><span class="relative z-10 inline-flex items-center px-4 py-2 border border-blue-600 bg-blue-600 text-sm font-medium text-white">{{ $page }}</span></li>
                            @else
                                <li><a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">{{ $page }}</a></li>
                            @endif
                        @endforeach

                        @if ($reagens->hasMorePages())
                            <li><a href="{{ $reagens->nextPageUrl() }}" class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-r-md">Next</a></li>
                        @else
                            <li><span class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 rounded-r-md cursor-not-allowed">Next</span></li>
                        @endif
                    </ul>
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
                    cancel: { text: "Cancel", visible: true, className: "swal-button--cancel" },
                    confirm: { text: "Yes, delete it", className: "swal-button--danger" }
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