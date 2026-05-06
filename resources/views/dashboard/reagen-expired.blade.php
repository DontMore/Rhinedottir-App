@extends('layout.main')
@section('title', 'Expired Reagents')

@section('container')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Expired Reagents</h1>
            <p class="mt-1 text-sm text-gray-500">Daftar reagen yang telah kedaluwarsa atau mendekati masa kedaluwarsa.</p>
        </div>
        <div class="w-full md:w-80">
            <form action="{{ route('reagen.expired') }}" method="GET">
                <div class="relative">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search reagents..." 
                        class="w-full pl-10 pr-4 py-2 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Catalog No.</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Brand</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Expired Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($reagenExpired as $index => $reagen)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ($reagenExpired->currentPage() - 1) * $reagenExpired->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600">{{ $reagen->noCatalog }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $reagen->reagen ? $reagen->reagen->nameReagen : 'Unknown' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $reagen->reagen ? $reagen->reagen->merk : 'Unknown' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $reagen->quantity <= 5 ? 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20' : 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20' }}">
                                {{ $reagen->quantity }} unit
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($reagen->expiredDate)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $today = now();
                                $expDate = \Carbon\Carbon::parse($reagen->expiredDate);
                                $daysRemaining = $today->diffInDays($expDate, false);
                                
                                if ($daysRemaining < 0) {
                                    $statusLabel = 'Expired';
                                    $statusClass = 'bg-red-50 text-red-700 ring-red-600/20';
                                    $dotClass = 'bg-red-500';
                                } elseif ($daysRemaining <= 30) {
                                    $statusLabel = 'Near Expiry';
                                    $statusClass = 'bg-amber-50 text-amber-700 ring-amber-600/20';
                                    $dotClass = 'bg-amber-500';
                                } else {
                                    $statusLabel = 'Valid';
                                    $statusClass = 'bg-emerald-50 text-emerald-700 ring-emerald-600/20';
                                    $dotClass = 'bg-emerald-500';
                                }
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium ring-1 ring-inset {{ $statusClass }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $dotClass }}"></span>
                                {{ $statusLabel }}
                                <span class="text-[10px] opacity-70">({{ abs($daysRemaining) }} days {{ $daysRemaining < 0 ? 'ago' : 'left' }})</span>
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-sm font-medium text-gray-900">No reagents found</h3>
                                <p class="mt-1 text-xs text-gray-500">Tidak ada reagen yang terdeteksi kedaluwarsa atau mendekati masa aktifnya.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($reagenExpired->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $reagenExpired->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
