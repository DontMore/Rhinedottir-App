<form action="{{ route('msds.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <!-- ✅ PERBAIKAN: Hidden input menggunakan reagen_guid -->
    <input type="hidden" name="reagen_guid" value="{{ $reagen->guid }}">
    
    <div class="soft-card">
        <h3 class="text-lg font-extrabold text-slate-800 mb-4">
            Upload MSDS untuk: {{ $reagen->nameReagen }}
        </h3>
        <p class="text-xs font-mono text-slate-400 mb-6">{{ $reagen->noCatalog }}</p>
        
        <div class="mb-4">
            <label class="label-soft">Judul Dokumen (Opsional)</label>
            <input type="text" name="document_title" 
                   placeholder="Misal: MSDS Acetone AR - Rev.2024"
                   class="soft-input">
        </div>
        
        <div class="mb-6">
            <label class="label-soft">File MSDS <span class="text-red-400">*</span></label>
            <input type="file" name="msds_file" 
                   accept=".pdf,.ppt,.pptx" required
                   class="block w-full text-sm text-slate-500 font-semibold 
                          file:mr-4 file:py-2.5 file:px-5 file:rounded-full 
                          file:border-0 file:text-xs file:font-bold 
                          file:bg-indigo-100 file:text-indigo-600 
                          hover:file:bg-indigo-200 file:cursor-pointer 
                          bg-slate-50 rounded-full py-3 px-5">
            <p class="mt-2 text-xs font-semibold text-slate-400 ml-1">
                Format: PDF, PPT, PPTX • Maks. 20MB
            </p>
        </div>
        
        <div class="flex justify-end gap-3">
            <a href="{{ route('msds.index') }}" class="soft-btn-secondary">Batal</a>
            <button type="submit" class="soft-btn-primary">
                <i class="bi bi-cloud-upload mr-2"></i> Upload MSDS
            </button>
        </div>
    </div>
</form>