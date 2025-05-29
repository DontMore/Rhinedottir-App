@extends('layout.main')

@section('container')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-xl font-semibold text-gray-800">Edit Reagen</h1>
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
        
        <div class="px-6 py-6">
            <form method="POST" action="{{ route('data.update', $data->noCatalog) }}">
                @csrf
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Catalog Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catalog Number</label>
                            <input type="text" name="noCatalog" value="{{ $data->noCatalog }}" readonly
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <!-- Reagen Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reagen Name</label>
                            <input type="text" name="nameReagen" value="{{ $data->nameReagen }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <!-- Brand -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Brand</label>
                            <input type="text" name="merk" value="{{ $data->merk }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <!-- Pack Size -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pack Size</label>
                            <input type="text" name="packSize" value="{{ $data->packSize }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Hazard Symbols -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Hazard Symbols</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-lg">
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
                                <input type="checkbox" 
                                    id="hazard{{ $hazard['id'] }}" 
                                    name="hazardOptions[]" 
                                    value="{{ $hazard['name'] }}"
                                    {{ in_array($hazard['name'], $hazardOptions) ? 'checked' : '' }}
                                    class="peer absolute opacity-0 w-full h-full cursor-pointer">
                                <label for="hazard{{ $hazard['id'] }}" 
                                    class="flex flex-col items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-100">
                                    <img src="{{ asset('public/images/' . $hazard['image']) }}" 
                                        alt="{{ $hazard['name'] }}" 
                                        class="w-16 h-16 object-contain mb-2 transition-transform peer-checked:scale-110">
                                    <span class="text-sm text-gray-600">{{ $hazard['name'] }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- MSDS Link -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">MSDS Link</label>
                            <input type="url" name="msds" value="{{ $data->msds }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Price</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="price" value="{{ $data->price }}" required
                                    class="block w-full pl-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="reset" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Reset
                        </button>
                        <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
