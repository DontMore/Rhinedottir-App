@extends('layout.main')
@section('title', 'Kelola Materi Training - ' . $reagen->nameReagen)

@section('container')
<div class="msds-page">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <a href="{{ route('msds.index') }}" class="text-xs font-bold text-indigo-500 hover:text-indigo-700 mb-2 inline-flex items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Reagen
            </a>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">📂 Materi Training: {{ $reagen->nameReagen }}</h2>
            <p class="text-sm font-medium text-slate-500 mt-1 font-mono">{{ $reagen->noCatalog }} • Total {{ $documents->count() }} File</p>
        </div>
        <a href="{{ route('msds.create', $reagen) }}" class="px-6 py-3 text-sm font-extrabold bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-full shadow-lg shadow-indigo-100 hover:shadow-indigo-200 transition-all">
            <i class="bi bi-cloud-upload mr-2"></i> Upload Materi Baru
        </a>
    </div>

    {{-- List Materi --}}
    <div class="bg-white rounded-[28px] shadow-[0px_15px_35px_rgba(0,0,0,0.04)] p-8">
        @if($documents->isEmpty())
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5">
                    <i class="bi bi-folder2-open text-4xl text-slate-300"></i>
                </div>
                <h4 class="text-lg font-extrabold text-slate-700">Belum ada materi training</h4>
                <p class="text-sm text-slate-400 mt-1">Klik tombol "Upload Materi Baru" untuk menambahkan file PDF/PPT.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($documents as $doc)
                <div class="bg-slate-50/50 p-6 rounded-[24px] border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center">
                            <i class="bi {{ $doc->file_icon }} text-2xl"></i>
                        </div>
                        <form action="{{ route('msds.destroy', $doc) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-full bg-white text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all shadow-sm opacity-0 group-hover:opacity-100">
                                <i class="bi bi-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                    
                    <h3 class="text-sm font-extrabold text-slate-800 truncate mb-1" title="{{ $doc->title }}">{{ $doc->title }}</h3>
                    <p class="text-xs font-semibold text-slate-400 mb-4">
                        {{ $doc->formatted_size }} • {{ strtoupper($doc->file_type) }} • {{ $doc->created_at->diffForHumans() }}
                    </p>
                    
                    <a href="{{ route('msds.show', $doc) }}" target="_blank" 
                       class="block w-full text-center px-4 py-2.5 text-xs font-extrabold bg-white text-indigo-600 rounded-full hover:bg-indigo-50 transition-all shadow-sm">
                        <i class="bi bi-eye mr-1"></i> Lihat / Download
                    </a>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection