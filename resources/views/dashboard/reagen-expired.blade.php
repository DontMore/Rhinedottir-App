@extends('layout.main')

@section('container')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Expired Reagents</h1>
            <p class="mt-2 text-sm text-gray-600">A detailed list of all expired and near-expiry reagents</p>
        </div>
        <div class="w-full md:w-72">
            <label for="search" class="sr-only">Search</label>
            <div class="relative text-gray-600">
                <input type="search" name="search" placeholder="Search reagents..." class="bg-white h-10 px-5 pr-10 rounded-lg text-sm focus:outline-none border border-gray-300 w-full">
                <button type="submit" class="absolute right-0 top-0 mt-3 mr-4">
                    <svg class="h-4 w-4 fill-current text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M10,2A8,8,0,1,0,18,10,8,8,0,0,0,10,2Zm0,14A6,6,0,1,1,16,10,6,6,0,0,1,10,16Z"/>
                        <line x1="21" y1="21" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Catalog No.</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Brand</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Expired Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($reagenED as $reagen)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reagen->noCatalog }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reagen->reagen ? $reagen->reagen->nameReagen : 'Unknown' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reagen->reagen ? $reagen->reagen->brand : 'Unknown' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $reagen->quantity <= 10 ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $reagen->quantity }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($reagen->expiredDate)->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $today = new DateTime();
                            $expDate = new DateTime($reagen->expiredDate);
                            $interval = $today->diff($expDate);
                            $daysRemaining = $interval->invert ? -$interval->days : $interval->days;
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-medium
                            {{ $daysRemaining < 0 ? 'bg-red-100 text-red-800' : 
                               ($daysRemaining <= 30 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                            <svg class="mr-1.5 h-2 w-2 
                                {{ $daysRemaining < 0 ? 'text-red-400' : 
                                   ($daysRemaining <= 30 ? 'text-yellow-400' : 'text-green-400') }}" 
                                 fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            {{ $daysRemaining < 0 ? 'Expired' : 
                               ($daysRemaining <= 30 ? 'Near Expiry' : 'Valid') }}
                            <span class="ml-1 text-gray-500">({{ abs($daysRemaining) }} days)</span>
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No expired reagents found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if ($reagenED->hasPages())
        <div class="px-4 py-6 flex items-center justify-center border-t border-gray-200">
            <nav aria-label="Page navigation example">
                <ul class="inline-flex -space-x-px text-base h-10">
                    {{-- Previous Page Link --}}
                    @if ($reagenED->onFirstPage())
                        <li>
                            <span class="flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-400 bg-gray-100 border border-e-0 border-gray-300 rounded-s-lg cursor-not-allowed">Previous</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $reagenED->previousPageUrl() }}" class="flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <li>
                                <span class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300">{{ $element }}</span>
                            </li>
                        @endif
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $reagenED->currentPage())
                                    <li>
                                        <a aria-current="page" class="flex items-center justify-center px-4 h-10 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700">{{ $page }}</a>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ $url }}" class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($reagenED->hasMorePages())
                        <li>
                            <a href="{{ $reagenED->nextPageUrl() }}" class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                        </li>
                    @else
                        <li>
                            <span class="flex items-center justify-center px-4 h-10 leading-tight text-gray-400 bg-gray-100 border border-gray-300 rounded-e-lg cursor-not-allowed">Next</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
        @endif
    </div>
</div>
@endsection
