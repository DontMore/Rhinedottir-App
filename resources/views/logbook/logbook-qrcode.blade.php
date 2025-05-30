<!-- logbook-take.blade.php -->

@extends('layout.main')

@section('container')
    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Error Modal -->
        @if(session('errors') && session('errors')->has('quantity_taken'))
        <div id="exampleModalCenter" class="fixed inset-0 z-50 hidden">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-2">Insufficient stock</h3>
                            <p class="text-sm text-gray-500">Please recheck the stock and the quantity being taken.</p>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeModal()">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <script>
            function closeModal() {
                document.getElementById('exampleModalCenter').classList.add('hidden');
            }
            
            document.addEventListener('DOMContentLoaded', function() {
                @if(session('errors') && session('errors')->has('quantity_taken'))
                    document.getElementById('exampleModalCenter').classList.remove('hidden');
                @endif
            });
        </script>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Logbook</h1>
            </div>

            <div class="p-6">
                <div class="grid gap-4 mb-6">
                    <div class="flex">
                        <span class="w-32 text-gray-600">Catalog Number</span>
                        <span class="text-gray-900">: {{ $reagenIn->noCatalog }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-gray-600">Reagent Name</span>
                        <span class="text-gray-900">: {{ $reagenIn->Reagen->nameReagen }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-gray-600">Merk</span>
                        <span class="text-gray-900">: {{ $reagenIn->Reagen->merk }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-gray-600">Stock</span>
                        <span class="text-gray-900">: {{ optional($reagenIn->stockReagen)->quantity ?? 'N/A' }}</span>
                    </div>
                </div>

                <form action="/take-process" method="post" class="space-y-6">
                    @csrf
                    <input type="hidden" value="{{ $reagenIn->noCatalog }}" name="noCatalog">
                    <input type="hidden" value="{{ auth()->user()->id }}" name="user_id">
                    <input type="hidden" class="form-control" name="quantity_taken" value="1">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Batch Number</label>
                        <input type="text" name="batch" value="{{ $reagenIn->batch }}" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expired Date</label>
                        <input type="text" id="expiredDateInput" value="{{ $reagenIn->expiredDate }}" readonly 
                               class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="note" rows="3" 
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                    </div>

                    <div>
                        <button type="submit" 
                                class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
