@extends('layout.main')
@section('title', 'Stock Opname List')

@section('container')
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h2 class="text-xl font-semibold text-gray-800">Stock Opname History</h2>
        <p class="text-sm text-gray-500 mt-1">Browse and manage past stock reconciliation records.</p>
    </div>

    <!-- Filter Form -->
    <form action="" method="GET" class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            <div>
                <label for="month" class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wider">Month</label>
                <select name="month" id="month" class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    <option value="">All Months</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="year" class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wider">Year</label>
                <select name="year" id="year" class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    <option value="">All Years</option>
                    @foreach(range(date('Y'), date('Y')-5) as $y)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Search
                </button>
                <a href="{{ route('stock.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Card Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse ($data as $item)
            @php
                $date = \Carbon\Carbon::createFromDate($item->year, $item->month, 1);
                $isComplete = $item->status == 1;
            @endphp
            <div class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-200 overflow-hidden flex flex-col">
                <div class="p-4 flex flex-col flex-1">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg {{ $isComplete ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 00-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $date->format('M') }}</h3>
                                <p class="text-sm text-gray-500">{{ $date->format('Y') }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $isComplete ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-amber-600/20' }}">
                            {{ $isComplete ? 'Complete' : 'Pending' }}
                        </span>
                    </div>
                    <div class="mt-auto pt-3 border-t border-gray-100">
                        <a href="{{ route('soDetail', ['month' => $item->month, 'year' => $item->year]) }}" class="w-full inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No records found</h3>
                <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or generate a new stock opname.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($data->hasPages())
    <div class="pt-6 border-t border-gray-200">
        <nav aria-label="Pagination" class="flex items-center justify-center">
            <ul class="inline-flex -space-x-px rounded-lg shadow-sm">
                @if ($data->onFirstPage())
                    <li><span class="relative inline-flex items-center px-3 py-2 border border-gray-200 bg-gray-50 text-sm font-medium text-gray-400 rounded-l-lg cursor-not-allowed">Previous</span></li>
                @else
                    <li><a href="{{ $data->previousPageUrl() }}" class="relative inline-flex items-center px-3 py-2 border border-gray-200 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-l-lg">Previous</a></li>
                @endif

                @foreach ($data->links()->elements[0] as $page => $url)
                    @if ($page == '...')
                        <li><span class="relative inline-flex items-center px-4 py-2 border border-gray-200 bg-white text-sm font-medium text-gray-500">...</span></li>
                    @elseif ($page == $data->currentPage())
                        <li><span class="relative z-10 inline-flex items-center px-4 py-2 border border-blue-600 bg-blue-600 text-sm font-medium text-white">{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-200 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">{{ $page }}</a></li>
                    @endif
                @endforeach

                @if ($data->hasMorePages())
                    <li><a href="{{ $data->nextPageUrl() }}" class="relative inline-flex items-center px-3 py-2 border border-gray-200 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-r-lg">Next</a></li>
                @else
                    <li><span class="relative inline-flex items-center px-3 py-2 border border-gray-200 bg-gray-50 text-sm font-medium text-gray-400 rounded-r-lg cursor-not-allowed">Next</span></li>
                @endif
            </ul>
        </nav>
    </div>
    @endif
</div>
@endsection