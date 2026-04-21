@extends('layout.main')

@section('container')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-xl font-semibold text-gray-800">Add New Reagen</h1>
            <a href="{{ route('management-stock.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
        
        <div class="px-6 py-6">

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 p-4">
                <h3 class="text-sm font-medium text-red-800">Ada kesalahan dalam input data:</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

            <form action="add-reagen" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catalog Number</label>
                        <input type="text" name="noCatalog" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reagen Name</label>
                        <input type="text" name="nameReagen" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                        <input type="text" name="merk" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pack Size</label>
                        <input type="text" name="packSize" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Hazard Symbol</label>
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
                                    ['id' => 8, 'name' => 'Oxidising', 'image' => 'oxidising.png']
                                ];
                            @endphp

                            @foreach($hazards as $hazard)
                            <div class="relative flex flex-col items-center">
                                <input type="checkbox" 
                                    id="hazard{{ $hazard['id'] }}" 
                                    name="hazardOptions[]" 
                                    value="{{ $hazard['name'] }}"
                                    class="peer absolute opacity-0 w-full h-full cursor-pointer">
                                <label for="hazard{{ $hazard['id'] }}" 
                                    class="flex flex-col items-center p-2 w-full text-center rounded-lg cursor-pointer border-2 border-transparent peer-checked:border-blue-500 hover:bg-gray-50">
                                    <img src="{{ asset('public/images/' . $hazard['image']) }}" 
                                        alt="{{ $hazard['name'] }}" 
                                        class="w-20 h-20 object-contain mb-2">
                                    <span class="text-sm text-gray-600">{{ $hazard['name'] }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">MSDS</label>
                        <input type="text" name="msds" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input type="number" name="price" required
                                class="block w-full pl-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="col-span-2 flex justify-end space-x-3 mt-6">
                        <button type="reset" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Reset
                        </button>
                        <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Save Reagen
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
