@extends('layout.main')
@section('title', 'Admin Reagent Usage')

@section('container')
<div class="space-y-6">
    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Record Usage (Admin)</h2>
            <p class="text-sm text-gray-500 mt-1">Log reagent consumption for other analysts with custom dates.</p>
        </div>
        <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m0 0h12"/></svg>
            Back
        </a>
    </div>

    <!-- Info & Form Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Reagent Info Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Reagent Information</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Catalog No.</span>
                    <span class="text-sm font-medium text-gray-900">{{ $reagen->noCatalog }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Reagent Name</span>
                    <span class="text-sm font-medium text-gray-900">{{ $reagen->nameReagen }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-500">Brand</span>
                    <span class="text-sm font-medium text-gray-900">{{ $reagen->merk }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-500">Available Stock</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                        {{ $reagen->stockReagen?->quantity ?? 0 }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Admin Usage Form Card -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-800">Usage Details</h3>
            </div>
            <form action="/take-process-admin" method="POST" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="noCatalog" value="{{ $reagen->noCatalog }}">
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1.5">Date <span class="text-red-500">*</span></label>
                        <input type="date" id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required
                            class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                        @error('date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="batchSelect" class="block text-sm font-medium text-gray-700 mb-1.5">Batch Number <span class="text-red-500">*</span></label>
                        <select id="batchSelect" name="batch" required
                            class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            @forelse ($reagen->reagenIn as $stock)
                                <option value="{{ $stock->batch }}" {{ old('batch') == $stock->batch ? 'selected' : '' }}>{{ $stock->batch }}</option>
                            @empty
                                <option value="" disabled>No active batches</option>
                            @endforelse
                        </select>
                        @error('batch') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="quantity_taken" class="block text-sm font-medium text-gray-700 mb-1.5">Quantity Taken <span class="text-red-500">*</span></label>
                        <input type="number" id="quantity_taken" name="quantity_taken" value="{{ old('quantity_taken') }}" required min="1" placeholder="0"
                            class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                        @error('quantity_taken') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="analis" class="block text-sm font-medium text-gray-700 mb-1.5">Analyst <span class="text-red-500">*</span></label>
                        <select id="analis" name="analis" required
                            class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                            @foreach ($analisList as $id => $name)
                                <option value="{{ $id }}" {{ old('analis') == $id ? 'selected' : '' }}>{{ $name }} - {{ $id }}</option>
                            @endforeach
                        </select>
                        @error('analis') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="note" class="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                    <textarea id="note" name="note" rows="3" placeholder="Optional remarks about this usage..."
                        class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors resize-y">{{ old('note') }}</textarea>
                    @error('note') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                        <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Submit Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('errors') && session('errors')->has('quantity_taken'))
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'Insufficient Stock',
        text: 'Please recheck the available stock and the quantity being taken.',
        icon: 'warning',
        confirmButtonText: 'OK',
        confirmButtonColor: '#3b82f6'
    });
});
</script>
@endpush
@endif
@endsection