@extends('layout.main')

@section('container')
@php
    $hazardImageMap = [
        'Flammable' => 'flammable.png',
        'Corrosive' => 'corrosive.png',
        'Toxic' => 'toxic.png',
        'Explosive' => 'explosive.png',
        'Oxidising' => 'oxidising.png',
        'Environment' => 'Environmental-Hazard.png',
        'Irritant' => 'irritant.png',
        'Carcinogen' => 'carcinogen.png',
    ];
@endphp
<div class="space-y-6">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Annual Reagen Usage Report</h1>
                <p class="text-sm text-gray-500 mt-1">Summary of all reagents with signal word, hazard symbols, and total usage in {{ $year }}</p>
            </div>
            <a href="{{ route('report.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h12" />
                </svg>
                Back to Reports
            </a>
        </div>

        <div class="px-6 py-4 border-b border-gray-100 bg-white">
            <form method="GET" action="{{ route('report.reagen-annual-usage') }}" class="flex flex-col xl:flex-row xl:items-end gap-3">
                <div>
                    <label for="signal_word" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Signal Word</label>
                    <select name="signal_word" id="signal_word" class="block rounded-lg border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All</option>
                        <option value="Danger" {{ $selectedSignalWord === 'Danger' ? 'selected' : '' }}>Danger</option>
                        <option value="Warning" {{ $selectedSignalWord === 'Warning' ? 'selected' : '' }}>Warning</option>
                        <option value="None" {{ $selectedSignalWord === 'None' ? 'selected' : '' }}>None</option>
                    </select>
                </div>
                <div>
                    <label for="sort_by" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Sort By</label>
                    <select name="sort_by" id="sort_by" class="block rounded-lg border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                        <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="usage" {{ $sortBy === 'usage' ? 'selected' : '' }}>Usage</option>
                    </select>
                </div>
                <div>
                    <label for="direction" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Direction</label>
                    <select name="direction" id="direction" class="block rounded-lg border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                        <option value="asc" {{ $direction === 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option value="desc" {{ $direction === 'desc' ? 'selected' : '' }}>Descending</option>
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors">
                    Apply Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Catalog</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reagent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Signal Word</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hazard Symbol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MSDS</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Usage {{ $year }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($report as $item)
                        @php
                            $signal = $item['signal_word'] ?? 'None';
                            $signalClass = match ($signal) {
                                'Danger' => 'bg-red-50 text-red-700 ring-red-600/20',
                                'Warning' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                                'None' => 'bg-gray-100 text-gray-700 ring-gray-600/20',
                                default => 'bg-gray-100 text-gray-700 ring-gray-600/20',
                            };
                            $hazards = $item['hazards'] ?? [];
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item['reagen']->noCatalog ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm">
                                <div class="font-medium text-gray-900">{{ $item['reagen']->nameReagen ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $item['reagen']->merk ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $signalClass }}">
                                    {{ $signal }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if(count($hazards))
                                    <div class="flex flex-wrap items-center gap-2">
                                        @foreach($hazards as $hazard)
                                            @php
                                                $hazardLabel = trim($hazard);
                                                $hazardImage = $hazardImageMap[$hazardLabel] ?? null;
                                            @endphp
                                            <div class="flex items-center gap-1.5 rounded-md border border-gray-200 bg-gray-50 px-2 py-1">
                                                @if($hazardImage)
                                                    <img src="{{ asset('images/' . $hazardImage) }}" alt="{{ $hazardLabel }}" class="w-6 h-6 object-contain" onerror="this.style.display='none'">
                                                @endif
                                                <span class="text-[10px] font-medium text-gray-700">{{ $hazardLabel }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if(!empty($item['reagen']->msds))
                                    <a href="{{ route('management-stock.msds', ['guid' => $item['reagen']->guid]) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors">
                                        <svg class="-ml-0.5 mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Preview
                                    </a>
                                @else
                                    <span class="text-sm text-gray-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                {{ number_format($item['usage_total'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                No reagent data available for this filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
