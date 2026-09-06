@extends('layout.main')
@section('title', 'Manajemen Materi Training MSDS')

@section('container')
<style>
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap');

.msds-page { 
    font-family: 'Nunito', sans-serif; 
    background: #F8FAFC; 
    min-height: 100vh; 
    padding: 24px; 
}

.soft-table thead th {
    background: #F8FAFC;
    color: #64748B;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    border: none;
}

.soft-table tbody tr {
    border: none;
    transition: all 0.2s ease;
}

.soft-table tbody tr:hover {
    background: #F8FAFC;
}

.soft-table tbody td {
    border: none;
    vertical-align: middle;
    padding: 20px 24px;
}

.table-wrapper::-webkit-scrollbar { height: 6px; }
.table-wrapper::-webkit-scrollbar-track { background: transparent; }
.table-wrapper::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
</style>

<div class="msds-page">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">🎓 Manajemen Materi Training MSDS</h2>
            <p class="text-sm font-medium text-slate-500 mt-1">Kelola dan distribusikan materi pelatihan Material Safety Data Sheet (PDF/PPT) untuk setiap reagen.</p>
        </div>
    </div>

    {{-- Stats Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        @php
            $totalReagen = $reagens->total();
            $withMsds = $reagens->filter(fn($r) => $r->msds_documents_count > 0)->count();
            $withoutMsds = $totalReagen - $withMsds;
            $totalFiles = $reagens->sum('msds_documents_count');
        @endphp
        
        <div class="bg-white p-6 rounded-[24px] shadow-[0px_15px_35px_rgba(0,0,0,0.04)] flex items-center gap-4 hover:shadow-[0px_20px_45px_rgba(0,0,0,0.06)] transition-all">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center">
                <i class="bi bi-collection text-2xl text-indigo-500"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Reagen</p>
                <h3 class="text-2xl font-extrabold text-slate-800">{{ $totalReagen }}</h3>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-[24px] shadow-[0px_15px_35px_rgba(0,0,0,0.04)] flex items-center gap-4 hover:shadow-[0px_20px_45px_rgba(0,0,0,0.06)] transition-all">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center">
                <i class="bi bi-check2-circle text-2xl text-emerald-500"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sudah Ada Materi</p>
                <h3 class="text-2xl font-extrabold text-emerald-600">{{ $withMsds }}</h3>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-[24px] shadow-[0px_15px_35px_rgba(0,0,0,0.04)] flex items-center gap-4 hover:shadow-[0px_20px_45px_rgba(0,0,0,0.06)] transition-all">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center">
                <i class="bi bi-exclamation-triangle text-2xl text-amber-500"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Belum Ada Materi</p>
                <h3 class="text-2xl font-extrabold text-amber-600">{{ $withoutMsds }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[24px] shadow-[0px_15px_35px_rgba(0,0,0,0.04)] flex items-center gap-4 hover:shadow-[0px_20px_45px_rgba(0,0,0,0.06)] transition-all">
            <div class="w-14 h-14 rounded-2xl bg-sky-50 flex items-center justify-center">
                <i class="bi bi-files text-2xl text-sky-500"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total File Materi</p>
                <h3 class="text-2xl font-extrabold text-sky-600">{{ $totalFiles }}</h3>
            </div>
        </div>
    </div>

    {{-- Main List Card --}}
    <div class="bg-white rounded-[28px] shadow-[0px_15px_35px_rgba(0,0,0,0.04)] overflow-hidden">
        
        {{-- List Header --}}
        <div class="p-8 pb-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800">Daftar Reagen & Materi Training</h3>
                    <p class="text-xs font-semibold text-slate-400 mt-1">Status kelengkapan materi pelatihan untuk seluruh katalog reagen.</p>
                </div>
                <div class="relative w-full sm:w-72">
                    <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" placeholder="Cari nama reagen..." 
                           class="w-full bg-slate-50 border-none rounded-full py-3 pl-12 pr-5 text-sm font-semibold text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-indigo-50 focus:bg-white transition-all">
                </div>
            </div>
        </div>

        {{-- Table Wrapper --}}
        <div class="table-wrapper overflow-x-auto">
            <table class="soft-table w-full text-left">
                <thead>
                    <tr>
                        <th class="px-6 py-4 rounded-l-2xl">Informasi Reagen</th>
                        <th class="px-6 py-4">Status Training</th>
                        <th class="px-6 py-4">Total Materi</th>
                        <th class="px-6 py-4">Materi Terbaru</th>
                        <th class="px-6 py-4 rounded-r-2xl text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reagens as $reagen)
                    <tr class="group">
                        {{-- Kolom 1: Info Reagen --}}
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl {{ $reagen->msds_documents_count > 0 ? 'bg-emerald-50' : 'bg-slate-50' }} flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <i class="bi bi-droplet {{ $reagen->msds_documents_count > 0 ? 'text-emerald-500' : 'text-slate-400' }} text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-extrabold text-slate-800 leading-tight">{{ $reagen->nameReagen }}</p>
                                    <p class="text-xs font-mono text-slate-400 mt-1 tracking-wide">{{ $reagen->noCatalog }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Kolom 2: Status --}}
                        <td class="px-6 py-5">
                            @if($reagen->msds_documents_count > 0)
                                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[11px] font-extrabold bg-emerald-50 text-emerald-600 rounded-full uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Sudah Ada
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-[11px] font-extrabold bg-amber-50 text-amber-600 rounded-full uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Belum Ada
                                </span>
                            @endif
                        </td>

                        {{-- Kolom 3: Total Materi --}}
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-9 h-9 rounded-full {{ $reagen->msds_documents_count > 0 ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xs font-extrabold shadow-sm">
                                    {{ $reagen->msds_documents_count }}
                                </span>
                                <span class="text-sm font-bold text-slate-600">File</span>
                            </div>
                        </td>

                        {{-- Kolom 4: Materi Terbaru --}}
                        <td class="px-6 py-5">
                            @if($reagen->latest_msds)
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center flex-shrink-0">
                                        <i class="bi {{ $reagen->latest_msds->file_icon }} text-lg"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-700 truncate max-w-[220px]">{{ $reagen->latest_msds->title }}</p>
                                        <p class="text-[11px] text-slate-400 font-semibold mt-0.5">
                                            {{ $reagen->latest_msds->formatted_size }} • {{ strtoupper($reagen->latest_msds->file_type) }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-2 text-slate-400">
                                    <i class="bi bi-dash-lg"></i>
                                    <span class="text-sm font-medium italic">Belum ada materi</span>
                                </div>
                            @endif
                        </td>

                        {{-- Kolom 5: Aksi --}}
                        <td class="px-6 py-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Tombol Upload - Primary Button (VISIBLE) --}}
                                <a href="{{ route('msds.create', $reagen) }}" 
                                class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-extrabold text-white bg-indigo-600 rounded-full hover:bg-indigo-700 shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                                    <i class="bi bi-cloud-upload"></i> 
                                    <span>Upload</span>
                                </a>
                                
                                {{-- Tombol Kelola --}}
                                <a href="{{ route('msds.manage', $reagen) }}" 
                                   class="px-4 py-2.5 text-xs font-extrabold bg-slate-50 text-slate-700 rounded-full hover:bg-slate-100 transition-all shadow-sm flex items-center gap-1.5">
                                    <i class="bi bi-folder2-open"></i> 
                                    <span>Kelola</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-5">
                                    <i class="bi bi-inbox text-4xl text-slate-300"></i>
                                </div>
                                <h4 class="text-lg font-extrabold text-slate-700">Tidak ada data reagen</h4>
                                <p class="text-sm text-slate-400 mt-1 font-medium">Silakan tambahkan reagen terlebih dahulu di menu Stock Management.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($reagens->hasPages())
        <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100/50 flex items-center justify-between">
            <p class="text-xs font-bold text-slate-500">
                Menampilkan {{ $reagens->firstItem() }} - {{ $reagens->lastItem() }} dari {{ $reagens->total() }} reagen
            </p>
            <div class="flex items-center gap-1">
                @if ($reagens->onFirstPage())
                    <span class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 text-slate-300 cursor-not-allowed">
                        <i class="bi bi-chevron-left text-xs"></i>
                    </span>
                @else
                    <a href="{{ $reagens->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-full bg-white text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 shadow-sm transition-all">
                        <i class="bi bi-chevron-left text-xs"></i>
                    </a>
                @endif

                @foreach ($reagens->links()->elements[0] ?? [] as $page => $url)
                    @if ($page == $reagens->currentPage())
                        <span class="w-9 h-9 flex items-center justify-center rounded-full bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-100">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center rounded-full bg-white text-slate-600 hover:bg-slate-50 font-bold text-xs shadow-sm transition-all">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                @if ($reagens->hasMorePages())
                    <a href="{{ $reagens->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-full bg-white text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 shadow-sm transition-all">
                        <i class="bi bi-chevron-right text-xs"></i>
                    </a>
                @else
                    <span class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 text-slate-300 cursor-not-allowed">
                        <i class="bi bi-chevron-right text-xs"></i>
                    </span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection