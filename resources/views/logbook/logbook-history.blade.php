@extends('layout.main')
@section('title', 'Reagent Log History')

@section('container')
<div class="space-y-6">
    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Reagent History</h2>
            <p class="text-sm text-gray-500 mt-1">View usage logs, batch details, and hazard information.</p>
        </div>
        <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h12"/></svg>
            Back
        </a>
    </div>

    <!-- Details & Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- General Info -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-800">General Information</h3>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Catalog Number</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">{{ $reagen->noCatalog }}</span>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Reagent Name</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">{{ $reagen->nameReagen }}</span>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Brand</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">{{ $reagen->merk }}</span>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2 items-center">
                    <span class="text-sm text-gray-500">Current Stock</span>
                    <span class="sm:col-span-2">
                        @php
                            $qty = $reagen->stockReagen?->quantity ?? 0;
                            $colorClass = $qty > 0 ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-red-50 text-red-700 ring-red-600/20';
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $colorClass }}">
                            {{ $qty }}
                        </span>
                    </span>
                </div>
                @if($reagen->msds)
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2 items-center">
                    <span class="text-sm text-gray-500">Safety Data Sheet</span>
                    <span class="sm:col-span-2">
                        <a href="{{ $reagen->msds }}" target="_blank" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 rounded-lg transition-all">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            View MSDS Document
                        </a>
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Hazards & Batches -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-semibold text-gray-800">Hazard Symbols</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-3 gap-3">
                        @php
                            $hazardOptions = explode(',', $reagen->hazardOptions ?? '');
                        @endphp
                        @foreach($hazardOptions as $hazard)
                            @if(trim($hazard))
                                @php
                                    $imagePath = match(trim($hazard)) {
                                        'Environment' => 'Environmental-Hazard',
                                        default => strtolower(trim($hazard))
                                    };
                                @endphp
                                <div class="flex flex-col items-center p-2 bg-gray-50 rounded-lg border border-gray-100">
                                    <img src="{{ asset('public/images/' . $imagePath . '.png') }}"
                                         alt="{{ trim($hazard) }}"
                                         class="w-10 h-10 object-contain"
                                         onerror="this.style.display='none'">
                                    <span class="mt-1.5 text-[10px] font-medium text-gray-600 text-center leading-tight">{{ trim($hazard) }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-base font-semibold text-gray-800">Active Batches</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($reagen->reagenIn as $stock)
                    <div class="px-6 py-3 flex justify-between items-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10">
                            {{ $stock->batch }}
                        </span>
                        <span class="text-xs text-gray-500">Exp: {{ \Carbon\Carbon::parse($stock->expiredDate)->format('d M Y') }}</span>
                    </div>
                    @empty
                    <div class="px-6 py-4 text-center text-sm text-gray-400">No batch data available.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Usage History Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-800">Usage History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($logbookReagens as $logbook)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ \Carbon\Carbon::parse($logbook->created_at)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                {{ $logbook->quantity_taken }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $logbook->user?->name ?? 'System' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $logbook->note }}">
                            {{ $logbook->note ?: '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                            No usage records found for this reagent.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($logbookReagens->hasPages())
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
            <nav aria-label="Pagination" class="flex items-center justify-between">
                <div class="hidden sm:block text-sm text-gray-500">
                    Showing <span class="font-medium">{{ $logbookReagens->firstItem() }}</span> to <span class="font-medium">{{ $logbookReagens->lastItem() }}</span> of <span class="font-medium">{{ $logbookReagens->total() }}</span> results
                </div>
                <div class="flex-1 flex justify-end sm:justify-center">
                    <ul class="inline-flex -space-x-px rounded-md shadow-sm">
                        @if ($logbookReagens->onFirstPage())
                            <li><span class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 rounded-l-md cursor-not-allowed">Previous</span></li>
                        @else
                            <li><a href="{{ $logbookReagens->previousPageUrl() }}" class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-l-md">Previous</a></li>
                        @endif

                        @foreach ($logbookReagens->links()->elements[0] as $page => $url)
                            @if ($page === '...')
                                <li><span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span></li>
                            @elseif ($page == $logbookReagens->currentPage())
                                <li><span class="relative z-10 inline-flex items-center px-4 py-2 border border-blue-600 bg-blue-600 text-sm font-medium text-white">{{ $page }}</span></li>
                            @else
                                <li><a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">{{ $page }}</a></li>
                            @endif
                        @endforeach

                        @if ($logbookReagens->hasMorePages())
                            <li><a href="{{ $logbookReagens->nextPageUrl() }}" class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-r-md">Next</a></li>
                        @else
                            <li><span class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 rounded-r-md cursor-not-allowed">Next</span></li>
                        @endif
                    </ul>
                </div>
                <div class="hidden sm:block w-1/3"></div>
            </nav>
        </div>
        @endif
    </div>
</div>
@endsection