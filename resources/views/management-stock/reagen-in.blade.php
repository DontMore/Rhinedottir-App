@extends('layout.main')
@section('title', 'Reagen In History')

@section('container')
<div class="space-y-6">
    <!-- Header & Breadcrumb -->
    <div>
        <nav class="flex items-center text-sm text-gray-500 mb-2">
            <a href="{{ route('dashboard.index') }}" class="hover:text-blue-600 transition">Dashboard</a>
            <svg class="w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-800 font-medium">Stock In History</span>
        </nav>
        <h2 class="text-xl font-semibold text-gray-800">Reagen Received</h2>
        <p class="text-sm text-gray-500 mt-1">Track all incoming reagent movements and deliveries.</p>
    </div>

    <!-- Date Grouped List -->
    <div class="space-y-8">
        @forelse ($paginatedData as $date => $items)
        <div>
            <h3 class="flex items-center gap-3 text-sm font-semibold text-gray-700 mb-4">
                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                <span class="flex-1 h-px bg-gray-200"></span>
            </h3>
            <div class="grid gap-3">
                @foreach ($items as $reagen)
                <a href="{{ route('data.view', ['guid' => $reagen->reagen_guid ?? $reagen->guid ?? '']) }}"
                   class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-sm transition-all duration-200 group">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3 sm:mb-0">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider">Catalog No.</p>
                            <p class="text-sm font-medium text-gray-900">{{ $reagen->noCatalog }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider">Reagent</p>
                            <p class="text-sm font-medium text-gray-900">{{ $reagen->reagen->nameReagen ?? 'Data Not Found' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider">Batch</p>
                            <p class="text-sm font-medium text-gray-900">{{ $reagen->batch ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 sm:justify-end">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                            +{{ number_format($reagen->quantity, 0, ',', '.') }}
                        </span>
                        <span class="text-xs text-gray-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $reagen->created_at->format('H:i') }}
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @empty
        <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No records found</h3>
            <p class="mt-1 text-sm text-gray-500">Incoming reagent movements will appear here once added.</p>
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