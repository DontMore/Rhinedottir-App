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

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('management-stock.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-1">
                <input type="text" name="keyword" value="{{ request('keyword') }}" 
                    placeholder="🔍 Search reagent..." 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <!-- Group Filter -->
            <div>
                <select name="group_guid" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">All Groups</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->guid }}" {{ request('group_guid') == $group->guid ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <select name="category_guid" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->guid }}" {{ request('category_guid') == $category->guid ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- ✅ BARU: Status Filter -->
            <div>
                <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>✅ Active Only</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>❌ Inactive Only</option>
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>📋 All Status</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    Apply Filter
                </button>
                <a href="{{ route('management-stock.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Reagen List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catalog</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reagens as $reagen)
                    <tr class="hover:bg-gray-50 {{ !$reagen->is_active ? 'opacity-60' : '' }}">
                        <!-- Status Column -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $reagen->is_active 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-gray-100 text-gray-600' }}">
                                <span class="w-2 h-2 rounded-full mr-1.5 {{ $reagen->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $reagen->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $reagen->noCatalog }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $reagen->nameReagen }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $reagen->merk }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $reagen->stockReagen->quantity ?? 0 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                <!-- ✅ Toggle Status Button -->
                                <form action="{{ route('reagen.toggle-status', $reagen->guid) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="p-1.5 rounded-lg transition-colors
                                            {{ $reagen->is_active 
                                                ? 'text-orange-600 hover:bg-orange-50' 
                                                : 'text-green-600 hover:bg-green-50' }}"
                                            title="{{ $reagen->is_active ? 'Non-aktifkan' : 'Aktifkan' }}"
                                            onclick="return confirm('{{ $reagen->is_active ? 'Non-aktifkan' : 'Aktifkan' }} reagen ini?')">
                                        @if($reagen->is_active)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                    </button>
                                </form>

                                <!-- ✅ View Button -->
                                <a href="{{ route('data.view', $reagen->guid) }}" 
                                class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors"
                                title="View Details">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View
                                </a>

                                <!-- ✅ Edit Button -->
                                <a href="{{ route('data.edit', $reagen->guid) }}" 
                                class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-md transition-colors"
                                title="Edit Reagent">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>

                                <!-- ✅ Add Stock Button -->
                                <a href="{{ route('reagen.addstock', $reagen->guid) }}" 
                                class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-md transition-colors"
                                title="Add Stock">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Stock
                                </a>

                                <!-- ✅ Delete Button -->
                                <form action="{{ route('data.delete', $reagen->guid) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition-colors confirm-button"
                                            title="Delete Reagent">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                            No reagents found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($reagens->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $reagens->links() }}
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