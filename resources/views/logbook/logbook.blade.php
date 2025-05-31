@extends('layout.main')

@section('container')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <h1 class="text-xl font-semibold text-gray-900">Reagen Logbook</h1>
                <div class="w-full md:w-96">
                    <form action="{{ route('logbook.index') }}" method="GET">
                        <div class="relative">
                            <input type="text" 
                                   name="keyword" 
                                   placeholder="Search reagen..."
                                   value="{{ request('keyword') }}"
                                   class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-full text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 hover:bg-gray-100 rounded-full">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[5%]">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[20%]">Catalog</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[30%]">Name</th>
                            <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">Brand</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[20%]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reagens as $index => $reagen)
                            <tr class="hover:bg-gray-50">
                                <td class="hidden lg:table-cell px-6 py-4 text-sm text-gray-900">{{ $reagens->firstItem() + $index }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $reagen->noCatalog }}</div>
                                    <div class="lg:hidden text-sm text-gray-500">{{ $reagen->merk }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $reagen->nameReagen }}</td>
                                <td class="hidden lg:table-cell px-6 py-4 text-sm text-gray-500">{{ $reagen->merk }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $reagen->stockReagen && $reagen->stockReagen->quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $reagen->stockReagen ? $reagen->stockReagen->quantity : '0' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <a href="{{ route('data.history', ['noCatalog' => $reagen->noCatalog]) }}" 
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg border border-blue-600 text-blue-600 hover:bg-blue-50">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            History
                                        </a>
                                        @auth
                                        <a href="{{ route('data.take', ['noCatalog' => $reagen->noCatalog]) }}" 
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                            Take
                                        </a>
                                        @endauth
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-center">
                @if ($reagens->hasPages())
                    <nav aria-label="Page navigation">
                        <ul class="inline-flex -space-x-px text-sm">
                            {{-- Previous Page Link --}}
                            @if ($reagens->onFirstPage())
                                <li>
                                    <span class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-400 bg-gray-100 border border-e-0 border-gray-300 rounded-s-lg cursor-not-allowed">Previous</span>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $reagens->previousPageUrl() }}" class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($reagens->links()->elements[0] as $page => $url)
                                @if ($page == $reagens->currentPage())
                                    <li>
                                        <span aria-current="page" class="flex items-center justify-center px-3 h-8 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700">{{ $page }}</span>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ $url }}" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($reagens->hasMorePages())
                                <li>
                                    <a href="{{ $reagens->nextPageUrl() }}" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
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
