@extends('layout.main')

@section('container')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">
                    Reagen In/Out & Stock Actual per Bulan
                </h1>

                <div class="flex flex-col sm:flex-row gap-2 sm:items-end">
                    <div class="flex gap-2">
                        <a href="{{ route('report.reagen-monthly-pdf', ['year' => $year]) }}"
                           target="_blank"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                            Generate PDF
                        </a>
                        <a href="{{ route('report.reagen-monthly-excel', ['year' => $year]) }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                            Export Excel
                        </a>
                    </div>

                    <form method="GET" action="{{ route('report.reagen-monthly') }}" class="flex gap-3 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                        <select name="year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach(range(date('Y')-2, date('Y')+1) as $y)
                                <option value="{{ $y }}" {{ (int)$year === (int)$y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                        Tampilkan
                    </button>
                </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                @php
                    $bulan = [
                        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
                        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
                    ];
                @endphp

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Catalog</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reagen</th>

                            @for($m = 1; $m <= 12; $m++)
                                <th colspan="3" class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                    {{ $bulan[$m] }}
                                </th>
                            @endfor
                        </tr>
                        <tr>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500 uppercase"></th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500 uppercase"></th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500 uppercase"></th>

                            @for($m = 1; $m <= 12; $m++)
                                <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase">In</th>
                                <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase">Out</th>
                                <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase">Stock</th>
                            @endfor
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($data as $idx => $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $row['reagen']->noCatalog ? ($row['reagen']->noCatalog) : '' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $row['reagen']->nameReagen }}</td>

                                @for($m = 1; $m <= 12; $m++)
                                    @php $cell = $row['months'][$m] ?? ['in' => 0, 'out' => 0, 'stock' => 0]; @endphp
                                    <td class="px-2 py-3 text-sm text-center text-gray-900" style="background:#8ed0a4;">{{ $cell['in'] }}</td>
                                    <td class="px-2 py-3 text-sm text-center text-gray-900" style="background:#ff9999;">{{ $cell['out'] }}</td>
                                    <td class="px-2 py-3 text-sm text-center text-gray-900">{{ $cell['stock'] }}</td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($data->isEmpty())
                <div class="mt-4 text-sm text-gray-600">
                    Tidak ada data untuk year {{ $year }}.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
