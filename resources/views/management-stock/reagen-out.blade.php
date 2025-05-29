@extends('layout.main')

@section('container')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-2 sm:space-y-0">
            <h1 class="text-xl font-semibold text-gray-900">Reagen Usage History</h1>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm text-gray-500">
                    <li><a href="{{ route('dashboard.index') }}" class="hover:text-gray-700">Dashboard</a></li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">Reagen Out</span>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="px-6 py-6">
            <!-- Table Header -->
            <div class="grid grid-cols-12 gap-4 pb-2 mb-3 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                <div class="col-span-3">Reagen Name</div>
                <div class="col-span-2">Catalog Number</div>
                <div class="col-span-2">Batch</div>
                <div class="col-span-2">Quantity</div>
                <div class="col-span-3">Analyst</div>
            </div>

            <!-- Timeline -->
            <div class="relative pl-4 border-l-2 border-gray-200">
                @foreach ($paginatedData as $date => $items)
                <div class="mb-8 relative">
                    <!-- Date Badge -->
                    <div class="absolute -left-6 flex items-center justify-center">
                        <span class="px-3 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full">
                            {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                        </span>
                    </div>

                    <!-- Items for this date -->
                    <div class="mt-6 space-y-3">
                        @foreach ($items as $reagen)
                        <a href="{{ route('data.history', ['noCatalog' => $reagen->noCatalog]) }}" 
                           class="block p-4 bg-white rounded-lg border border-gray-100 hover:bg-gray-50 transition duration-150">
                            <div class="grid grid-cols-12 gap-4 items-center">
                                <div class="col-span-3 font-medium text-gray-900">{{ $reagen->reagen->nameReagen }}</div>
                                <div class="col-span-2 text-gray-600 text-sm">{{ $reagen->noCatalog }}</div>
                                <div class="col-span-2 text-gray-600">{{ $reagen->batch }}</div>
                                <div class="col-span-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                        {{ $reagen->quantity_taken }}
                                    </span>
                                </div>
                                <div class="col-span-3 text-sm text-gray-500 flex items-center">
                                    <svg class="mr-1.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $reagen->user->name }}
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
                @if ($paginatedData->hasPages())
                    <nav aria-label="Page navigation">
                        <ul class="inline-flex -space-x-px text-sm">
                            {{-- Previous Page Link --}}
                            @if ($paginatedData->onFirstPage())
                                <li>
                                    <span class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-400 bg-gray-100 border border-e-0 border-gray-300 rounded-s-lg cursor-not-allowed">Previous</span>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $paginatedData->previousPageUrl() }}" class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($paginatedData->links()->elements[0] as $page => $url)
                                <li>
                                    @if ($page == $paginatedData->currentPage())
                                        <span aria-current="page" class="flex items-center justify-center px-3 h-8 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $page }}</a>
                                    @endif
                                </li>
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($paginatedData->hasMorePages())
                                <li>
                                    <a href="{{ $paginatedData->nextPageUrl() }}" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                                </li>
                            @else
                                <li>
                                    <span class="flex items-center justify-center px-3 h-8 leading-tight text-gray-400 bg-gray-100 border border-gray-300 rounded-e-lg cursor-not-allowed">Next</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
