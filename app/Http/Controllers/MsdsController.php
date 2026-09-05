<?php

namespace App\Http\Controllers;

use App\Models\Reagen;
use App\Models\MsdsDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MsdsController extends Controller
{
    /**
     * Tampilkan daftar semua reagen dengan status MSDS
     */
    public function index()
    {
        // ✅ withCount akan menghitung jumlah msdsDocuments per reagen
        $reagens = Reagen::withCount('msdsDocuments')
            ->with('latestMsds')
            ->orderBy('nameReagen')
            ->paginate(12);

        return view('msds.index', compact('reagens'));
    }

    /**
     * Form upload MSDS untuk reagen tertentu
     * ✅ PERBAIKAN: Route model binding menggunakan 'guid'
     */
    public function create(Reagen $reagen)
    {
        $reagen->load('msdsDocuments');
        return view('msds.upload', compact('reagen'));
    }

    /**
     * Simpan file MSDS (PDF/PPT/PPTX)
     */
    public function store(Request $request)
    {
        $request->validate([
            // ✅ PERBAIKAN: exists validation harus merujuk ke kolom 'guid'
            'reagen_guid'    => 'required|exists:reagens,guid',
            'msds_file'      => 'required|file|mimes:pdf,ppt,pptx|max:20480',
            'document_title' => 'nullable|string|max:255',
        ]);

        $file = $request->file('msds_file');
        
        // Simpan di folder: storage/app/public/msds/{reagen_guid}/
        $path = $file->store('msds/' . $request->reagen_guid, 'public');

        MsdsDocument::create([
            'reagen_guid' => $request->reagen_guid,  // ✅ PERBAIKAN
            'file_path'   => $path,
            'file_name'   => $file->getClientOriginalName(),
            'file_size'   => $file->getSize(),
            'file_type'   => strtolower($file->getClientOriginalExtension()),
            'title'       => $request->document_title ?? $file->getClientOriginalName(),
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()
            ->route('msds.index')
            ->with('success', '✅ MSDS berhasil diunggah!');
    }

    /**
     * Lihat / Download file MSDS
     */
    public function show(Reagen $reagen)
    {
        // ✅ PERBAIKAN: Gunakan relasi yang sudah didefinisikan
        $document = $reagen->latestMsds;
        
        if (!$document || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Dokumen MSDS tidak ditemukan.');
        }

        return response()->file(storage_path('app/public/' . $document->file_path));
    }

    /**
     * ✅ BARU: Menampilkan daftar SEMUA materi training untuk 1 reagen
     */
    public function manage(Reagen $reagen)
    {
        // Ambil semua dokumen MSDS untuk reagen ini, diurutkan dari yang terbaru
        $documents = $reagen->msdsDocuments()->latest()->get();
        
        return view('msds.manage', compact('reagen', 'documents'));
    }

    /**
     * ✅ DIUBAH: Hapus 1 file dokumen spesifik (bukan semua file reagen)
     */
    public function destroy(MsdsDocument $document)
    {
        // Hapus file fisik
        Storage::disk('public')->delete($document->file_path);
        
        // Hapus record dari database
        $document->delete();

        return back()->with('success', 'Materi training berhasil dihapus.');
    }
}