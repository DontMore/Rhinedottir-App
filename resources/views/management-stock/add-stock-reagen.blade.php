@extends('layout.main')
@section('title', 'Add Stock Reagen')

@section('container')
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h2 class="text-xl font-semibold text-gray-800">Add Stock Reagen</h2>
        <p class="text-sm text-gray-500 mt-1">Fill in the details below to record a new stock entry.</p>
    </div>

    <!-- Form Card -->
    <form action="{{ route('reagen.addstockreagen') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @csrf
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Catalog No (Read-only) -->
                <div>
                    <label for="noCatalog" class="block text-sm font-medium text-gray-700 mb-1.5">Catalog Number</label>
                    <input type="text" id="noCatalog" name="noCatalog" value="{{ old('noCatalog', $reagen->noCatalog ?? '') }}" readonly
                        class="block w-full rounded-lg border-gray-200 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 cursor-not-allowed focus:outline-none">
                    @error('noCatalog') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Reagent Name (Read-only) -->
                <div>
                    <label for="namaReagen" class="block text-sm font-medium text-gray-700 mb-1.5">Reagent Name</label>
                    <input type="text" id="namaReagen" value="{{ $reagen->nameReagen ?? '' }}" readonly
                        class="block w-full rounded-lg border-gray-200 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 cursor-not-allowed focus:outline-none">
                </div>

                <!-- Brand (Read-only) -->
                <div>
                    <label for="merk" class="block text-sm font-medium text-gray-700 mb-1.5">Brand</label>
                    <input type="text" id="merk" value="{{ $reagen->merk ?? '' }}" readonly
                        class="block w-full rounded-lg border-gray-200 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 cursor-not-allowed focus:outline-none">
                </div>

                <!-- Batch Number -->
                <div>
                    <label for="batch" class="block text-sm font-medium text-gray-700 mb-1.5">Batch Number <span class="text-red-500">*</span></label>
                    <input type="text" id="batch" name="batch" value="{{ old('batch') }}" required placeholder="e.g., B2024001"
                        class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('batch') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Quantity -->
                <div>
                    <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-1.5">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" id="jumlah" name="quantity" value="{{ old('quantity') }}" required min="1" placeholder="0"
                        class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('quantity') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Expiry Date -->
                <div>
                    <label for="tanggalKadaluarsa" class="block text-sm font-medium text-gray-700 mb-1.5">Expiry Date <span class="text-red-500">*</span></label>
                    <input type="date" id="tanggalKadaluarsa" name="expiredDate" value="{{ old('expiredDate') }}" required
                        class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    @error('expiredDate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- COA File -->
                <div class="sm:col-span-2 lg:col-span-3">
                    <label for="coa" class="block text-sm font-medium text-gray-700 mb-1.5">COA File (PDF) <span class="text-red-500">*</span></label>
                    <input type="file" id="coa" name="coa" accept="application/pdf,.pdf" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="mt-1 text-xs text-gray-500">Upload dokumen Certificate of Analysis dalam format PDF.</p>
                    @error('coa') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                <textarea id="catatan" name="note" rows="3" placeholder="Optional remarks about this stock entry..."
                    class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors resize-y">{{ old('note') }}</textarea>
                @error('note') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Form Footer / Actions -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
            <a href="{{ route('management-stock.index') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-colors">
                Cancel
            </a>
            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Stock Entry
            </button>
        </div>
    </form>
</div>
@endsection