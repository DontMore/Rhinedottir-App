@extends('layout.main')
@section('title', 'Manajemen Dokumen MSDS')

@section('container')
<style>
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap');
.msds-page { font-family: 'Nunito', sans-serif; background: #F8FAFC; min-height: 100vh; padding: 24px; }
.msds-card { background: #fff; border-radius: 28px; box-shadow: 0px 15px 35px rgba(0,0,0,0.04); padding: 28px; transition: all 0.3s; }
.msds-card:hover { box-shadow: 0px 20px 45px rgba(0,0,0,0.06); transform: translateY(-2px); }
.msds-pill-btn { border-radius: 50px; padding: 10px 20px; font-weight: 700; font-size: 0.8rem; transition: all 0.3s; }
</style>

<div class="msds-page">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">📂 Manajemen MSDS</h2>
            <p class="text-sm font-medium text-slate-500 mt-1">Upload dan kelola dokumen Material Safety Data Sheet (PDF/PPT) untuk setiap reagen.</p>
        </div>
    </div>

    {{-- Stats Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @php
            $totalReagen = $reagens->total();
            $withMsds = $reagens->filter(fn($r) => $r->msds_documents_count > 0)->count();
            $withoutMsds = $totalReagen - $withMsds;
        @endphp
        <div class="msds-card flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center">
                <i class="bi bi-collection text-2xl text-indigo-500"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Reagen</p>
                <h3 class="text-2xl font-extrabold text-slate-800">{{ $totalReagen }}</h3>
            </div>
        </div>
        <div class="msds-card flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center">
                <i class="bi bi-check2-circle text-2xl text-emerald-500"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sudah Upload</p>
                <h3 class="text-2xl font-extrabold text-emerald-600">{{ $withMsds }}</h3>
            </div>
        </div>
        <div class="msds-card flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center">
                <i class="bi bi-exclamation-triangle text-2xl text-amber-500"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Belum Upload</p>
                <h3 class="text-2xl font-extrabold text-amber-600">{{ $withoutMsds }}</h3>
            </div>
        </div>
    </div>

    {{-- Grid Daftar Reagen --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($reagens as $reagen)
        <div class="msds-card flex flex-col">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl {{ $reagen->msds_documents_count > 0 ? 'bg-emerald-50' : 'bg-slate-50' }} flex items-center justify-center">
                    <i class="bi {{ $reagen->msds_documents_count > 0 ? 'bi-file-earmark-check text-emerald-500' : 'bi-file-earmark-x text-slate-400' }} text-xl"></i>
                </div>
                @if($reagen->msds_documents_count > 0)
                    <span class="px-3 py-1 text-[10px] font-extrabold bg-emerald-50 text-emerald-600 rounded-full uppercase tracking-wider">
                        ✓ Uploaded
                    </span>
                @else
                    <span class="px-3 py-1 text-[10px] font-extrabold bg-amber-50 text-amber-600 rounded-full uppercase tracking-wider">
                        Pending
                    </span>
                @endif
            </div>

            <h3 class="text-base font-extrabold text-slate-800 mb-1 line-clamp-2">{{ $reagen->nameReagen }}</h3>
            <p class="text-xs font-mono text-slate-400 mb-4">{{ $reagen->noCatalog }}</p>

            @if($reagen->latest_msds)
                <div class="flex items-center gap-2 p-3 bg-slate-50 rounded-2xl mb-4">
                    <i class="bi {{ $reagen->latest_msds->file_icon }} text-lg"></i>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-700 truncate">{{ $reagen->latest_msds->title }}</p>
                        <p class="text-[10px] text-slate-400">{{ $reagen->latest_msds->formatted_size }}</p>
                    </div>
                </div>
            @endif

            <div class="mt-auto flex gap-2">
                @if($reagen->msds_documents_count > 0)
                    <a href="{{ route('msds.show', $reagen) }}" target="_blank"
                       class="flex-1 msds-pill-btn bg-slate-50 text-slate-700 hover:bg-slate-100 text-center">
                        <i class="bi bi-eye mr-1"></i> View
                    </a>
                    <form action="{{ route('msds.destroy', $reagen) }}" method="POST" class="flex-1" 
                          onsubmit="return confirm('Hapus dokumen MSDS ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full msds-pill-btn bg-red-50 text-red-600 hover:bg-red-100">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                @endif
                <a href="{{ route('msds.create', $reagen) }}" 
                   class="flex-1 msds-pill-btn bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-lg shadow-indigo-100 hover:shadow-indigo-200 text-center">
                    <i class="bi bi-cloud-upload mr-1"></i> {{ $reagen->msds_documents_count > 0 ? 'Re-upload' : 'Upload' }}
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full msds-card text-center py-16">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="bi bi-inbox text-4xl text-slate-300"></i>
            </div>
            <p class="text-slate-500 font-semibold">Belum ada data reagen.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">{{ $reagens->links() }}</div>
</div>
@endsection