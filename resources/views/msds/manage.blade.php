@extends('layout.main')
@section('title', 'Kelola Materi Training - ' . $reagen->nameReagen)

@section('container')
<style>
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap');

.manage-page { 
    font-family: 'Nunito', sans-serif; 
    background: #F8FAFC; 
    min-height: 100vh; 
    padding: 24px; 
}

/* Soft UI List Item Hover Effect */
.list-item {
    transition: all 0.2s ease;
}
.list-item:hover {
    background-color: #F8FAFC;
}
</style>

<div class="manage-page">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <a href="{{ route('msds.index') }}" class="text-xs font-bold text-indigo-500 hover:text-indigo-700 mb-2 inline-flex items-center gap-1 transition-colors">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Reagen
            </a>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">📂 Materi Training: {{ $reagen->nameReagen }}</h2>
            <p class="text-sm font-medium text-slate-500 mt-1 font-mono">{{ $reagen->noCatalog }} • Total {{ $documents->count() }} File</p>
        </div>
        <a href="{{ route('msds.create', $reagen) }}" class="inline-flex items-center px-6 py-3 text-sm font-extrabold bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-full shadow-lg shadow-indigo-100 hover:shadow-indigo-200 hover:-translate-y-0.5 transition-all">
            <i class="bi bi-cloud-upload mr-2"></i> Upload Materi Baru
        </a>
    </div>

    {{-- Main List Card --}}
    <div class="bg-white rounded-[28px] shadow-[0px_15px_35px_rgba(0,0,0,0.04)] overflow-hidden">
        
        @if($documents->isEmpty())
            {{-- Empty State --}}
            <div class="text-center py-20 px-6">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5">
                    <i class="bi bi-folder2-open text-4xl text-slate-300"></i>
                </div>
                <h4 class="text-lg font-extrabold text-slate-700">Belum ada materi training</h4>
                <p class="text-sm text-slate-400 mt-1 max-w-md mx-auto">Klik tombol "Upload Materi Baru" di atas untuk menambahkan file PDF atau PPT untuk reagen ini.</p>
            </div>
        @else
            {{-- List Header (Hidden on mobile) --}}
            <div class="hidden md:flex items-center px-8 py-4 bg-slate-50/50">
                <div class="md:w-2/5 text-[11px] font-extrabold text-slate-400 uppercase tracking-widest">Informasi File</div>
                <div class="md:w-2/5 text-[11px] font-extrabold text-slate-400 uppercase tracking-widest px-4">Trainer</div>
                <div class="md:w-1/5 text-[11px] font-extrabold text-slate-400 uppercase tracking-widest text-right">Aksi</div>
            </div>

            {{-- List Items --}}
            <div class="divide-y divide-slate-100/50">
                @foreach($documents as $doc)
                <div class="list-item flex flex-col md:flex-row md:items-center p-6 md:px-8">
                    
                    {{-- Column 1: File Info --}}
                    <div class="md:w-2/5 flex items-center gap-4 mb-4 md:mb-0">
                        <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center flex-shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="bi {{ $doc->file_icon }} text-2xl"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-extrabold text-slate-800 truncate max-w-[250px]" title="{{ $doc->title }}">{{ $doc->title }}</h3>
                            <p class="text-xs font-semibold text-slate-400 mt-1">
                                {{ $doc->formatted_size }} • {{ strtoupper($doc->file_type) }} • {{ $doc->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    {{-- Column 2: Trainers --}}
                    <div class="md:w-2/5 mb-4 md:mb-0 px-0 md:px-6">
                        @if($doc->trainers && count($doc->trainers) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($doc->trainers as $trainer)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-full text-[11px] font-bold">
                                    <i class="bi bi-person-fill text-indigo-500"></i>
                                    {{ $trainer }}
                                </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-xs font-medium text-slate-400 italic flex items-center gap-1.5">
                                <i class="bi bi-dash"></i> Tidak ada trainer
                            </span>
                        @endif
                    </div>

                    {{-- Column 3: Actions --}}
                    <div class="md:w-1/5 flex items-center justify-start md:justify-end gap-2">
                        <a href="{{ route('msds.show', $doc) }}" target="_blank" 
                           class="inline-flex items-center px-4 py-2.5 text-xs font-extrabold bg-slate-50 text-slate-700 rounded-full hover:bg-indigo-50 hover:text-indigo-600 transition-all shadow-sm">
                            <i class="bi bi-eye mr-1.5"></i> Lihat
                        </a>
                        <form action="{{ route('msds.destroy', $doc) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2.5 text-xs font-extrabold bg-slate-50 text-slate-700 rounded-full hover:bg-red-50 hover:text-red-600 transition-all shadow-sm">
                                <i class="bi bi-trash mr-1.5"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection