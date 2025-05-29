@extends('layout.main')

@section('container')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-1 lg:px-1">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Add Stock Reagen</h2>
            <p class="mt-1 text-sm text-gray-600">Please fill in the form below to add new reagent stock.</p>
        </div>

        <div class="px-6 py-6">
            <form action="{{ route('reagen.addstockreagen') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <!-- Read-only Fields -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="noCatalog" class="block text-sm font-medium text-gray-700">Catalog Number</label>
                            <input type="text" id="noCatalog" name="noCatalog" value="{{ $reagen->noCatalog }}" readonly
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="namaReagen" class="block text-sm font-medium text-gray-700">Reagent Name</label>
                            <input type="text" id="namaReagen" value="{{ $reagen->nameReagen }}" readonly
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="merk" class="block text-sm font-medium text-gray-700">Brand</label>
                            <input type="text" id="merk" value="{{ $reagen->merk }}" readonly
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="batch" class="block text-sm font-medium text-gray-700">Batch Number</label>
                            <input type="text" id="batch" name="batch" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="jumlah" class="block text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" id="jumlah" name="quantity" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="tanggalKadaluarsa" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                            <input type="date" id="tanggalKadaluarsa" name="expiredDate" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="catatan" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea id="catatan" name="note" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="{{ route('management-stock.index') }}" 
                            class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection