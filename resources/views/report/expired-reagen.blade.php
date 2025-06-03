@extends('layout.main')

@section('container')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Expired Reagen Report</h1>
                <div class="flex gap-2">
                    <a href="{{ route('report.expired-pdf') }}?{{ http_build_query(request()->all()) }}" 
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Generate PDF
                    </a>
                    <a href="{{ route('report.expired-excel') }}?{{ http_build_query(request()->all()) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>

            <form class="mb-6">
                <div class="max-w-xs">
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="expired_status" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="expired" {{ request('expired_status') === 'expired' ? 'selected' : '' }}>
                            Expired
                        </option>
                        <option value="near" {{ request('expired_status') === 'near' ? 'selected' : '' }}>
                            Near Expiry
                        </option>
                    </select>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Catalog</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reagen Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Expired Date</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Remaining</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reagents as $reagent)
                        @php
                            $expiredDate = \Carbon\Carbon::parse($reagent->expiredDate);
                            $isExpired = $expiredDate->isPast();
                            $remainingDays = now()->diffInDays($expiredDate, false);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reagent->noCatalog }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reagent->reagen->nameReagen }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reagent->batch }}</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-900">
                                {{ $expiredDate->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                @if($isExpired)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Near Expiry
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-center {{ $isExpired ? 'text-red-600' : 'text-yellow-600' }}">
                                {{ $remainingDays }} days
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
