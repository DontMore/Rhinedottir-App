@extends('layout.main')
@section('title', 'Edit Reagent')
@section('container')
<style>
/* 🎨 Soft UI Global Styles */
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap');

.soft-ui-page {
    font-family: 'Nunito', 'Poppins', sans-serif;
    background-color: #F8FAFC;
    color: #334155;
    min-height: 100vh;
    padding: 24px;
}

.soft-ui-page h2, .soft-ui-page h3, .soft-ui-page h4 {
    color: #1E293B;
    font-weight: 800;
    letter-spacing: -0.02em;
}

.soft-card {
    background: #FFFFFF;
    border-radius: 28px;
    box-shadow: 0px 15px 35px rgba(0,0,0,0.04);
    padding: 32px;
    margin-bottom: 24px;
    border: none;
    transition: all 0.3s ease;
}

.soft-input {
    background: #F1F5F9;
    border: none !important;
    border-radius: 50px;
    padding: 14px 24px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #334155;
    width: 100%;
    transition: all 0.3s ease;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
}

.soft-input:focus {
    outline: none;
    background: #FFFFFF;
    box-shadow: 0 0 0 4px rgba(99,102,241,0.1), inset 0 2px 4px rgba(0,0,0,0);
}

select.soft-input {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 24px center;
    background-repeat: no-repeat;
    background-size: 16px;
    padding-right: 56px;
}

.soft-btn-primary {
    background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
    color: white;
    border-radius: 50px;
    padding: 14px 32px;
    font-weight: 800;
    font-size: 0.9rem;
    box-shadow: 0px 10px 25px rgba(79, 70, 229, 0.25);
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.soft-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0px 15px 30px rgba(79, 70, 229, 0.35);
}

.soft-btn-secondary {
    background: #F1F5F9;
    color: #475569;
    border-radius: 50px;
    padding: 14px 32px;
    font-weight: 800;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}

.soft-btn-secondary:hover {
    background: #E2E8F0;
    transform: translateY(-1px);
}

.label-soft {
    display: block;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #64748B;
    margin-bottom: 8px;
    margin-left: 4px;
}
</style>

<div class="soft-ui-page">
    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Edit Reagent Details</h2>
            <p class="text-sm font-medium text-slate-500 mt-1">Update specifications, storage location, and hazard classifications.</p>
        </div>
        <a href="{{ url()->previous() }}" class="inline-flex items-center px-6 py-3 text-sm font-bold text-slate-600 bg-white rounded-full shadow-[0px_10px_20px_rgba(0,0,0,0.04)] hover:shadow-[0px_15px_25px_rgba(0,0,0,0.06)] hover:-translate-y-0.5 transition-all">
            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m0 0h12"/></svg>
            Back
        </a>
    </div>

    <!-- ✅ PENTING: enctype="multipart/form-data" wajib ada untuk upload file -->
    <form action="{{ route('data.update', $data->guid) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        @if ($errors->any())
            <div class="bg-red-50/80 p-6 rounded-[24px] mb-6 shadow-[0px_10px_25px_rgba(239,68,68,0.05)]">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-red-800">Please correct the following errors:</h3>
                        <ul class="mt-2 text-sm text-red-600 list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- SECTION 1: BASIC INFO -->
        <div class="soft-card">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 flex items-center justify-center">
                    <svg class="h-6 w-6 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800">Basic Information</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Core details of the reagent</p>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-1">
                    <label class="label-soft">Catalog Number</label>
                    <input type="text" value="{{ old('noCatalog', $data->noCatalog) }}" readonly class="soft-input !bg-slate-100 !text-slate-500 cursor-not-allowed">
                    <input type="hidden" name="noCatalog" value="{{ old('noCatalog', $data->noCatalog) }}">
                </div>
                <div class="lg:col-span-2">
                    <label class="label-soft">Reagent Name <span class="text-red-400">*</span></label>
                    <input type="text" name="nameReagen" value="{{ old('nameReagen', $data->nameReagen) }}" required placeholder="e.g., Acetone AR" class="soft-input">
                </div>
                <div>
                    <label class="label-soft">Brand / Merk <span class="text-red-400">*</span></label>
                    <input type="text" name="merk" value="{{ old('merk', $data->merk) }}" required placeholder="e.g., Merck" class="soft-input">
                </div>
                <div>
                    <label class="label-soft">Pack Size <span class="text-red-400">*</span></label>
                    <input type="text" name="packSize" value="{{ old('packSize', $data->packSize) }}" required placeholder="e.g., 500ml" class="soft-input">
                </div>
            </div>
        </div>

        <!-- SECTION 2: CLASSIFICATION -->
        <div class="soft-card">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center">
                    <svg class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800">Classification</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Group and category mapping</p>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label class="label-soft">Reagen Group</label>
                    <input type="text" placeholder="🔍 Search group..." class="group-search soft-input !bg-white !shadow-[0px_5px_15px_rgba(0,0,0,0.02)] mb-2">
                    <select name="group_guid" class="group-select soft-input">
                        <option value="">-- Select Group --</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->guid }}" {{ old('group_guid', $data->group_guid) == $group->guid ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label-soft">Reagen Category</label>
                    <input type="text" placeholder="🔍 Search category..." class="category-search soft-input !bg-white !shadow-[0px_5px_15px_rgba(0,0,0,0.02)] mb-2">
                    <select name="category_guid" class="category-select soft-input">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->guid }}" {{ old('category_guid', $data->category_guid) == $category->guid ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- SECTION 3: STORAGE LOGIC & HAZARDS -->
        <div class="soft-card">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center">
                    <svg class="h-6 w-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800">Storage Logic & Hazards</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Safety and physical properties</p>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="label-soft">Physical Form <span class="text-red-400">*</span></label>
                    <select name="reagent_form" id="reagentForm" required class="soft-input">
                        <option value="">-- Select Form --</option>
                        <option value="liquid" {{ old('reagent_form', $data->reagent_form) == 'liquid' ? 'selected' : '' }}>Liquid (Cairan)</option>
                        <option value="solid" {{ old('reagent_form', $data->reagent_form) == 'solid' ? 'selected' : '' }}>Solid (Padatan)</option>
                        <option value="crystal" {{ old('reagent_form', $data->reagent_form) == 'crystal' ? 'selected' : '' }}>Crystal (Kristal)</option>
                    </select>
                </div>
                <div>
                    <label class="label-soft">Signal Word (GHS)</label>
                    <select name="signal_word" class="soft-input">
                        <option value="">-- Select Signal Word --</option>
                        <option value="Danger" {{ old('signal_word', $data->signal_word) == 'Danger' ? 'selected' : '' }}>Danger</option>
                        <option value="Warning" {{ old('signal_word', $data->signal_word) == 'Warning' ? 'selected' : '' }}>Warning</option>
                        <option value="None" {{ old('signal_word', $data->signal_word) == 'None' ? 'selected' : '' }}>None</option>
                    </select>
                </div>
            </div>
            <div class="mb-8">
                <label class="label-soft">Recommended Storage</label>
                <div id="recommendedStorageText" class="block w-full rounded-[20px] border-2 border-indigo-100 bg-indigo-50/50 px-6 py-4 text-sm text-indigo-700 font-bold min-h-[52px] flex items-center">
                    {{ $data->recommended_storage_location ?? 'Select Form & Hazards to see recommendation...' }}
                </div>
                <p class="mt-2 text-xs font-semibold text-indigo-500 ml-1">Based on physical form + hazard classification</p>
            </div>
            <div>
                <label class="label-soft mb-4">Hazard Symbols</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
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
                                {{ in_array($hazard['name'], old('hazardOptions', $hazardOptions)) ? 'checked' : '' }}
                                class="peer absolute opacity-0 w-full h-full cursor-pointer z-10" onchange="updateRecommendedStorage()">
                            <label for="hazard{{ $hazard['id'] }}"
                                class="flex flex-col items-center p-5 bg-slate-50 rounded-[24px] cursor-pointer transition-all hover:bg-slate-100 peer-checked:bg-indigo-50 peer-checked:shadow-[0px_10px_25px_rgba(99,102,241,0.1)] peer-checked:ring-2 peer-checked:ring-indigo-200">
                                <img src="{{ asset('public/images/' . $hazard['image']) }}" alt="{{ $hazard['name'] }}"
                                    class="w-12 h-12 object-contain mb-3 transition-transform peer-checked:scale-110">
                                <span class="text-xs font-bold text-slate-600 peer-checked:text-indigo-600">{{ $hazard['name'] }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- SECTION 4: ACTUAL STORAGE & PRICING -->
        <div class="soft-card">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center">
                    <svg class="h-6 w-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800">Details & Pricing</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Storage location and financial data</p>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <label class="label-soft">Actual Storage Location</label>
                    <select name="storage_location_guid" class="soft-input">
                        <option value="">-- Select Location --</option>
                        @foreach($storageLocations as $location)
                            <option value="{{ $location->guid }}" {{ old('storage_location_guid', $data->storage_location_guid) == $location->guid ? 'selected' : '' }}>
                                {{ $location->code }} - {{ $location->name }} ({{ $location->state_type }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- ✅ PERBAIKAN MSDS: Input File & Route yang Benar -->
                <div>
                    <label class="label-soft">MSDS File (PDF)</label>
                    <input type="file" name="msds" accept="application/pdf,.pdf"
                           class="block w-full text-sm text-slate-500 font-semibold file:mr-4 file:py-2.5 file:px-5 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-indigo-100 file:text-indigo-600 hover:file:bg-indigo-200 file:cursor-pointer file:transition-colors bg-slate-50 rounded-full py-3 px-5">
                    
                    @if($data->msds)
                        <p class="mt-2 text-xs font-semibold text-slate-500 ml-1">
                            File saat ini: 
                            <!-- ✅ Menggunakan route 'management-stock.msds' yang sudah ada di web.php -->
                            <a href="{{ route('management-stock.msds', $data->guid) }}" target="_blank" class="text-indigo-600 underline hover:text-indigo-800 font-bold">
                                Lihat MSDS
                            </a> 
                            <span class="text-slate-400">(Kosongkan input di atas jika tidak ingin mengubah file)</span>
                        </p>
                    @else
                        <p class="mt-2 text-xs font-semibold text-slate-400 ml-1">Upload dokumen MSDS format PDF.</p>
                    @endif
                </div>

                <div>
                    <label class="label-soft">Price <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                            <span class="text-sm font-bold text-slate-400">Rp</span>
                        </div>
                        <input type="number" name="price" value="{{ old('price', $data->price) }}" required placeholder="0"
                               class="soft-input !pl-14">
                    </div>
                </div>
                <div class="sm:col-span-2 lg:col-span-4">
                    <label class="label-soft">Buffer Stock Limit <span class="text-red-400">*</span></label>
                    <input type="number" name="buffer_stock" value="{{ old('buffer_stock', $data->buffer_stock) }}" required placeholder="Minimum stock quantity"
                           class="soft-input max-w-xs">
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex justify-end gap-4 pt-4 pb-8">
            <a href="{{ url()->previous() }}" class="soft-btn-secondary">Cancel</a>
            <button type="submit" class="soft-btn-primary flex items-center">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Update Reagent
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Search Logic for Dropdowns
document.addEventListener('DOMContentLoaded', function() {
    function attachSearch(searchSelector, selectSelector) {
        const searchInput = document.querySelector(searchSelector);
        const select = document.querySelector(selectSelector);
        if (!searchInput || !select) return;

        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            Array.from(select.options).forEach(option => {
                if (option.value === "") { option.style.display = ''; return; }
                option.style.display = option.text.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
        select.addEventListener('change', () => { searchInput.value = ''; });
    }
    attachSearch('.group-search', '.group-select');
    attachSearch('.category-search', '.category-select');
});

// Logic Rekomendasi Storage
function updateRecommendedStorage() {
    const form = document.getElementById('reagentForm').value;
    const selectedHazards = Array.from(document.querySelectorAll('input[name="hazardOptions[]"]:checked')).map(cb => cb.value);

    const recDiv = document.getElementById('recommendedStorageText');
    if (!form) {
        recDiv.textContent = 'Please select Physical Form first...';
        recDiv.className = 'block w-full rounded-[20px] border-2 border-slate-200 bg-slate-50 px-6 py-4 text-sm text-slate-500 font-bold min-h-[52px] flex items-center';
        return;
    }

    const hazardPriority = { 'Explosive': 1, 'Flammable': 2, 'Toxic': 3, 'Corrosive': 4, 'Oxidising': 5, 'Carcinogen': 6, 'Environment': 7, 'Irritant': 8 };
    selectedHazards.sort((a, b) => (hazardPriority[a] || 99) - (hazardPriority[b] || 99));
    const primaryHazard = selectedHazards.length > 0 ? selectedHazards[0] : null;

    const storageMap = {
        liquid: {
            'Flammable': 'Flammable Liquid Cabinet - Lemari Tahan Api (NFPA Class I/II)',
            'Corrosive': 'Corrosive Liquid Cabinet - Rak Plastik/PP Tahan Asam-Basa',
            'Toxic': 'Poison Liquid Storage - Lemari Terkunci + Ventilasi',
            'Oxidising': 'Oxidizer Liquid Storage - Terpisah dari Bahan Mudah Terbakar',
            'Carcinogen': 'Carcinogen Liquid Storage - Area Terkontrol Fume Hood',
            'Environment': 'Hazardous Liquid Waste Area - Kontainer B3',
            'Explosive': '⚠️ EXPLOSIVE LIQUID - Ruang Khusus Berstandar ATP',
            'Irritant': 'General Liquid Storage - Rak Kimia Basah',
            null: 'General Liquid Storage - Rak Kimia Basah (Non-Hazard)'
        },
        solid: {
            'Flammable': 'Flammable Solid Storage - Lemari Kering Tahan Api',
            'Corrosive': 'Corrosive Solid Storage - Wadah Plastik Bersegel',
            'Toxic': 'Toxic Solid Storage - Lemari Terkunci + Label Jelas',
            'Oxidising': 'Oxidizer Solid Storage - Terpisah dari Reduser/Bahan Organik',
            'Carcinogen': 'Carcinogen Solid Storage - Area Terkontrol',
            'Environment': 'Hazardous Solid Waste Area - Kontainer B3',
            'Explosive': '⚠️ EXPLOSIVE SOLID - Ruang Khusus Berstandar ATP',
            'Irritant': 'General Solid Storage - Rak Kimia Kering',
            null: 'General Solid Storage - Rak Kimia Kering (Non-Hazard)'
        },
        crystal: {
            'Flammable': 'Flammable Crystal Storage - Wadah Kaca Gelap + Kering',
            'Corrosive': 'Corrosive Crystal Storage - Desikator / Wadah Plastik',
            'Toxic': 'Toxic Crystal Storage - Lemari Terkunci + Kelembaban Rendah',
            'Oxidising': 'Oxidizer Crystal Storage - Terpisah, Suhu Stabil',
            'Carcinogen': 'Carcinogen Crystal Storage - Area Terkontrol',
            'Environment': 'Hazardous Crystal Waste Area',
            'Explosive': '⚠️ EXPLOSIVE CRYSTAL - Ruang Khusus Berstandar ATP',
            'Irritant': 'General Crystal Storage - Rak Kering + Silica Gel',
            null: 'General Crystal Storage - Rak Kering + Desikator (Non-Hazard)'
        }
    };

    const recommendation = storageMap[form]?.[primaryHazard] || 'General Storage';

    recDiv.textContent = recommendation;
    recDiv.className = 'block w-full rounded-[20px] border-2 border-indigo-100 bg-indigo-50/50 px-6 py-4 text-sm text-indigo-700 font-bold min-h-[52px] flex items-center';
}

// Event Listeners
const formSelect = document.getElementById('reagentForm');
if(formSelect) formSelect.addEventListener('change', updateRecommendedStorage);
document.querySelectorAll('input[name="hazardOptions[]"]').forEach(cb => cb.addEventListener('change', updateRecommendedStorage));

// Jalankan saat load untuk memastikan value awal benar
updateRecommendedStorage();
</script>
@endpush
@endsection