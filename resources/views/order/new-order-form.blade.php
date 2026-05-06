@extends('layout.main')
@section('container')

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">New Order Form</h1>
            <p class="text-sm text-gray-500 mt-1">Fill in the details below to submit a new reagent order request.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('order.eksisting') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <i class="bi bi-box-arrow-in-right"></i> Existing Order
            </a>
            <a href="{{ route('order.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- Form Card --}}
    <form action="{{ route('orders.store') }}" method="POST" id="orderForm" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- No Catalog --}}
            <div class="space-y-1">
                <label for="noCatalog" class="block text-sm font-medium text-gray-700">No Catalog</label>
                <div class="relative">
                    <input type="text" name="noCatalog" id="noCatalog" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition placeholder-gray-400 @error('noCatalog') border-red-500 @enderror"
                           value="{{ old('noCatalog', request('catalog')) }}" placeholder="e.g., 1000142500">
                    <div id="loadingSpinner" class="hidden absolute right-3 top-3">
                        <div class="animate-spin h-4 w-4 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                    </div>
                </div>
                @error('noCatalog') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Name Reagen --}}
            <div class="space-y-1">
                <label for="nameReagen" class="block text-sm font-medium text-gray-700">Reagent Name</label>
                <input type="text" name="nameReagen" id="nameReagen" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition placeholder-gray-400 @error('nameReagen') border-red-500 @enderror"
                       value="{{ old('nameReagen') }}" placeholder="e.g., Ethanol Absolute">
                @error('nameReagen') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Merk --}}
            <div class="space-y-1">
                <label for="merk" class="block text-sm font-medium text-gray-700">Brand / Merk</label>
                <input type="text" name="merk" id="merk" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition placeholder-gray-400 @error('merk') border-red-500 @enderror"
                       value="{{ old('merk') }}" placeholder="e.g., Merck, Sigma">
                @error('merk') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Pack Size --}}
            <div class="space-y-1">
                <label for="packSize" class="block text-sm font-medium text-gray-700">Pack Size</label>
                <input type="text" name="packSize" id="packSize" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition placeholder-gray-400 @error('packSize') border-red-500 @enderror"
                       value="{{ old('packSize') }}" placeholder="e.g., 500 mL, 1 kg">
                @error('packSize') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Quantity --}}
            <div class="space-y-1 md:col-span-2">
                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" name="quantity" id="quantity" min="1" required
                       class="w-full md:w-1/3 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition placeholder-gray-400 @error('quantity') border-red-500 @enderror"
                       value="{{ old('quantity') }}" placeholder="1">
                @error('quantity') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Hidden Fields --}}
        <input type="hidden" name="status" value="0">
        <input type="hidden" name="userId" value="{{ auth()->id() }}">

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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function fetchReagenData(catalog) {
            if (!catalog) return;
            
            $('#loadingSpinner').removeClass('hidden');
            
            // Search for reagen data using existing endpoint
            // Note: The endpoint expects GUID, but we might need a search by catalog
            // Let's check if we can find it in the organization
            $.ajax({
                url: "{{ url('/reagen') }}/" + catalog, // This might need verification
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    if (data) {
                        $('#nameReagen').val(data.nameReagen);
                        $('#merk').val(data.merk);
                        $('#packSize').val(data.packSize);
                    }
                },
                complete: function() {
                    $('#loadingSpinner').addClass('hidden');
                }
            });
        }

        // Auto-fetch if catalog is in URL
        const urlParams = new URLSearchParams(window.location.search);
        const catalogParam = urlParams.get('catalog');
        if (catalogParam) {
            fetchReagenData(catalogParam);
        }

        // Fetch on manual input change
        $('#noCatalog').on('change', function() {
            fetchReagenData($(this).val());
        });
    });
</script>
@endpush

@endsection