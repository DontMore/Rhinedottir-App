@extends('layout.main')
@section('title', 'Add New Reagent')
@section('container')
<div class="space-y-6">
    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Add New Reagent</h2>
            <p class="text-sm text-gray-500 mt-1">Register a new reagent with specifications, storage, and hazards.</p>
        </div>
        <a href="{{ route('management-stock.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m0 0h12"/></svg>
            Back to List
        </a>
    </div>

    <!-- Form Card -->
    <!-- ✅ TAMBAHKAN enctype="multipart/form-data" UNTUK UPLOAD FILE -->
    <form action="{{ route('management-stock.add-reagen.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
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

        <!-- SECTION 1: BASIC INFO (Sama seperti sebelumnya) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
                <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-base font-semibold text-gray-800">Basic Information</h3>
            </div>
            <div class="p-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Catalog Number <span class="text-red-500">*</span></label>
                    <input type="text" name="noCatalog" value="{{ old('noCatalog') }}" required placeholder="e.g., CAT-12345" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Reagent Name <span class="text-red-500">*</span></label>
                    <input type="text" name="nameReagen" value="{{ old('nameReagen') }}" required placeholder="e.g., Acetone AR" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Brand / Merk <span class="text-red-500">*</span></label>
                    <input type="text" name="merk" value="{{ old('merk') }}" required placeholder="e.g., Merck" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Pack Size <span class="text-red-500">*</span></label>
                    <input type="text" name="packSize" value="{{ old('packSize') }}" required placeholder="e.g., 500ml" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
            </div>
        </div>

        <!-- SECTION 2: CLASSIFICATION (Sama seperti sebelumnya) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
                <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                <h3 class="text-base font-semibold text-gray-800">Classification</h3>
            </div>
            <div class="p-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Reagen Group</label>
                    <input type="text" placeholder="🔍 Search group..." class="group-search block w-full rounded-lg border-gray-200 bg-gray-50 px-3 py-2 text-sm mb-1.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    <select name="group_guid" class="group-select block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">-- Select Group --</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->guid }}" {{ old('group_guid') == $group->guid ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Reagen Category</label>
                    <input type="text" placeholder="🔍 Search category..." class="category-search block w-full rounded-lg border-gray-200 bg-gray-50 px-3 py-2 text-sm mb-1.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    <select name="category_guid" class="category-select block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->guid }}" {{ old('category_guid') == $category->guid ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- SECTION 3: STORAGE LOGIC & HAZARDS -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
                <svg class="h-5 w-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                <h3 class="text-base font-semibold text-gray-800">Storage Logic & Hazards</h3>
            </div>
            <div class="p-6">
                <!-- Physical Form & Signal Word -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Physical Form <span class="text-red-500">*</span></label>
                        <select name="reagent_form" id="reagentForm" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">-- Select Form --</option>
                            <option value="liquid" {{ old('reagent_form') == 'liquid' ? 'selected' : '' }}>Liquid (Cairan)</option>
                            <option value="solid" {{ old('reagent_form') == 'solid' ? 'selected' : '' }}>Solid (Padatan)</option>
                            <option value="crystal" {{ old('reagent_form') == 'crystal' ? 'selected' : '' }}>Crystal (Kristal)</option>
                        </select>
                    </div>
                    
                    <!-- ✅ TAMBAHAN: SIGNAL WORD DROPDOWN -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Signal Word (GHS)</label>
                        <select name="signal_word" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">-- Select Signal Word --</option>
                            <option value="Danger" {{ old('signal_word') == 'Danger' ? 'selected' : '' }}>Danger</option>
                            <option value="Warning" {{ old('signal_word') == 'Warning' ? 'selected' : '' }}>Warning</option>
                            <option value="None" {{ old('signal_word') == 'None' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                </div>

                <!-- Recommended Storage -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Recommended Storage</label>
                    <div id="recommendedStorageText" class="block w-full rounded-lg border border-blue-200 bg-blue-50 px-3 py-2.5 text-sm text-blue-800 min-h-[42px] flex items-center">
                        Select Form & Hazards to see recommendation...
                    </div>
                    <p class="mt-1 text-xs text-blue-600">Based on physical form + hazard classification</p>
                    <input type="hidden" name="recommended_storage_location" id="recommendedStorageInput">
                </div>

                <!-- Hazard Symbols -->
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
                                class="peer absolute opacity-0 w-full h-full cursor-pointer z-10" onchange="updateRecommendedStorage()">
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
        </div>

        <!-- SECTION 4: ACTUAL STORAGE & PRICING -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
                <svg class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <h3 class="text-base font-semibold text-gray-800">Details & Pricing</h3>
            </div>
            <div class="p-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Actual Storage Location</label>
                    <select name="storage_location_guid" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">-- Select Location --</option>
                        @foreach($storageLocations as $location)
                            <option value="{{ $location->guid }}" {{ old('storage_location_guid') == $location->guid ? 'selected' : '' }}>
                                {{ $location->code }} - {{ $location->name }} ({{ $location->state_type }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- ✅ DIUBAH: MSDS UPLOAD FILE PDF -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">MSDS File (PDF) <span class="text-red-500">*</span></label>
                    <input type="file" name="msds" accept="application/pdf,.pdf" required
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-xs text-gray-500">Upload dokumen MSDS format PDF.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Price <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-sm text-gray-500">Rp</span>
                        </div>
                        <input type="number" name="price" value="{{ old('price') }}" required placeholder="0"
                               class="block w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Buffer Stock Limit <span class="text-red-500">*</span></label>
                    <input type="number" name="buffer_stock" value="{{ old('buffer_stock', 5) }}" required placeholder="Minimum stock quantity"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex justify-end gap-3 pt-4">
            <button type="reset" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Reset Form</button>
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors flex items-center">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Reagent
            </button>
        </div>
    </form>
</div>

<!-- Script JS (Sama seperti sebelumnya, tidak ada perubahan) -->
@push('scripts')
<script>
// ... (Salin seluruh script JS dari file asli Anda di sini) ...
</script>
@endpush
@endsection