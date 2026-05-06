@extends('layout.main')
@section('title', 'Backup & Data')

@section('container')
<div class="flex flex-col md:flex-row gap-8">
    {{-- Navigation Sidebar --}}
    @include('settings.partials.navigation')

    {{-- Main Content --}}
    <div class="flex-1 space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Backup & Export</h2>
            <p class="text-sm text-gray-500 mt-1">Download your data for offline use or as a backup. Data is exported in CSV format.</p>
        </div>

        @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-100 rounded-xl flex items-center gap-3 text-red-700 text-sm">
            <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Full Backup Card -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
                <div class="p-6 flex-grow">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Full Backup</h3>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                        Mendownload seluruh data reagen, logbook, stok, dan riwayat dalam satu file ZIP (Kumpulan CSV).
                    </p>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <form action="{{ route('settings.backup.download') }}" method="POST">
                        @csrf
                        <input type="hidden" name="table" value="all">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
                            Download Full Backup (.zip)
                        </button>
                    </form>
                </div>
            </div>

            <!-- Single Table Export -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
                <div class="p-6 flex-grow">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Specific Table Export</h3>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                        Pilih tabel tertentu yang ingin Anda ekspor ke format CSV.
                    </p>
                    
                    <form action="{{ route('settings.backup.download') }}" method="POST" id="single-table-form" class="mt-4">
                        @csrf
                        <select name="table" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                            <option value="reagens">Master Reagens</option>
                            <option value="reagen_ins">Reagen In (Purchases)</option>
                            <option value="logbook_reagens">Logbook Usage</option>
                            <option value="stock_reagens">Current Stock</option>
                            <option value="stock_histories">Inventory History</option>
                        </select>
                    </form>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('single-table-form').submit()" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-colors shadow-sm">
                        Export Selected Table (.csv)
                    </button>
                </div>
            </div>
        </div>

        <!-- Security Warning -->
        <div class="bg-amber-50 rounded-2xl border border-amber-100 p-6 flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-amber-500 shadow-sm border border-amber-50 flex-shrink-0">
                <i class="fas fa-shield-alt text-lg"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-amber-900">Security Recommendation</h4>
                <p class="text-xs text-amber-800 leading-relaxed mt-1">
                    Selalu simpan file backup Anda di lokasi yang aman dan terenkripsi. Data yang diekspor berisi informasi sensitif mengenai inventaris dan aktivitas laboratorium Anda.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
