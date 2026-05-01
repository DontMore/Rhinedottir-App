@extends('layout.main')
@section('title', 'Logbook Report')

@section('container')
<div class="space-y-6">
    <!-- Header & Export Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Logbook Report</h2>
            <p class="text-sm text-gray-500 mt-1">Filter and export reagent transaction history.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('report.logbook-pdf', request()->all()) }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Generate PDF
            </a>
            <a href="{{ route('report.logbook-excel', request()->all()) }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
        </div>
    </div>

    <!-- Filter Form Card -->
    <form id="reportFilterForm" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label for="start_date" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}"
                    class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
            </div>
            <div>
                <label for="end_date" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">End Date</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}"
                    class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
            </div>
            <div>
                <label for="reagenSelect" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1.5">Reagent</label>
                <select id="reagenSelect" name="reagen"
                    class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    <option value="">All Reagents</option>
                    @forelse($reagens as $reagen)
                        <option value="{{ $reagen->noCatalog }}" {{ request('reagen') == $reagen->noCatalog ? 'selected' : '' }}>
                            {{ $reagen->nameReagen }} ({{ $reagen->noCatalog }})
                        </option>
                    @empty
                        <option disabled>No reagents available</option>
                    @endforelse
                </select>
            </div>
        </div>
    </form>

    <!-- Data Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reagent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Taken</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($logbooks as $logbook)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $logbook->created_at?->format('d M Y, H:i') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                            {{ $logbook->reagen?->nameReagen ?? 'Unknown' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                {{ $logbook->quantity_taken ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $logbook->user?->name ?? 'System' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 max-w-xs truncate" title="{{ $logbook->notes }}">
                            {{ $logbook->notes ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No records found</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or check back later.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reportFilterForm');
    const inputs = form.querySelectorAll('input[type="date"], select');

    inputs.forEach(input => {
        input.addEventListener('change', function() {
            const params = new URLSearchParams(new FormData(form));
            // Hapus parameter kosong agar URL lebih bersih
            for (let [key, value] of params.entries()) {
                if (!value) params.delete(key);
            }
            window.location.href = '{{ route("reportDetail") }}?' + params.toString();
        });
    });
});
</script>
@endpush
@endsection