@extends('layout.main')
@section('title', 'Add New Reagent')

@section('container')
<div class="space-y-6">
    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Add New Reagent</h2>
            <p class="text-sm text-gray-500 mt-1">Fill in the details below to register a new reagent in the system.</p>
        </div>
        <a href="{{ route('management-stock.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m0 0h12" />
            </svg>
            Back
        </a>
    </div>

    <!-- Form Card -->
    <form action="add-reagen" method="POST" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @csrf

        @if ($errors->any())
        <div class="mx-6 mt-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <div class="p-6 space-y-6">
            <!-- Basic Info Grid -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Catalog Number <span class="text-red-500">*</span></label>
                    <input type="text" name="noCatalog" value="{{ old('noCatalog') }}" required
                        class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('noCatalog') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Reagent Name <span class="text-red-500">*</span></label>
                    <input type="text" name="nameReagen" value="{{ old('nameReagen') }}" required
                        class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('nameReagen') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Brand <span class="text-red-500">*</span></label>
                    <input type="text" name="merk" value="{{ old('merk') }}" required
                        class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('merk') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Pack Size <span class="text-red-500">*</span></label>
                    <input type="text" name="packSize" value="{{ old('packSize') }}" required
                        class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('packSize') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- ✅ Group & Category Dropdowns with Search -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <!-- Group Dropdown -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Reagen Group <span class="text-gray-400 font-normal">(Optional)</span>
                    </label>
                    <input type="text" placeholder="🔍 Search group..."
                        class="group-search block w-full rounded-lg border-gray-200 bg-gray-50 px-3 py-2 text-sm mb-1.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                        autocomplete="off">
                    <select name="group_guid" class="group-select block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                        <option value="">-- Select Group --</option>
                        @foreach($groups as $group)
                        <option value="{{ $group->guid }}" {{ old('group_guid') == $group->guid ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Dropdown -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Reagen Category <span class="text-gray-400 font-normal">(Optional)</span>
                    </label>
                    <input type="text" placeholder=" Search category..."
                        class="category-search block w-full rounded-lg border-gray-200 bg-gray-50 px-3 py-2 text-sm mb-1.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                        autocomplete="off">
                    <select name="category_guid" class="category-select block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->guid }}" {{ old('category_guid') == $category->guid ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Script untuk fitur Search pada Dropdown -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    function attachSearch(searchSelector, selectSelector) {
                        const searchInput = document.querySelector(searchSelector);
                        const select = document.querySelector(selectSelector);
                        if (!searchInput || !select) return;

                        searchInput.addEventListener('input', function() {
                            const filter = this.value.toLowerCase();
                            Array.from(select.options).forEach(option => {
                                // Skip option pertama (placeholder)
                                if (option.value === "") {
                                    option.style.display = '';
                                    return;
                                }
                                option.style.display = option.text.toLowerCase().includes(filter) ? '' : 'none';
                            });
                        });

                        // Reset filter saat select dibuka/berubah
                        select.addEventListener('change', () => {
                            searchInput.value = '';
                        });
                        select.addEventListener('blur', () => {
                            searchInput.value = '';
                        });
                    }

                    attachSearch('.group-search', '.group-select');
                    attachSearch('.category-search', '.category-select');
                });
            </script>

            <!-- Hazard Symbols -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Hazard Symbols</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @php
                    $hazards = [
                    ['id' => 1, 'name' => 'Toxic', 'image' => 'toxic.png'],
                    ['id' => 2, 'name' => 'Corrosive', 'image' => 'corrosive.png'],
                    ['id' => 3, 'name' => 'Explosive', 'image' => 'explosive.png'],
                    ['id' => 4, 'name' => 'Carcinogen', 'image' => 'carcinogen.png'],
                    ['id' => 5, 'name' => 'Environment', 'image' => 'Environmental-Hazard.png'],
                    ['id' => 6, 'name' => 'Flammable', 'image' => 'flammable.png'],
                    ['id' => 7, 'name' => 'Irritant', 'image' => 'irritant.png'],
                    ['id' => 8, 'name' => 'Oxidising', 'image' => 'oxidising.png'],
                    ];
                    @endphp
                    @foreach($hazards as $hazard)
                    <div class="relative">
                        <input type="checkbox" id="hazard{{ $hazard['id'] }}" name="hazardOptions[]" value="{{ $hazard['name'] }}"
                            {{ in_array($hazard['name'], old('hazardOptions', [])) ? 'checked' : '' }}
                            class="peer absolute opacity-0 w-full h-full cursor-pointer z-10">
                        <label for="hazard{{ $hazard['id'] }}"
                            class="flex flex-col items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer transition-all bg-white hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <img src="{{ asset('public/images/' . $hazard['image']) }}" alt="{{ $hazard['name'] }}"
                                class="w-12 h-12 object-contain mb-2 transition-transform peer-checked:scale-110">
                            <span class="text-xs font-medium text-gray-600 peer-checked:text-blue-700">{{ $hazard['name'] }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- MSDS, Price, & Buffer Stock -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">MSDS Link <span class="text-red-500">*</span></label>
                    <input type="url" name="msds" value="{{ old('msds') }}" required placeholder="https://..."
                        class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('msds') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Price <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-sm text-gray-500">Rp</span>
                        </div>
                        <input type="number" name="price" value="{{ old('price') }}" required placeholder="0"
                            class="block w-full pl-10 rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                        @error('price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Buffer Stock <span class="text-red-500">*</span></label>
                    <input type="number" name="buffer_stock" value="{{ old('buffer_stock', 0) }}" required placeholder="0"
                        class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('buffer_stock') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Form Footer -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
            <button type="reset" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Reset Form
            </button>
            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Reagent
            </button>
        </div>
    </form>
</div>
@endsection