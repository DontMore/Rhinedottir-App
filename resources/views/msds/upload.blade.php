@extends('layout.main')
@section('title', 'Upload Materi Training MSDS')

@section('container')
<style>
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap');

.upload-page { 
    font-family: 'Nunito', sans-serif; 
    background: #F8FAFC; 
    min-height: 100vh; 
    padding: 24px; 
}

.upload-card {
    background: #FFFFFF;
    border-radius: 28px;
    box-shadow: 0px 15px 35px rgba(0,0,0,0.04);
    padding: 40px;
}

.upload-zone {
    border: 3px dashed #CBD5E1;
    border-radius: 24px;
    padding: 60px 40px;
    text-align: center;
    transition: all 0.3s ease;
    background: #F8FAFC;
    cursor: pointer;
}

.upload-zone:hover, .upload-zone.dragover {
    border-color: #6366F1;
    background: #EEF2FF;
}

.upload-zone.has-file {
    border-color: #10B981;
    background: #ECFDF5;
}

.soft-input {
    background: #F1F5F9;
    border: none !important;
    border-radius: 50px;
    padding: 14px 24px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #334155;
    width: 100%;
    transition: all 0.3s ease;
}

.soft-input:focus {
    outline: none;
    background: #FFFFFF;
    box-shadow: 0 0 0 4px rgba(99,102,241,0.1);
}

.soft-btn {
    border-radius: 50px;
    padding: 14px 32px;
    font-weight: 800;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.soft-btn-primary {
    background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
    color: white;
    box-shadow: 0px 10px 25px rgba(79, 70, 229, 0.25);
}

.soft-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0px 15px 30px rgba(79, 70, 229, 0.35);
}

.soft-btn-secondary {
    background: #F1F5F9;
    color: #475569;
}

.soft-btn-secondary:hover {
    background: #E2E8F0;
}

.label-soft {
    display: block;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #64748B;
    margin-bottom: 8px;
    margin-left: 4px;
}
</style>

<div class="upload-page">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <a href="{{ route('msds.index') }}" class="text-xs font-bold text-indigo-500 hover:text-indigo-700 mb-2 inline-flex items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
            </a>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">📤 Upload Materi Training MSDS</h2>
            <p class="text-sm font-medium text-slate-500 mt-1">Tambahkan materi pelatihan untuk reagen ini.</p>
        </div>
    </div>

    {{-- Info Reagen Card --}}
    <div class="bg-white rounded-[24px] shadow-[0px_15px_35px_rgba(0,0,0,0.04)] p-6 mb-8 flex items-center gap-6">
        <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
            <i class="bi bi-droplet text-3xl text-indigo-500"></i>
        </div>
        <div class="flex-1">
            <h3 class="text-lg font-extrabold text-slate-800">{{ $reagen->nameReagen }}</h3>
            <p class="text-sm font-mono text-slate-400 mt-1">{{ $reagen->noCatalog }}</p>
            <div class="flex items-center gap-4 mt-2">
                <span class="text-xs font-semibold text-slate-500">
                    <i class="bi bi-box mr-1"></i> {{ $reagen->merk }}
                </span>
                <span class="text-xs font-semibold text-slate-500">
                    <i class="bi bi-rulers mr-1"></i> {{ $reagen->packSize }}
                </span>
            </div>
        </div>
        <div class="text-right">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Materi</p>
            <p class="text-2xl font-extrabold text-indigo-600">{{ $reagen->msds_documents_count }} File</p>
        </div>
    </div>

    {{-- Upload Form --}}
    <form action="{{ route('msds.store') }}" method="POST" enctype="multipart/form-data" class="upload-card">
        @csrf
        <input type="hidden" name="reagen_guid" value="{{ $reagen->guid }}">

        {{-- Upload Zone --}}
        <div class="mb-8">
            <label class="label-soft">File Materi Training <span class="text-red-400">*</span></label>
            <div id="dropZone" class="upload-zone" onclick="document.getElementById('fileInput').click()">
                <input type="file" id="fileInput" name="msds_file" accept=".pdf,.ppt,.pptx" required 
                       class="hidden" onchange="handleFileSelect(this)">
                
                <div id="uploadPlaceholder">
                    <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-cloud-upload text-4xl text-indigo-500"></i>
                    </div>
                    <h4 class="text-base font-extrabold text-slate-700 mb-2">Klik atau Drag & Drop file di sini</h4>
                    <p class="text-sm text-slate-500 mb-4">Format yang didukung: PDF, PPT, PPTX</p>
                    <span class="inline-flex items-center px-4 py-2 bg-white rounded-full text-xs font-bold text-slate-600 shadow-sm">
                        <i class="bi bi-file-earmark mr-2"></i> Maksimal 20MB
                    </span>
                </div>

                <div id="fileInfo" class="hidden">
                    <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-check2 text-3xl text-emerald-500"></i>
                    </div>
                    <h4 id="fileName" class="text-base font-extrabold text-slate-700 mb-2"></h4>
                    <p id="fileSize" class="text-sm text-slate-500 mb-4"></p>
                    <button type="button" onclick="clearFile()" class="text-xs font-bold text-red-500 hover:text-red-700">
                        <i class="bi bi-trash mr-1"></i> Hapus File
                    </button>
                </div>
            </div>
        </div>

        {{-- Document Title --}}
        <div class="mb-8">
            <label class="label-soft">Judul Dokumen <span class="text-slate-400">(Opsional)</span></label>
            <input type="text" name="document_title" 
                   placeholder="Contoh: MSDS Acetone AR - Prosedur Keamanan"
                   class="soft-input"
                   value="{{ old('document_title') }}">
            <p class="mt-2 text-xs font-semibold text-slate-400 ml-1">Jika kosong, akan menggunakan nama file</p>
        </div>

        {{-- ✅ TRAINER INPUT (BARU) --}}
<div class="mb-8">
    <label class="label-soft">Nama Trainer <span class="text-slate-400">(Bisa lebih dari 1 orang)</span></label>
    
    <div id="trainersContainer" class="space-y-3">
        {{-- Trainer Input Pertama --}}
        <div class="trainer-item flex items-center gap-3">
            <div class="flex-1 relative">
                <input type="text" name="trainers[]" 
                       placeholder="Masukkan nama trainer"
                       class="soft-input pr-12"
                       value="{{ old('trainers.0') }}">
                <i class="bi bi-person absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            </div>
            <button type="button" onclick="removeTrainer(this)" 
                    class="w-12 h-[52px] flex items-center justify-center rounded-full bg-red-50 text-red-500 hover:bg-red-100 transition-all opacity-0 pointer-events-none"
                    disabled>
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
    
    {{-- Tombol Tambah Trainer --}}
    <button type="button" onclick="addTrainer()" 
                class="mt-3 inline-flex items-center gap-2 px-5 py-2.5 text-xs font-extrabold text-indigo-600 bg-indigo-50 rounded-full hover:bg-indigo-100 transition-all">
            <i class="bi bi-plus-circle"></i>
            <span>Tambah Trainer Lain</span>
        </button>
        
        <p class="mt-2 text-xs font-semibold text-slate-400 ml-1">Klik tombol untuk menambahkan trainer tambahan</p>
    </div>

        {{-- File Type Info --}}
        <div class="bg-slate-50 rounded-[20px] p-6 mb-8">
            <h4 class="text-sm font-extrabold text-slate-700 mb-4 flex items-center gap-2">
                <i class="bi bi-info-circle text-indigo-500"></i>
                Panduan Upload
            </h4>
            <ul class="space-y-2 text-sm text-slate-600">
                <li class="flex items-start gap-2">
                    <i class="bi bi-check2 text-emerald-500 mt-0.5"></i>
                    <span>Format file: PDF, PowerPoint (PPT/PPTX)</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="bi bi-check2 text-emerald-500 mt-0.5"></i>
                    <span>Ukuran maksimal: 20 MB</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="bi bi-check2 text-emerald-500 mt-0.5"></i>
                    <span>Pastikan materi berisi informasi keselamatan dan prosedur penanganan reagen</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="bi bi-check2 text-emerald-500 mt-0.5"></i>
                    <span>Satu reagen dapat memiliki multiple materi training</span>
                </li>
            </ul>
        </div>

        {{-- Action Buttons --}}
        <div class="flex justify-end gap-4 pt-4">
            <a href="{{ route('msds.manage', $reagen) }}" class="soft-btn soft-btn-secondary">
                Batal
            </a>
            <button type="submit" class="soft-btn soft-btn-primary flex items-center gap-2">
                <i class="bi bi-cloud-upload"></i>
                <span>Upload Materi</span>
            </button>
        </div>
    </form>
</div>

<script>
function handleFileSelect(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = fileSize + ' MB';
        
        document.getElementById('uploadPlaceholder').classList.add('hidden');
        document.getElementById('fileInfo').classList.remove('hidden');
        document.getElementById('dropZone').classList.add('has-file');
    }
}

function clearFile() {
    const input = document.getElementById('fileInput');
    input.value = '';
    
    document.getElementById('uploadPlaceholder').classList.remove('hidden');
    document.getElementById('fileInfo').classList.add('hidden');
    document.getElementById('dropZone').classList.remove('has-file');
}

// Drag and Drop functionality
const dropZone = document.getElementById('dropZone');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, unhighlight, false);
});

function highlight(e) {
    dropZone.classList.add('dragover');
}

function unhighlight(e) {
    dropZone.classList.remove('dragover');
}

dropZone.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    
    if (files.length > 0) {
        const input = document.getElementById('fileInput');
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(files[0]);
        input.files = dataTransfer.files;
        handleFileSelect(input);
    }
}
</script>

@push('scripts')
<script>
let trainerCount = 1;

function addTrainer() {
    const container = document.getElementById('trainersContainer');
    const newIndex = trainerCount;
    
    const trainerDiv = document.createElement('div');
    trainerDiv.className = 'trainer-item flex items-center gap-3 animate-fade-in';
    trainerDiv.innerHTML = `
        <div class="flex-1 relative">
            <input type="text" name="trainers[]" 
                   placeholder="Masukkan nama trainer ${newIndex + 1}"
                   class="soft-input pr-12"
                   value="">
            <i class="bi bi-person absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
        </div>
        <button type="button" onclick="removeTrainer(this)" 
                class="w-12 h-[52px] flex items-center justify-center rounded-full bg-red-50 text-red-500 hover:bg-red-100 transition-all">
            <i class="bi bi-trash"></i>
        </button>
    `;
    
    container.appendChild(trainerDiv);
    trainerCount++;
    
    // Enable delete button untuk trainer pertama jika sebelumnya hanya ada 1
    updateTrainerButtons();
}

function removeTrainer(button) {
    const trainerItem = button.closest('.trainer-item');
    trainerItem.remove();
    trainerCount--;
    
    updateTrainerButtons();
}

function updateTrainerButtons() {
    const trainerItems = document.querySelectorAll('.trainer-item');
    const deleteButtons = document.querySelectorAll('.trainer-item button');
    
    if (trainerItems.length === 1) {
        // Hanya 1 trainer, disable delete button
        deleteButtons.forEach(btn => {
            btn.classList.add('opacity-0', 'pointer-events-none');
            btn.disabled = true;
        });
    } else {
        // Lebih dari 1 trainer, enable semua delete button
        deleteButtons.forEach(btn => {
            btn.classList.remove('opacity-0', 'pointer-events-none');
            btn.disabled = false;
        });
    }
}

// Initialize button state
document.addEventListener('DOMContentLoaded', function() {
    updateTrainerButtons();
});
</script>
@endpush
@endsection