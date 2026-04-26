@extends('layout.main')
@section('title', 'Reagent Usage History')

@section('container')
<div class="space-y-6">
    <!-- Header & Breadcrumb -->
    <div>
        <nav class="flex items-center text-sm text-gray-500 mb-2">
            <a href="{{ route('dashboard.index') }}" class="hover:text-blue-600 transition">Dashboard</a>
            <svg class="w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-800 font-medium">Stock Out History</span>
        </nav>
        <h2 class="text-xl font-semibold text-gray-800">Reagent Usage</h2>
        <p class="text-sm text-gray-500 mt-1">Track all outgoing reagent movements and analyst assignments.</p>
    </div>

    <!-- Date Grouped List -->
    <div class="space-y-8">
        @forelse ($paginatedData as $date => $items)
        <div>
            <h3 class="flex items-center gap-3 text-sm font-semibold text-gray-700 mb-4">
                <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                <span class="flex-1 h-px bg-gray-200"></span>
            </h3>
            <div class="grid gap-3">
                @foreach ($items as $reagen)
                @php
                    $historyGuid = $reagen->reagen_guid ?? $reagen->reagen?->guid ?? null;
                @endphp

                @if($historyGuid)
                <a href="{{ route('data.history', ['guid' => $historyGuid]) }}"
                   class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white rounded-xl border border-gray-200 hover:border-red-300 hover:shadow-sm transition-all duration-200 group">
                @else
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-gray-50 rounded-xl border border-dashed border-gray-200 cursor-not-allowed opacity-70">
                @endif
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-3 sm:mb-0">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider">Reagent</p>
                            <p class="text-sm font-medium text-gray-900">{{ $reagen->reagen->nameReagen ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider">Catalog No.</p>
                            <p class="text-sm text-gray-700">{{ $reagen->noCatalog ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider">Batch</p>
                            <p class="text-sm text-gray-700">{{ $reagen->batch ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider">Analyst</p>
                            <p class="text-sm text-gray-700 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $reagen->user->name ?? 'Unknown' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 sm:justify-end">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20">
                            -{{ number_format($reagen->quantity_taken ?? $reagen->quantity ?? 0, 0, ',', '.') }}
                        </span>
                    </div>

                @if($historyGuid)
                </a>
                @else
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @empty
        <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No usage records found</h3>
            <p class="mt-1 text-sm text-gray-500">Outgoing reagent movements will appear here once recorded.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($paginatedData->hasPages())
    <div class="pt-4 border-t border-gray-200">
        {{ $paginatedData->links() }}
    </div>
    @endif
</div>
@endsection