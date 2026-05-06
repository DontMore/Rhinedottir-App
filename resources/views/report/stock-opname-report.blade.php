@extends('layout.main')

@section('container')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Stock Opname Report</h1>
                <div class="flex gap-2">
                    <a href="{{ route('report.stock-opname-pdf') }}?{{ http_build_query(request()->all()) }}" 
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Generate PDF
                    </a>
                    <a href="{{ route('report.stock-opname-excel') }}?{{ http_build_query(request()->all()) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>

            <form class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Month</label>
                    <select name="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Year</label>
                    <select name="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach(range(date('Y')-2, date('Y')) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Catalog</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reagen Name</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Previous</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">In</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Out</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Current</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actual</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($stocks as $stock)
                        @php
                            $expectedStock = ($stock->quantity + $stock->quantity_in) - $stock->quantity_out;
                            $difference = $stock->quantity_actual - $expectedStock;
                            $status = $difference === 0 ? 'Equal' : ($difference > 0 ? 'Excess' : 'Deficient');
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $stock->noCatalog }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $stock->reagen->nameReagen }}</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-900">{{ $stock->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-900">{{ $stock->quantity_in }}</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-900">{{ $stock->quantity_out }}</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-900">{{ $expectedStock }}</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-900">{{ $stock->quantity_actual }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $status }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $stock->catatan ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('select').forEach(select => {
    select.addEventListener('change', () => {
        const form = select.closest('form');
        const params = new URLSearchParams(new FormData(form));
        window.location.href = '{{ route("report.stock-opname") }}?' + params.toString();
    });
});
</script>
@endsection
