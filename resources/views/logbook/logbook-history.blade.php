<!-- logbook-history.blade.php -->

@extends('layout.main')

@section('container')
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Reagen Details Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 flex justify-between items-center border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-900">Reagen Details</h1>
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-6">
                    <!-- Details List -->
                    <div class="divide-y divide-gray-200">
                        <div class="flex justify-between py-3">
                            <span class="text-gray-500">Catalog Number</span>
                            <span class="font-medium text-gray-900">{{ $reagen->noCatalog }}</span>
                        </div>
                        <div class="flex justify-between py-3">
                            <span class="text-gray-500">Reagent Name</span>
                            <span class="font-medium text-gray-900">{{ $reagen->nameReagen }}</span>
                        </div>
                        <div class="flex justify-between py-3">
                            <span class="text-gray-500">Brand</span>
                            <span class="font-medium text-gray-900">{{ $reagen->merk }}</span>
                        </div>
                        <div class="flex justify-between py-3">
                            <span class="text-gray-500">Current Stock</span>
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-sm font-medium {{ optional($reagen->stockReagen)->quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ optional($reagen->stockReagen)->quantity ?? '0' }}
                            </span>
                        </div>
                    </div>

                    <!-- Hazard Symbols -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-sm font-medium text-gray-900 mb-4">Hazard Symbols</h2>
                        <div class="grid grid-cols-3 gap-4">
                            @php
                                $hazardOptions = explode(',', $reagen->hazardOptions);
                            @endphp
                            @foreach($hazardOptions as $hazard)
                                <div class="flex flex-col items-center">
                                    @php
                                        $imagePath = match($hazard) {
                                            'Environment' => 'Environmental-Hazard',
                                            default => strtolower($hazard)
                                        };
                                    @endphp
                                    <img src="{{ asset('public/images/' . $imagePath . '.png') }}"
                                         alt="{{ $hazard }}"
                                         class="w-12 h-12 object-contain transform transition hover:scale-110">
                                    <span class="mt-2 text-xs text-gray-500">{{ $hazard }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Batch Information -->
                <div class="bg-gray-50 rounded-lg overflow-hidden">
                    <div class="px-4 py-3 bg-gray-100 border-b border-gray-200">
                        <h2 class="text-sm font-medium text-gray-900">Batch Information</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach ($reagen->reagenIn as $stock)
                        <div class="px-4 py-3">
                            <div class="flex justify-between items-center">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $stock->batch }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    Expires: {{ \Carbon\Carbon::parse($stock->expiredDate)->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage History Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Usage History</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($logbookReagens as $logbook)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-calendar3 text-muted"></i>
                                        <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($logbook->formatted_created_at)->format('d M Y H:i') }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                                        {{ $logbook->quantity_taken }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-person text-muted"></i>
                                        <span class="text-sm text-gray-900">{{ $logbook->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $logbook->note ?: '-' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex justify-center">
                @if ($logbookReagens->hasPages())
                    <nav aria-label="Page navigation">
                        <ul class="inline-flex -space-x-px text-sm">
                            {{-- Previous Page Link --}}
                            @if ($logbookReagens->onFirstPage())
                                <li>
                                    <span class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-400 bg-gray-100 border border-e-0 border-gray-300 rounded-s-lg cursor-not-allowed">Previous</span>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $logbookReagens->previousPageUrl() }}" class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($logbookReagens->links()->elements[0] as $page => $url)
                                @if ($page == $logbookReagens->currentPage())
                                    <li>
                                        <span aria-current="page" class="flex items-center justify-center px-3 h-8 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700">{{ $page }}</span>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ $url }}" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($logbookReagens->hasMorePages())
                                <li>
                                    <a href="{{ $logbookReagens->nextPageUrl() }}" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                                </li>
                            @else
                                <li>
                                    <span class="flex items-center justify-center px-3 h-8 leading-tight text-gray-400 bg-gray-100 border border-gray-300 rounded-e-lg cursor-not-allowed">Next</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
