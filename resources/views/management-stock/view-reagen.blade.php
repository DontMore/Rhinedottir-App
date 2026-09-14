@extends('layout.main')
@section('title', 'Reagent Details')
@section('container')
<div class="space-y-6">
    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Reagent Details</h2>
            <p class="text-sm text-gray-500 mt-1">View specifications, hazard information, and stock movement history.</p>
        </div>
        <div class="flex gap-3">
            <!-- ✅ BARU: Toggle Status Button -->
            <form action="{{ route('reagen.toggle-status', $data->guid) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors shadow-sm
                        {{ $data->is_active 
                            ? 'text-orange-700 bg-orange-50 hover:bg-orange-100 border border-orange-300' 
                            : 'text-green-700 bg-green-50 hover:bg-green-100 border border-green-300' }}"
                        onclick="return confirm('{{ $data->is_active ? 'Non-aktifkan' : 'Aktifkan' }} reagen ini?')">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($data->is_active)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @endif
                    </svg>
                    {{ $data->is_active ? 'Non-aktifkan' : 'Aktifkan' }}
                </button>
            </form>

            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </a>
            <a href="{{ route('data.edit', ['guid' => $data->guid]) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Data
            </a>
        </div>
    </div>

    <!-- ✅ BARU: Alert Notifikasi Review MSDS Annual -->
    @php
        $latestMsdsDoc = isset($reagenMsdsDocuments) ? $reagenMsdsDocuments->where('is_latest', true)->first() : null;
        $msdsNeedsReview = $latestMsdsDoc ? $latestMsdsDoc->needsReview() : false;
    @endphp

    @if($msdsNeedsReview)
    <div class="bg-amber-50 border-2 border-amber-300 rounded-xl p-4 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-start sm:items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-amber-900">⚠️ Peringatan Review Tahunan Dokumen MSDS</h4>
                <p class="text-xs text-amber-700 mt-0.5">
                    Dokumen MSDS {{ $latestMsdsDoc->version ? 'Versi (' . $latestMsdsDoc->version . ')' : '' }} sudah lebih dari 1 tahun sejak revisi terakhir ({{ $latestMsdsDoc->revision_date ? $latestMsdsDoc->revision_date->format('d M Y') : '-' }}). Mohon lakukan upload versi baru atau konfirmasi review di halaman Edit.
                </p>
            </div>
        </div>
        <a href="{{ route('data.edit', ['guid' => $data->guid]) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition-colors flex-shrink-0 shadow-sm">
            Review / Update MSDS →
        </a>
    </div>
    @endif

    <!-- Details & Hazards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Information Card -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-800">General Information</h3>
                    <!-- ✅ BARU: Status Badge -->
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                        {{ $data->is_active 
                            ? 'bg-green-100 text-green-800 ring-1 ring-green-600/20' 
                            : 'bg-gray-100 text-gray-600 ring-1 ring-gray-500/20' }}">
                        <span class="w-2 h-2 rounded-full mr-2 {{ $data->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                        {{ $data->is_active ? 'Active' : 'Inactive' }}
                    </span>
            </div>
            <div class="divide-y divide-gray-100">
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Catalog Number</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">{{ $data->noCatalog }}</span>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Reagent Name</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">{{ $data->nameReagen }}</span>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Brand</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">{{ $data->merk }}</span>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Pack Size</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">{{ $data->packSize }}</span>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Buffer Stock</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">
                        {{ isset($data->buffer_stock) ? number_format((float) $data->buffer_stock, 0, ',', '.') : '-' }}
                    </span>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Group</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">{{ $data->group->name ?? '-' }}</span>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Category</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">{{ $data->category->name ?? '-' }}</span>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Price</span>
                    <span class="sm:col-span-2 text-sm font-medium text-gray-900">Rp {{ number_format((float)$data->price, 0, ',', '.') }}</span>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Storage Location</span>
                    <span class="sm:col-span-2">
                        @if($data->storageLocation)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 ring-1 ring-green-600/20">
                            {{ $data->storageLocation->code }} - {{ $data->storageLocation->name }}
                        </span>
                        @else
                        <span class="text-sm text-gray-400 italic">Not assigned</span>
                        @endif
                    </span>
                </div>
                @if($data->recommended_storage_location)
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <span class="text-sm text-gray-500">Recommended Storage</span>
                    <span class="sm:col-span-2 text-sm text-blue-700 font-medium">
                        {{ $data->recommended_storage_location }}
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- ✅ DIUBAH: Hazard & Dangerous Card (Signal Word + MSDS List + Hazard Symbols) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-800">Hazard & Dangerous</h3>
            </div>
            <div class="p-6 space-y-6">

                <!-- Row 1: Signal Word -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        GHS Signal Word
                    </h4>
                    @if($data->signal_word)
                        @php
                            $color = match($data->signal_word) {
                                'Danger' => 'bg-red-50 text-red-700 ring-red-600/20',
                                'Warning' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                                'None' => 'bg-gray-100 text-gray-700 ring-gray-600/20',
                                default => 'bg-gray-100 text-gray-700 ring-gray-600/20',
                            };
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold ring-1 ring-inset {{ $color }}">
                            @if($data->signal_word == 'Danger')
                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            @elseif($data->signal_word == 'Warning')
                                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            @endif
                            {{ $data->signal_word }}
                        </span>
                    @else
                        <span class="text-sm text-gray-400 italic">Not specified</span>
                    @endif
                </div>

                <!-- Row 2: MSDS Documents List (Multi-Versi) dengan Review Status -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                        </svg>
                        MSDS Documents
                        @if(isset($reagenMsdsDocuments) && $reagenMsdsDocuments->count() > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                {{ $reagenMsdsDocuments->count() }} version(s)
                            </span>
                        @endif
                    </h4>

                    @if(isset($reagenMsdsDocuments) && $reagenMsdsDocuments->count() > 0)
                        <div class="space-y-2">
                            @foreach($reagenMsdsDocuments as $doc)
                                @php
                                    $needsReview = $doc->needsReview();
                                    $yearsSince = $doc->getYearsSinceRevision();
                                @endphp
                                <div class="flex items-center justify-between p-3 rounded-lg {{ 
                                    $doc->is_latest 
                                        ? ($needsReview ? 'bg-amber-50 border-2 border-amber-300' : 'bg-emerald-50 border border-emerald-200') 
                                        : 'bg-gray-50 border border-gray-100' 
                                }} hover:shadow-sm transition-all">
                                    <div class="flex items-center gap-2 flex-1 min-w-0">
                                        <!-- Icon dengan indikator review -->
                                        <div class="relative">
                                            <div class="w-10 h-10 rounded-lg {{ 
                                                $doc->is_latest 
                                                    ? ($needsReview ? 'bg-amber-100' : 'bg-emerald-100') 
                                                    : 'bg-red-100' 
                                            }} flex items-center justify-center flex-shrink-0">
                                                @if($needsReview && $doc->is_latest)
                                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5 {{ $doc->is_latest ? 'text-emerald-600' : 'text-red-500' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            @if($needsReview && $doc->is_latest)
                                                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="text-xs font-medium text-gray-800 truncate">{{ $doc->file_name }}</p>
                                                @if($doc->is_latest)
                                                    @if($needsReview)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-500 text-white rounded-full text-[10px] font-bold">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/>
                                                            </svg>
                                                            NEEDS REVIEW
                                                        </span>
                                                    @else
                                                        <span class="inline-block px-1.5 py-0.5 bg-emerald-500 text-white rounded-full text-[10px] font-bold">LATEST</span>
                                                    @endif
                                                @endif
                                            </div>
                                            <p class="text-[10px] text-gray-500 mt-0.5 flex flex-wrap gap-x-2 gap-y-0.5">
                                                @if($doc->version)
                                                    <span class="inline-block px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded font-bold">{{ $doc->version }}</span>
                                                @endif
                                                @if($doc->revision_date)
                                                    <span>📅 {{ $doc->revision_date->format('d M Y') }}</span>
                                                    @if($needsReview)
                                                        <span class="text-amber-600 font-semibold">({{ $yearsSince }} years ago)</span>
                                                    @endif
                                                @endif
                                            </p>
                                            @if($needsReview && $doc->is_latest)
                                                <div class="mt-1 p-1.5 bg-amber-100 rounded text-[10px] text-amber-800 font-medium flex items-center gap-1">
                                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    MSDS ini sudah lebih dari 1 tahun. Silakan review dan update di halaman Edit.
                                                </div>
                                            @elseif(!$needsReview && $doc->is_latest && $doc->review_status === 'no_update' && $doc->reviewed_at)
                                                <div class="mt-1 p-1.5 bg-blue-50 border border-blue-100 rounded text-[10px] text-blue-800 font-medium flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span>Telah dikonfirmasi belum ada revisi baru pada <strong>{{ $doc->reviewed_at->format('d M Y') }}</strong> ("{{ $doc->review_note }}"). Pengingat akan aktif kembali setahun lagi.</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex gap-1 flex-shrink-0 ml-2">
                                        <a href="{{ route('reagen.msds.document.view', $doc->id) }}" target="_blank"
                                        class="inline-flex items-center p-1.5 text-xs font-medium text-blue-700 bg-white hover:bg-blue-50 rounded-md transition-colors ring-1 ring-blue-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('reagen.msds.document.delete', $doc->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus dokumen MSDS {{ $doc->version ?? 'ini' }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center p-1.5 text-xs font-medium text-red-700 bg-white hover:bg-red-50 rounded-md transition-colors ring-1 ring-red-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex items-center justify-center p-4 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                            <div class="text-center">
                                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-1 text-xs text-gray-500">Belum ada MSDS</p>
                                <a href="{{ route('data.edit', $data->guid) }}" class="mt-1 inline-flex items-center text-xs font-medium text-blue-600 hover:text-blue-500">
                                    Upload sekarang →
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Row 3: Hazard Symbols -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Hazard Symbols
                    </h4>
                    @if(count($hazardOptions) > 0)
                        <div class="grid grid-cols-3 gap-2">
                            @foreach($hazardOptions as $hazard)
                                @php
                                    $imagePath = match($hazard) {
                                        'Environment' => 'Environmental-Hazard',
                                        default => strtolower($hazard)
                                    };
                                @endphp
                                <div class="flex flex-col items-center p-2 bg-gray-50 rounded-lg border border-gray-100 hover:shadow-sm transition-shadow">
                                    <img src="{{ asset('public/images/' . $imagePath . '.png') }}"
                                        alt="{{ $hazard }}"
                                        class="w-10 h-10 object-contain"
                                        onerror="this.style.display='none'">
                                    <span class="mt-1 text-[10px] font-medium text-gray-600 text-center">{{ $hazard }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">No hazard symbols assigned</p>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- ✅ BARU: Stock Batches (Reagen In) dengan Pagination -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <h3 class="text-base font-semibold text-gray-800">Stock Batches (Reagen In)</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    Total: {{ $reagenInPaginated->total() }}
                </span>
            </div>
            <a href="{{ route('reagen.addstock', $data->guid) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Stock
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expired Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($reagenInPaginated as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item->batch }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                {{ number_format($item->quantity, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $isExpired = \Carbon\Carbon::parse($item->expiredDate)->isPast();
                            $daysUntilExpired = \Carbon\Carbon::parse($item->expiredDate)->diffInDays(now(), false);
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isExpired ? 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20' : ($daysUntilExpired <= 30 ? 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20' : 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20') }}">
                                {{ \Carbon\Carbon::parse($item->expiredDate)->format('d M Y') }}
                                @if($isExpired) <span class="ml-1">(Expired)</span>
                                @elseif($daysUntilExpired <= 30) <span class="ml-1">(Soon)</span> @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <div class="flex items-center justify-end gap-2">
                                @if($item->coa)
                                <button type="button" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-violet-700 bg-violet-50 hover:bg-violet-100 rounded-md transition-colors preview-coa-btn" data-url="{{ route('management-stock.coa', $item->Id) }}">
                                    <svg class="-ml-0.5 mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    COA
                                </button>
                                @endif
                                <button type="button" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors open-label-modal" data-id="{{ $item->Id }}" data-expired="{{ \Carbon\Carbon::parse($item->expiredDate)->format('d F Y') }}">
                                    <svg class="-ml-0.5 mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    Label
                                </button>
                                <form action="{{ route('management-stock.delete-stock', $item->Id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition-colors confirm-delete">
                                        <svg class="-ml-0.5 mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                            No stock history available for this reagent.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ✅ PAGINATION LINKS -->
        @if($reagenInPaginated->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $reagenInPaginated->links() }}
        </div>
        @endif
    </div>

    <!-- Stock History (Monthly) Table -->
    @if($data->stockHistories && $data->stockHistories->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-base font-semibold text-gray-800">Stock History (Monthly)</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                Total: {{ $data->stockHistories->count() }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month/Year</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($data->stockHistories as $history)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $history->month }}/{{ $history->year }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+{{ $history->quantity_in }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">-{{ $history->quantity_out }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $history->quantity }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Label Modal -->
<div id="labelModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" data-close-modal></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Reagent Label</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500 transition-colors" data-close-modal>
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6" id="modalContent">
                <div class="flex flex-col md:flex-row border border-gray-200 rounded-lg bg-white overflow-hidden">
                    <div class="p-6 border-b md:border-b-0 md:border-r border-gray-200 flex items-center justify-center bg-gray-50">
                        <div id="qrcode"></div>
                    </div>
                    <div class="flex-1 flex flex-col">
                        <div class="flex items-center p-4 border-b border-gray-200 bg-gray-50/50">
                            <div class="w-24 px-2">
                                <img src="{{ asset('images/logo_b7.png') }}" alt="Logo" class="w-full h-auto object-contain">
                            </div>
                            <div class="flex-1 text-center font-bold text-lg text-gray-800">LABORATORIUM QC-ANDEV</div>
                        </div>
                        <div class="flex-1 p-6 space-y-4 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="w-full sm:w-32 text-sm text-gray-600">Nama Reagen</span>
                                <span class="text-sm font-medium text-gray-900 sm:flex-1">: {{ $data->nameReagen }}</span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="w-full sm:w-32 text-sm text-gray-600">Expired Date</span>
                                <span class="text-sm font-medium text-gray-900 sm:flex-1">: <span id="modalExpired"></span></span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4">
                                <span class="w-full sm:w-32 text-sm text-gray-600">Tanggal Buka</span>
                                <span class="text-sm font-medium text-gray-900 sm:flex-1">: ___________________</span>
                            </div>
                        </div>
                        <div class="p-4 text-right text-xs text-gray-500">
                            Distribution List - Lampiran 1:WI-QO-QC-1018.02
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" data-close-modal>Close</button>
                <button type="button" id="downloadLabelBtn" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Label
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MSDS Preview Modal -->
<div id="msdsModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" data-close-msds-modal></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-5xl">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">MSDS Document Preview</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500 transition-colors" data-close-msds-modal>
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <iframe id="msdsIframe" src="" class="w-full h-[70vh] border border-gray-200 rounded-lg bg-gray-50" frameborder="0"></iframe>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                <a id="msdsDownloadBtn" href="" download class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download PDF
                </a>
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" data-close-msds-modal>Close</button>
            </div>
        </div>
    </div>
</div>

<!-- COA Preview Modal -->
<div id="coaModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" data-close-coa-modal></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-5xl">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">COA Document Preview</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500 transition-colors" data-close-coa-modal>
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <iframe id="coaIframe" src="" class="w-full h-[70vh] border border-gray-200 rounded-lg bg-gray-50" frameborder="0"></iframe>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                <a id="coaDownloadBtn" href="" download class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download PDF
                </a>
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" data-close-coa-modal>Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // ============================================
    // LABEL MODAL
    // ============================================
    const labelModal = document.getElementById('labelModal');
    const qrContainer = document.getElementById('qrcode');
    let qrCodeInstance = null;

    document.querySelectorAll('.open-label-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modalExpired').textContent = this.dataset.expired;
            const id = this.dataset.id;

            qrContainer.innerHTML = '';
            qrCodeInstance = new QRCode(qrContainer, {
                text: `https://reagen.onexternal.com/qrcode/${id}`,
                width: 128,
                height: 128,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.L
            });

            labelModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
    });

    // Close Label Modal
    const closeLabelModal = () => {
        labelModal.classList.add('hidden');
        document.body.style.overflow = '';
    };
    document.querySelectorAll('[data-close-modal]').forEach(btn => btn.addEventListener('click', closeLabelModal));

    // Download Label
    document.getElementById('downloadLabelBtn').addEventListener('click', function() {
        const element = document.getElementById('modalContent');
        html2canvas(element, {
            scale: 2,
            backgroundColor: '#ffffff',
            logging: false
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = `label_${Date.now()}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    });

    // ============================================
    // DELETE CONFIRMATION (SweetAlert2)
    // ============================================
    document.querySelectorAll('.confirm-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('form');
            Swal.fire({
                title: 'Delete Stock Record?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // ============================================
    // MSDS PREVIEW MODAL
    // ============================================
    const msdsModal = document.getElementById('msdsModal');
    const msdsIframe = document.getElementById('msdsIframe');
    const msdsDownloadBtn = document.getElementById('msdsDownloadBtn');

    document.querySelectorAll('.preview-msds-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.dataset.url;
            msdsIframe.src = url;
            msdsDownloadBtn.href = url;
            msdsModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
    });

    document.querySelectorAll('[data-close-msds-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
            msdsModal.classList.add('hidden');
            msdsIframe.src = '';
            document.body.style.overflow = '';
        });
    });

    // ============================================
    // COA PREVIEW MODAL
    // ============================================
    const coaModal = document.getElementById('coaModal');
    const coaIframe = document.getElementById('coaIframe');
    const coaDownloadBtn = document.getElementById('coaDownloadBtn');

    document.querySelectorAll('.preview-coa-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.dataset.url;
            coaIframe.src = url;
            coaDownloadBtn.href = url;
            coaModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
    });

    document.querySelectorAll('[data-close-coa-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
            coaModal.classList.add('hidden');
            coaIframe.src = '';
            document.body.style.overflow = '';
        });
    });

    // ============================================
    // ESCAPE KEY TO CLOSE ALL MODALS
    // ============================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLabelModal();
            msdsModal.classList.add('hidden');
            msdsIframe.src = '';
            coaModal.classList.add('hidden');
            coaIframe.src = '';
            document.body.style.overflow = '';
        }
    });
});
</script>
@endpush
@endsection