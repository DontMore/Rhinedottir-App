@extends('layout.main')

@section('container')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Historical Report</h1>
                <div class="flex gap-2">
                    <a href="{{ route('report.historical-pdf') }}?{{ http_build_query(request()->all()) }}"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Generate PDF
                    </a>
                    <a href="{{ route('report.historical-excel') }}?{{ http_build_query(request()->all()) }}"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>

            <form class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
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
                    <select name="reagen" id="reagen">
                        <option value="">All Reagents</option>
                        @foreach($reagens as $r)
                        {{-- ✅ Pastikan value adalah guid --}}
                        <option value="{{ $r->guid }}" {{ request('reagen') == $r->guid ? 'selected' : '' }}>
                            {{ $r->nameReagen }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reagen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recorded By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($histories as $history)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ isset($history->created_at) ? Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($history->transaction_type === 'in')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    In
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Out
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $history->reagen->nameReagen ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm {{ $history->transaction_type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $history->quantity ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $history->user_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $history->description ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('input[type="date"], select').forEach(input => {
        input.addEventListener('change', () => {
            const form = input.closest('form');
            const params = new URLSearchParams(new FormData(form));
            window.location.href = '{{ route("report.historical") }}?' + params.toString();
        });
    });
</script>
@endsection