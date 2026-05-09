@extends('layout.main')
@section('container')

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Existing Order Form</h1>
            <p class="text-sm text-gray-500 mt-1">Select a registered reagent to quickly place a repeat order.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('order.new') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <i class="bi bi-plus-lg"></i> New Order
            </a>
            <a href="{{ route('order.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- Form Card --}}
    <form action="{{ route('orders.store') }}" method="POST" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-6">
        @csrf
        <input type="hidden" name="status" value="0">
        <input type="hidden" name="userId" value="{{ auth()->id() }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Reagent Selector --}}
            <div class="space-y-1 md:col-span-2">
                <label for="reagenSelect" class="block text-sm font-medium text-gray-700">Select Existing Reagent</label>
                <select id="reagenSelect" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white @error('noCatalog') border-red-500 @enderror">
                    <option value="">-- Choose Reagent --</option>
                    @foreach($reagens as $reagen)
                    {{-- 3. Tambahkan kondisi selected --}}
                    <option value="{{ $reagen->noCatalog }}"
                        data-name="{{ $reagen->nameReagen }}"
                        data-merk="{{ $reagen->merk }}"
                        data-pack="{{ $reagen->packSize }}"
                        {{ isset($selectedCatalog) && $reagen->noCatalog == $selectedCatalog ? 'selected' : '' }}>
                        {{ $reagen->nameReagen }} ({{ $reagen->noCatalog }})
                    </option>
                    @endforeach
                </select>
                @error('noCatalog') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Auto-filled Fields (Read-only) --}}
            <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-500">No Catalog</label>
                <input type="text" id="noCatalog" name="noCatalog" readonly
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 cursor-not-allowed"
                    placeholder="Auto-filled">
            </div>

            <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-500">Reagent Name</label>
                <input type="text" id="nameReagen" name="nameReagen" readonly
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 cursor-not-allowed"
                    placeholder="Auto-filled">
            </div>

            <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-500">Brand / Merk</label>
                <input type="text" id="merk" name="merk" readonly
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 cursor-not-allowed"
                    placeholder="Auto-filled">
            </div>

            <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-500">Pack Size</label>
                <input type="text" id="packSize" name="packSize" readonly
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 cursor-not-allowed"
                    placeholder="Auto-filled">
            </div>

            {{-- Quantity --}}
            <div class="space-y-1 md:col-span-2">
                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" name="quantity" id="quantity" min="1" required
                    class="w-full md:w-1/3 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition placeholder-gray-400 @error('quantity') border-red-500 @enderror"
                    value="{{ old('quantity') }}" placeholder="Enter quantity...">
                @error('quantity') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-3">
            <a href="{{ route('order.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition text-center">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-sm focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Submit Order
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('reagenSelect').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const noCatalog = this.value;
        const name = selectedOption.getAttribute('data-name') || '';
        const merk = selectedOption.getAttribute('data-merk') || '';
        const pack = selectedOption.getAttribute('data-pack') || '';

        // Update read-only fields
        document.getElementById('noCatalog').value = noCatalog;
        document.getElementById('nameReagen').value = name;
        document.getElementById('merk').value = merk;
        document.getElementById('packSize').value = pack;
    });

    // Reset fields when dropdown is cleared (optional UX polish)
    document.getElementById('reagenSelect').addEventListener('focus', function() {
        if (!this.value) {
            ['noCatalog', 'nameReagen', 'merk', 'packSize'].forEach(id => {
                document.getElementById(id).value = '';
            });
        }
    });

    // Trigger change event jika sudah ada value yang terpilih dari URL
    const select = document.getElementById('reagenSelect');
    if (select.value) {
        // Dispatch event change secara manual
        select.dispatchEvent(new Event('change'));
    }
</script>
@endpush

@endsection