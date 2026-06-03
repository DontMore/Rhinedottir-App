@extends('layout.main')
@section('title', 'Management Stock')
@section('container')
<div class="space-y-6">
    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Reagent Inventory</h2>
            <p class="text-sm text-gray-500 mt-1">Manage stock, track movements, and organize reagents.</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2 flex-wrap sm:flex-nowrap">
            <a href="{{ url('add-reagen') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add New
            </a>

            <!-- + Group / + Category -->
            <button type="button" id="openGroupModal" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7H5" />
                </svg>
                + Group
            </button>

            <button type="button" id="openCategoryModal" class="inline-flex items-center justify-center px-4 py-2.5 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition-colors shadow-sm">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7H5" />
                </svg>
                + Category
            </button>

            <a href="reagen-in" class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Stock In
            </a>
            <a href="reagen-out" class="inline-flex items-center justify-center px-4 py-2.5 bg-amber-500 text-white text-sm font-semibold rounded-lg hover:bg-amber-600 transition-colors shadow-sm">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
                Stock Out
            </a>
        </div>
    </div>

    <!-- + Group / + Category Modals -->
    <div id="groupModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" data-close-modal-group></div>
        <div class="relative mx-auto mt-24 max-w-lg">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-800">Add Group</h3>
                    <button type="button" class="text-gray-500 hover:text-gray-700" data-close-modal-group>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('group.store') }}" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Group Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" required
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">

                        @if($errors->has('name'))
                            <p class="text-sm text-red-600 mt-1">
                                {{ $errors->first('name') }}
                            </p>
                        @endif
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" data-close-modal-group>
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                            Save Group
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="categoryModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" data-close-modal-category></div>
        <div class="relative mx-auto mt-24 max-w-lg">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-800">Add Category</h3>
                    <button type="button" class="text-gray-500 hover:text-gray-700" data-close-modal-category>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('category.store') }}" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Category Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" required
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">

                        @if($errors->has('name'))
                            <p class="text-sm text-red-600 mt-1">
                                {{ $errors->first('name') }}
                            </p>
                        @endif
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" data-close-modal-category>
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition-colors shadow-sm">
                            Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Filters & Search Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <form action="{{ route('management-stock.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                <!-- Search Input -->
                <div class="md:col-span-2 space-y-1">
                    <label for="keyword" class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Search Reagents</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="keyword" id="keyword" value="{{ request('keyword') }}"
                            placeholder="Search catalog no, reagent name, or brand..."
                            class="block w-full pl-10 pr-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all bg-gray-50 hover:bg-white focus:bg-white">
                    </div>
                </div>

                <!-- Group Filter -->
                <div class="space-y-1">
                    <label for="group_guid" class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Group</label>
                    <select name="group_guid" id="group_guid" class="block w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all bg-gray-50 hover:bg-white focus:bg-white">
                        <option value="">All Groups</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->guid }}" {{ request('group_guid') == $g->guid ? 'selected' : '' }}>
                                {{ $g->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div class="space-y-1">
                    <label for="category_guid" class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Category</label>
                    <select name="category_guid" id="category_guid" class="block w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all bg-gray-50 hover:bg-white focus:bg-white">
                        <option value="">All Categories</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->guid }}" {{ request('category_guid') == $c->guid ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Filter Controls -->
            <div class="flex items-center justify-between border-t border-gray-100 pt-4 mt-2">
                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Apply Filters
                    </button>
                    @if(request('keyword') || request('group_guid') || request('category_guid'))
                        <a href="{{ route('management-stock.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-all">
                            Clear All
                        </a>
                    @endif
                </div>

                @if(request('keyword') || request('group_guid') || request('category_guid'))
                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1.5 rounded-full">
                        Active Filters
                    </span>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Catalog No.</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reagent Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Group</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Brand</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($reagens as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->noCatalog }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item->nameReagen }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($item->group)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                    {{ $item->group->name }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">None</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($item->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-700/10">
                                    {{ $item->category->name }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">None</span>
                            @endif
                        </td>
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
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                            @if(request('keyword') || request('group_guid') || request('category_guid'))
                            No reagents found matching selected search or filters. Try clearing filters.
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

    // Open/Close Group & Category Modals
    const openGroupBtn = document.getElementById('openGroupModal');
    const openCategoryBtn = document.getElementById('openCategoryModal');

    const groupModal = document.getElementById('groupModal');
    const categoryModal = document.getElementById('categoryModal');

    function openModal(modalEl) {
        if (!modalEl) return;
        modalEl.classList.remove('hidden');
    }

    function closeModal(modalEl) {
        if (!modalEl) return;
        modalEl.classList.add('hidden');
    }

    if (openGroupBtn) {
        openGroupBtn.addEventListener('click', () => openModal(groupModal));
    }
    if (openCategoryBtn) {
        openCategoryBtn.addEventListener('click', () => openModal(categoryModal));
    }

    document.querySelectorAll('[data-close-modal-group]').forEach(el => {
        el.addEventListener('click', () => closeModal(groupModal));
    });

    document.querySelectorAll('[data-close-modal-category]').forEach(el => {
        el.addEventListener('click', () => closeModal(categoryModal));
    });
</script>
@endpush
@endsection