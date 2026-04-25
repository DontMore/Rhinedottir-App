@extends('layout.main')
@section('container')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Logbook Report</h1>
            <p class="text-sm text-gray-500 mt-1">Filter and export reagent transaction history.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('report.logbook-pdf', request()->all()) }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <i class="bi bi-file-earmark-pdf"></i> Generate PDF
            </a>
            <a href="{{ route('report.logbook-excel', request()->all()) }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
        </div>
    </div>

    {{-- Filter Form dengan ID --}}
    <form id="reportFilterForm" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700">Start Date</label>
            <input type="date" name="start_date"
                value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">End Date</label>
            <input type="date" name="end_date"
                value="{{ request('end_date', now()->format('Y-m-d')) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Reagen</label>
            <select name="reagen" id="reagenSelect"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Reagens</option>
                @forelse($reagens as $reagen)
                <option value="{{ $reagen->noCatalog }}"
                    {{ request('reagen') == $reagen->noCatalog ? 'selected' : '' }}>
                    {{ $reagen->nameReagen }} ({{ $reagen->noCatalog }})
                </option>
                @empty
                <option disabled>No reagens available</option>
                @endforelse
            </select>
        </div>
    </form>

    {{-- Table Section --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reagen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity Taken</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logbooks as $logbook)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $logbook->created_at?->format('d/m/Y H:i') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $logbook->reagen?->nameReagen ?? 'Unknown' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $logbook->quantity_taken ?? 0 }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $logbook->user?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $logbook->notes ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                            📭 No logbook entries found for the selected filters.
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