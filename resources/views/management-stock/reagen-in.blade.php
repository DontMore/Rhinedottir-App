@extends('layout.main')

@section('container')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-2 sm:space-y-0">
            <h1 class="text-xl font-semibold text-gray-900">Reagen Received History</h1>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm text-gray-500">
                    <li><a href="{{ route('dashboard.index') }}" class="hover:text-gray-700">Dashboard</a></li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">Reagen In</span>
                    </li>
                </ol>
            </nav>
        </div>
        
        <div class="px-6 py-6">
            <!-- Table Header -->
            <div class="grid grid-cols-12 gap-4 pb-2 mb-3 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                <div class="col-span-2">Catalog Number</div>
                <div class="col-span-3">Reagen Name</div>
                <div class="col-span-2">Batch</div>
                <div class="col-span-2">Quantity</div>
                <div class="col-span-3">Received Date</div>
            </div>

            <!-- Timeline -->
            <div class="relative pl-4 border-l-2 border-gray-200">
                @foreach ($paginatedData as $date => $items)
                <div class="mb-8 relative">
                    <!-- Date Badge -->
                    <div class="absolute -left-6 flex items-center justify-center">
                        <span class="px-3 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">
                            {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                        </span>
                    </div>

                    <!-- Items for this date -->
                    <div class="mt-6 space-y-3">
                        @foreach ($items as $reagen)
                            {{-- ✅ Route menggunakan guid, tapi fallback aman jika relasi null --}}
                            <a href="{{ route('data.view', ['guid' => $reagen->reagen_guid ?? $reagen->guid]) }}" 
                            class="block p-4 bg-white rounded-lg border border-gray-100 hover:bg-gray-50 transition duration-150">
                                <div class="grid grid-cols-12 gap-4 items-center">
                                    <div class="col-span-2 text-gray-600 text-sm">{{ $reagen->noCatalog }}</div>
                                    
                                    {{-- ✅ Tambahkan null coalescing untuk mencegah crash --}}
                                    <div class="col-span-3 font-medium text-gray-900">
                                        {{ $reagen->reagen->nameReagen ?? 'Master Data Not Found' }}
                                    </div>
                                    
                                    <div class="col-span-2 text-gray-600">{{ $reagen->batch }}</div>
                                    <div class="col-span-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            {{ $reagen->quantity }}
                                        </span>
                                    </div>
                                    <div class="col-span-3 text-sm text-gray-500 flex items-center">
                                        <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $reagen->created_at->format('d M Y H:i') }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-center">
                {{ $paginatedData->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
