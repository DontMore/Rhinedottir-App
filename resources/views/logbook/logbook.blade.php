@extends('layout.main')
@section('title', 'Reagent Logbook')

@section('container')
<div class="space-y-6">
    <!-- Header & Search -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Reagent Logbook</h2>
            <p class="text-sm text-gray-500 mt-1">Track usage history and manage reagent allocations.</p>
        </div>
        <div class="w-full sm:w-80">
            <form action="{{ route('logbook.index') }}" method="GET">
                <div class="relative">
                    <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Search reagent..."
                        class="block w-full rounded-lg border-gray-200 bg-white pl-10 pr-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 p-1.5 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catalog</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($reagens as $index => $reagen)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="hidden lg:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reagens->firstItem() + $index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $reagen->noCatalog }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $reagen->nameReagen }}</td>
                        <td class="hidden lg:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reagen->merk }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $qty = $reagen->stockReagen ? $reagen->stockReagen->quantity : 0;
                                $colorClass = $qty > 0 ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-red-50 text-red-700 ring-red-600/20';
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $colorClass }}">
                                {{ $qty }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('data.history', ['guid' => $reagen->guid]) }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">
                                    <svg class="-ml-0.5 mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    History
                                </a>
                                @if($reagen->msds)
                                <a href="{{ $reagen->msds }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-md transition-colors">
                                    <svg class="-ml-0.5 mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    MSDS
                                </a>
                                @endif
                                @auth
                                <a href="{{ route('data.take', ['guid' => $reagen->guid]) }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors">
                                    <svg class="-ml-0.5 mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    Take
                                </a>
                                @endauth
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                            No reagents found. Try adjusting your search query.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($reagens->hasPages())
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
            <nav aria-label="Pagination" class="flex items-center justify-between">
                <div class="hidden sm:block text-sm text-gray-500">
                    Showing <span class="font-medium">{{ $reagens->firstItem() }}</span> to <span class="font-medium">{{ $reagens->lastItem() }}</span> of <span class="font-medium">{{ $reagens->total() }}</span> results
                </div>
                <div class="flex-1 flex justify-end sm:justify-center">
                    <ul class="inline-flex -space-x-px rounded-md shadow-sm">
                        @if ($reagens->onFirstPage())
                            <li><span class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 rounded-l-md cursor-not-allowed">Previous</span></li>
                        @else
                            <li><a href="{{ $reagens->previousPageUrl() }}" class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-l-md">Previous</a></li>
                        @endif

                        @foreach ($reagens->links()->elements[0] as $page => $url)
                            @if ($page === '...')
                                <li><span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span></li>
                            @elseif ($page == $reagens->currentPage())
                                <li><span class="relative z-10 inline-flex items-center px-4 py-2 border border-blue-600 bg-blue-600 text-sm font-medium text-white">{{ $page }}</span></li>
                            @else
                                <li><a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">{{ $page }}</a></li>
                            @endif
                        @endforeach

                        @if ($reagens->hasMorePages())
                            <li><a href="{{ $reagens->nextPageUrl() }}" class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-r-md">Next</a></li>
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