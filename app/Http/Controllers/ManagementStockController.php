<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Models\Reagen;
use App\Models\ReagenIn;
use App\Models\StockReagen;
use App\Models\StockHistory;
use App\Models\LogbookReagen;
use App\Models\ReagenGroup;
use App\Models\ReagenCategory;
use App\Models\StorageLocation;
use App\Models\ReagenMsds;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ManagementStockController extends Controller
{

    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $groupGuid = $request->input('group_guid');
        $categoryGuid = $request->input('category_guid');
        $status = $request->input('status', 'active'); // ✅ Filter status (active/inactive/all)
        $user = auth()->user();

        $query = Reagen::with(['stockReagen', 'group', 'category', 'latestReagenMsds'])
            ->where('organization_guid', $user->organization_guid);

        // ✅ Filter berdasarkan status
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }
        // Jika 'all', tidak ada filter

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('noCatalog', 'LIKE', '%' . $keyword . '%')
                ->orWhere('nameReagen', 'LIKE', '%' . $keyword . '%')
                ->orWhere('merk', 'LIKE', '%' . $keyword . '%');
            });
        }
        if ($groupGuid) {
            $query->where('group_guid', $groupGuid);
        }
        if ($categoryGuid) {
            $query->where('category_guid', $categoryGuid);
        }

        $reagens = $query->paginate(20);

        $groups = ReagenGroup::where('organization_guid', $user->organization_guid)->latest()->get();
        $categories = ReagenCategory::where('organization_guid', $user->organization_guid)->latest()->get();

        return view('management-stock.management-stock', compact('reagens', 'groups', 'categories', 'status'));
    }

    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('reagen_groups', 'name')->where('organization_guid', auth()->user()->organization_guid)
            ]
        ]);

        ReagenGroup::create([
            'name' => $validated['name'],
            'organization_guid' => auth()->user()->organization_guid
        ]);

        Alert::success('Success', 'Group berhasil dibuat.');
        return back();
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('reagen_categories', 'name')->where('organization_guid', auth()->user()->organization_guid)
            ]
        ]);

        ReagenCategory::create([
            'name' => $validated['name'],
            'organization_guid' => auth()->user()->organization_guid
        ]);

        Alert::success('Success', 'Category berhasil dibuat.');
        return back();
    }

    public function addReagen()
    {
        $user = auth()->user();
        $groups = ReagenGroup::where('organization_guid', $user->organization_guid)->orderBy('name')->get();
        $categories = ReagenCategory::where('organization_guid', $user->organization_guid)->orderBy('name')->get();
        $storageLocations = StorageLocation::where('organization_guid', $user->organization_guid)->orderBy('code')->get();

        return view('management-stock.add-reagen', compact('groups', 'categories', 'storageLocations'));
    }

    // ============================================
    // ✅ BARU: Handle Multiple MSDS Upload
    // ============================================
    private function handleMultipleReagenMsdsUpload(Request $request, string $reagenGuid): array
    {
        if (!$request->hasFile('msds_files')) {
            return [];
        }

        $user = auth()->user();
        $files = $request->file('msds_files');
        $versions = $request->input('msds_versions', []);
        $revisionDates = $request->input('msds_revision_dates', []);
        $notes = $request->input('msds_notes', []);
        $uploadedDocs = [];

        // Reset flag is_latest untuk semua dokumen lama
        ReagenMsds::where('reagen_guid', $reagenGuid)
            ->update(['is_latest' => false]);

        foreach ($files as $index => $file) {
            if (!$file || !$file->isValid()) continue;

            $destinationPath = 'reagen_msds/' . $reagenGuid;
            if (!Storage::disk('public')->exists($destinationPath)) {
                Storage::disk('public')->makeDirectory($destinationPath);
            }

            $fileName = time() . '_' . $index . '_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $file->getClientOriginalName());
            $storedPath = $file->storeAs($destinationPath, $fileName, 'public');

            if ($storedPath) {
                $doc = ReagenMsds::create([
                    'reagen_guid' => $reagenGuid,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $storedPath,
                    'version' => !empty($versions[$index]) ? $versions[$index] : null,
                    'revision_date' => !empty($revisionDates[$index]) ? $revisionDates[$index] : null,
                    'notes' => !empty($notes[$index]) ? $notes[$index] : null,
                    'is_latest' => true,
                    'organization_guid' => $user->organization_guid,
                    'uploaded_by' => $user->id,
                ]);
                $uploadedDocs[] = $doc;
            }
        }

        return $uploadedDocs;
    }

    // ============================================
    // ✅ UPDATE: addReagenStore dengan Multiple MSDS
    // ============================================
    public function addReagenStore(Request $request)
    {
        $validatedData = $request->validate([
            'noCatalog' => 'required',
            'nameReagen' => 'required',
            'merk' => 'required',
            'packSize' => 'required',
            'hazardOptions' => 'array',
            // ✅ Multiple MSDS files dengan versi & tanggal revisi
            'msds_files' => 'required|array|min:1',
            'msds_files.*' => 'file|mimes:pdf|max:10240',
            'msds_versions' => 'nullable|array',
            'msds_versions.*' => 'nullable|string|max:50',
            'msds_revision_dates' => 'nullable|array',
            'msds_revision_dates.*' => 'nullable|date',
            'msds_notes' => 'nullable|array',
            'msds_notes.*' => 'nullable|string|max:500',
            'price' => 'required',
            'buffer_stock' => 'required|numeric|min:0',
            'signal_word' => 'nullable|in:Danger,Warning,None',
            'group_guid' => 'nullable|uuid|exists:reagen_groups,guid',
            'category_guid' => 'nullable|uuid|exists:reagen_categories,guid',
            'storage_location_guid' => 'nullable|uuid|exists:storage_locations,guid',
            'reagent_form' => 'required|in:liquid,solid,crystal',
        ]);

        $hazardOptions = $request->has('hazardOptions') ? $request->input('hazardOptions') : [];
        $validatedData['hazardOptions'] = implode(',', $hazardOptions);
        $validatedData['organization_guid'] = auth()->user()->organization_guid;
        $validatedData['recommended_storage_location'] = Reagen::getRecommendedStorage(
            $hazardOptions,
            auth()->user()->organization_guid
        );

        // Hapus field msds_files dari validated data (bukan kolom reagens)
        unset($validatedData['msds_files'], $validatedData['msds_versions'],
              $validatedData['msds_revision_dates'], $validatedData['msds_notes']);

        // Kosongkan kolom msds lama (legacy)
        $validatedData['msds'] = '';

        $reagen = Reagen::create($validatedData);

        // ✅ Upload multiple MSDS ke tabel reagen_msds
        $uploadedDocs = $this->handleMultipleReagenMsdsUpload($request, $reagen->guid);

        Alert::success('Success!', 'Data has been added successfully with ' . count($uploadedDocs) . ' MSDS document(s)');
        return redirect()->route('management-stock.index');
    }

    // ============================================
    // ✅ UPDATE: viewReagen dengan MSDS documents
    // ============================================
    public function viewReagen($guid)
    {
        $user = auth()->user();
        $data = Reagen::with([
            'stockReagen',
            'stockHistories' => function ($q) {
                $q->orderBy('created_at', 'desc');
            },
            'group',
            'category'
        ])
        ->where('guid', $guid)
        ->where('organization_guid', $user->organization_guid)
        ->firstOrFail();

        // ✅ PAGINATION: Ambil ReagenIn dengan pagination (10 per halaman)
        $reagenInPaginated = ReagenIn::where('reagen_guid', $guid)
            ->where('organization_guid', $user->organization_guid)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // ✅ Ambil semua dokumen MSDS (multi-versi)
        $reagenMsdsDocuments = \App\Models\ReagenMsds::where('reagen_guid', $guid)
            ->orderBy('is_latest', 'desc')
            ->orderBy('revision_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $hazardOptions = array_filter(explode(',', $data->hazardOptions ?? ''));

        return view('management-stock.view-reagen', compact(
            'data', 'hazardOptions', 'reagenInPaginated', 'reagenMsdsDocuments'
        ));
    }

    // ============================================
    // ✅ UPDATE: editReagen dengan MSDS documents
    // ============================================
    public function editReagen($guid)
    {
        $user = auth()->user();
        $data = Reagen::where('guid', $guid)
            ->where('organization_guid', $user->organization_guid)
            ->firstOrFail();

        // ✅ Ambil semua dokumen MSDS yang sudah ada
        $reagenMsdsDocuments = \App\Models\ReagenMsds::where('reagen_guid', $guid)
            ->orderBy('is_latest', 'desc')
            ->orderBy('revision_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $hazardOptions = array_filter(explode(',', $data->hazardOptions ?? ''));
        $groups = ReagenGroup::where('organization_guid', $user->organization_guid)->orderBy('name')->get();
        $categories = ReagenCategory::where('organization_guid', $user->organization_guid)->orderBy('name')->get();
        $storageLocations = StorageLocation::where('organization_guid', $user->organization_guid)->orderBy('code')->get();

        return view('management-stock.edit-reagen', compact(
            'data', 'hazardOptions', 'groups', 'categories', 'storageLocations', 'reagenMsdsDocuments'
        ));
    }

    // ============================================
    // ✅ UPDATE: updateReagen dengan Multiple MSDS
    // ============================================
    public function updateReagen(Request $request, $guid)
    {
        $user = auth()->user();
        $data = Reagen::where('guid', $guid)
            ->where('organization_guid', $user->organization_guid)
            ->firstOrFail();

        $validatedData = $request->validate([
            'noCatalog' => 'required',
            'nameReagen' => 'required',
            'merk' => 'required',
            'packSize' => 'required',
            'hazardOptions' => 'array',
            'msds_files' => 'nullable|array',
            'msds_files.*' => 'file|mimes:pdf|max:10240',
            'msds_versions' => 'nullable|array',
            'msds_versions.*' => 'nullable|string|max:50',
            'msds_revision_dates' => 'nullable|array',
            'msds_revision_dates.*' => 'nullable|date',
            'msds_notes' => 'nullable|array',
            'msds_notes.*' => 'nullable|string|max:500',
            // ✅ BARU: Validasi untuk review
            'review_action' => 'nullable|in:upload_new,no_update',
            'review_no_update_note' => 'nullable|string|max:1000',
            'price' => 'required',
            'buffer_stock' => 'required|numeric|min:0',
            'signal_word' => 'nullable|in:Danger,Warning,None',
            'group_guid' => 'nullable|uuid|exists:reagen_groups,guid',
            'category_guid' => 'nullable|uuid|exists:reagen_categories,guid',
            'storage_location_guid' => 'nullable|uuid|exists:storage_locations,guid',
            'reagent_form' => 'required|in:liquid,solid,crystal',
        ]);

        // ✅ BARU: Handle Review Logic
        $reviewAction = $request->input('review_action');
        $latestMsds = \App\Models\ReagenMsds::where('reagen_guid', $guid)
            ->where('is_latest', true)
            ->first();

        if ($latestMsds && $latestMsds->needsReview()) {
            if ($reviewAction === 'upload_new') {
                // Validasi: Harus ada file yang diupload
                if (!$request->hasFile('msds_files') || empty($request->file('msds_files')[0] ?? null)) {
                    return back()->withErrors([
                        'msds_files' => 'Anda memilih upload versi baru, silakan upload file MSDS.'
                    ])->withInput();
                }
            } elseif ($reviewAction === 'no_update') {
                // Validasi: Harus ada note
                $reviewNote = $request->input('review_no_update_note');
                if (empty($reviewNote)) {
                    return back()->withErrors([
                        'review_no_update_note' => 'Anda memilih belum ada update, silakan tuliskan catatan konfirmasi.'
                    ])->withInput();
                }

                // Update status review di MSDS terbaru
                $latestMsds->update([
                    'review_status' => 'no_update',
                    'review_note' => $reviewNote,
                    'reviewed_at' => now(),
                    'reviewed_by' => $user->id,
                ]);

                Alert::success('Review Recorded', 'Catatan review berhasil disimpan. MSDS tetap valid.');
            }
        }

        $hazardOptions = $request->has('hazardOptions') ? $request->input('hazardOptions') : [];
        $validatedData['hazardOptions'] = implode(',', $hazardOptions);
        $validatedData['recommended_storage_location'] = Reagen::getRecommendedStorage(
            $hazardOptions,
            $user->organization_guid
        );

        unset($validatedData['msds_files'], $validatedData['msds_versions'],
            $validatedData['msds_revision_dates'], $validatedData['msds_notes'],
            $validatedData['review_action'], $validatedData['review_no_update_note']);

        $data->update($validatedData);

        // ✅ Jika ada file baru, tambahkan sebagai versi baru
        if ($request->hasFile('msds_files')) {
            $uploadedDocs = $this->handleMultipleReagenMsdsUpload($request, $data->guid);
            
            // ✅ Tandai review sebagai "reviewed" jika upload baru
            if ($latestMsds && $latestMsds->needsReview()) {
                $latestMsds->update([
                    'review_status' => 'reviewed',
                    'review_note' => 'Replaced with new version',
                    'reviewed_at' => now(),
                    'reviewed_by' => $user->id,
                ]);
            }

            Alert::success('SUCCESS!', 'Reagen updated & ' . count($uploadedDocs) . ' new MSDS version(s) added');
        } else {
            Alert::success('SUCCESS!', 'Reagen updated successfully');
        }

        return redirect()->route('data.view', ['guid' => $data->guid]);
    }

    // ============================================
    // ✅ BARU: View/Download MSDS by document ID
    // ============================================
    public function viewReagenMsdsDocument($documentId)
    {
        $user = auth()->user();
        $doc = ReagenMsds::where('id', $documentId)
            ->where('organization_guid', $user->organization_guid)
            ->firstOrFail();

        if (!Storage::disk('public')->exists($doc->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->response(
            $doc->file_path,
            $doc->file_name,
            ['Content-Disposition' => 'inline; filename="' . $doc->file_name . '"']
        );
    }

    // ============================================
    // ✅ BARU: Delete single MSDS document
    // ============================================
    public function deleteReagenMsdsDocument($documentId)
    {
        $user = auth()->user();
        $doc = ReagenMsds::where('id', $documentId)
            ->where('organization_guid', $user->organization_guid)
            ->firstOrFail();

        if (Storage::disk('public')->exists($doc->file_path)) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $doc->delete();

        Alert::success('Success', 'MSDS document deleted');
        return redirect()->back();
    }

    // ============================================
    // LEGACY: showMsds (untuk backward compatibility)
    // ============================================
    public function showMsds($guid)
    {
        $user = auth()->user();
        $reagen = Reagen::where('guid', $guid)
            ->where('organization_guid', $user->organization_guid)
            ->firstOrFail();

        // Coba ambil dari tabel reagen_msds dulu (latest)
        $latestMsds = ReagenMsds::where('reagen_guid', $guid)
            ->where('is_latest', true)
            ->first();

        if ($latestMsds && Storage::disk('public')->exists($latestMsds->file_path)) {
            return Storage::disk('public')->response(
                $latestMsds->file_path,
                $latestMsds->file_name,
                ['Content-Disposition' => 'inline; filename="' . $latestMsds->file_name . '"']
            );
        }

        // Fallback ke kolom msds lama
        if (empty($reagen->msds) || !Storage::disk('public')->exists($reagen->msds)) {
            abort(404, 'MSDS file not found.');
        }

        return Storage::disk('public')->response(
            $reagen->msds,
            basename($reagen->msds),
            ['Content-Disposition' => 'inline; filename="' . basename($reagen->msds) . '"']
        );
    }

    public function deleteReagen($guid)
    {
        $user = auth()->user();
        $data = Reagen::where('guid', $guid)
            ->where('organization_guid', $user->organization_guid)
            ->firstOrFail();

        $data->delete();

        Alert::success('Success!', 'Data has been deleted successfully');
        return redirect()->route('management-stock.index');
    }

    public function getReagenData($guid)
    {
        $user = auth()->user();
        $reagen = Reagen::where('guid', $guid)
            ->where('organization_guid', $user->organization_guid)
            ->first();

        if (!$reagen) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        return response()->json($reagen);
    }

    // ============================================
    // COA Upload Handler
    // ============================================
    private function handleCoaUpload(Request $request, ?string $currentPath = null): ?string
    {
        if (!$request->hasFile('coa')) {
            return $currentPath;
        }

        $file = $request->file('coa');
        if (!$file || !$file->isValid()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'coa' => ['File COA tidak valid atau gagal diupload.'],
            ]);
        }

        if ($currentPath && Storage::disk('public')->exists($currentPath)) {
            Storage::disk('public')->delete($currentPath);
        }

        try {
            $storedPath = $file->storeAs('coa_files', $file->hashName(), 'public');
        } catch (\Throwable $e) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'coa' => ['Gagal menyimpan file COA ke storage: ' . $e->getMessage()],
            ]);
        }

        if (empty($storedPath) || $storedPath === false) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'coa' => ['Gagal menyimpan file COA ke storage.'],
            ]);
        }

        return $storedPath;
    }

    public function addStock(Request $request)
    {
        $user = auth()->user();
        $filePath = null;

        if ($request->hasFile('coa')) {
            $filePath = $this->handleCoaUpload($request);
        }

        $validatedDataStock = $request->validate([
            'noCatalog' => 'required',
            'batch' => 'required',
            'quantity' => 'required|numeric',
            'expiredDate' => 'required|date',
            'note' => 'nullable',
            'coa' => 'required|file|mimes:pdf|max:10240',
        ]);

        if (!empty($filePath)) {
            $validatedDataStock['coa'] = $filePath;
        }

        $masterReagen = Reagen::where('noCatalog', $validatedDataStock['noCatalog'])
            ->where('organization_guid', $user->organization_guid)
            ->firstOrFail();

        $validatedDataStock['reagen_guid'] = $masterReagen->guid;
        $validatedDataStock['user_id'] = $user->id;
        $validatedDataStock['organization_guid'] = $user->organization_guid;

        ReagenIn::create($validatedDataStock);

        $stockReagen = StockReagen::where('reagen_guid', $masterReagen->guid)
            ->where('organization_guid', $user->organization_guid)
            ->first();

        if ($stockReagen) {
            $stockReagen->quantity += $validatedDataStock['quantity'];
            $stockReagen->save();
        } else {
            StockReagen::create([
                'noCatalog' => $validatedDataStock['noCatalog'],
                'reagen_guid' => $masterReagen->guid,
                'quantity' => $validatedDataStock['quantity'],
                'organization_guid' => $user->organization_guid,
            ]);
        }

        Alert::success('SUCCESS!', 'Stok berhasil ditambahkan');
        OrderController::refreshRecommendations($user->organization_guid);

        return redirect()->back();
    }

    public function addStockReagen($guid)
    {
        $user = auth()->user();
        $reagen = Reagen::where('guid', $guid)
            ->where('organization_guid', $user->organization_guid)
            ->first();

        if (!$reagen) {
            abort(404, "Data reagen tidak ditemukan atau Anda tidak memiliki akses.");
        }

        return view('management-stock.add-stock-reagen', compact('reagen'));
    }

    public function generateLabel($id)
    {
        $user = auth()->user();
        $data = ReagenIn::whereHas('user', function ($q) use ($user) {
            $q->where('organization_guid', $user->organization_guid);
        })->find($id);

        if (!$data) abort(404, 'Data tidak ditemukan atau tidak memiliki akses.');

        $qrCode = QrCode::size(100)->generate(url('/qrcode/' . $id));
        $pdf = PDF::loadView('management-stock.reagen-label', compact('data', 'qrCode'));
        return $pdf->stream('reagen-label.pdf');
    }

    public function generateQrCode($id)
    {
        $user = auth()->user();
        $data = ReagenIn::whereHas('user', function ($q) use ($user) {
            $q->where('organization_guid', $user->organization_guid);
        })->find($id);

        if (!$data) abort(404, 'Data tidak ditemukan atau tidak memiliki akses.');

        $qrCode = QrCode::format('png')->generate(url('/qrcode/' . $id));
        return 'data:image/png;base64,' . base64_encode($qrCode);
    }

    public function deleteStock($id)
    {
        $user = auth()->user();
        $reagenIn = ReagenIn::whereHas('user', function ($q) use ($user) {
            $q->where('organization_guid', $user->organization_guid);
        })->find($id);

        if ($reagenIn) {
            $stockReagen = StockReagen::where('noCatalog', $reagenIn->noCatalog)
                ->where('organization_guid', $user->organization_guid)
                ->first();

            if ($stockReagen) {
                $stockReagen->quantity -= $reagenIn->quantity;
                if ($stockReagen->quantity < 0) $stockReagen->quantity = 0;
                $stockReagen->save();
            }

            $currentMonth = Carbon::now()->format('m');
            $currentYear = Carbon::now()->format('Y');
            $stockHistory = StockHistory::where('noCatalog', $reagenIn->noCatalog)
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->first();

            if ($stockHistory) {
                $stockHistory->quantity -= $reagenIn->quantity;
                $stockHistory->quantity_in -= $reagenIn->quantity;
                if ($stockHistory->quantity < 0) $stockHistory->quantity = 0;
                $stockHistory->save();
            }

            $reagenIn->delete();
            Alert::success('SUCCESS!', 'Stock deleted successfully');
        } else {
            Alert::error('Error', 'Stock not found');
        }

        return redirect()->route('management-stock.index');
    }

    public function reagenIn()
    {
        $user = auth()->user();
        $reagenIn = ReagenIn::with('reagen')
            ->where('organization_guid', $user->organization_guid)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        $groupedData = collect($reagenIn);
        $currentPage = request()->get('page', 1);
        $perPage = 10;
        $paginatedData = new LengthAwarePaginator(
            $groupedData->forPage($currentPage, $perPage),
            $groupedData->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );

        return view('management-stock.reagen-in', compact('paginatedData'));
    }

    public function reagenOut()
    {
        $user = auth()->user();
        $reagenOut = LogbookReagen::with(['reagen', 'user'])
            ->whereHas('user', function ($q) use ($user) {
                $q->where('organization_guid', $user->organization_guid);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        $groupedData = collect($reagenOut);
        $currentPage = request()->get('page', 1);
        $perPage = 10;
        $paginatedData = new LengthAwarePaginator(
            $groupedData->forPage($currentPage, $perPage),
            $groupedData->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );

        return view('management-stock.reagen-out', compact('paginatedData'));
    }

    public function reagenExpired(Request $request)
    {
        $user = auth()->user();
        $query = ReagenIn::with('reagen')
            ->where('quantity', '>', 0)
            ->where('organization_guid', $user->organization_guid);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('reagen', function ($q) use ($search) {
                $q->where('nameReagen', 'LIKE', "%{$search}%")
                  ->orWhere('noCatalog', 'LIKE', "%{$search}%");
            });
        }

        $reagenExpired = $query->orderBy('expiredDate', 'asc')
            ->paginate(15)
            ->through(function ($item) {
                $today = now();
                $expDate = Carbon::parse($item->expiredDate);
                $daysUntilExpired = $today->diffInDays($expDate, false);

                if ($daysUntilExpired < 0) {
                    $item->status = 'Expired';
                    $item->status_color = 'dark';
                } elseif ($daysUntilExpired <= 7) {
                    $item->status = 'Akan Expired (< 1 minggu)';
                    $item->status_color = 'danger';
                } elseif ($daysUntilExpired <= 30) {
                    $item->status = 'Akan Expired (< 1 bulan)';
                    $item->status_color = 'warning';
                } else {
                    $item->status = 'Tidak Expired';
                    $item->status_color = 'success';
                }

                $item->days_until_expired = $daysUntilExpired;
                return $item;
            });

        return view('dashboard.reagen-expired', compact('reagenExpired'));
    }

    public function showCoa($id)
    {
        $user = auth()->user();
        $reagenIn = ReagenIn::whereHas('reagen', function ($q) use ($user) {
            $q->where('organization_guid', $user->organization_guid);
        })->where('Id', $id)->firstOrFail();

        if (empty($reagenIn->coa) || !Storage::disk('public')->exists($reagenIn->coa)) {
            abort(404, 'COA file not found.');
        }

        return Storage::disk('public')->response(
            $reagenIn->coa,
            basename($reagenIn->coa),
            ['Content-Disposition' => 'inline; filename="' . basename($reagenIn->coa) . '"']
        );
    }

    // ============================================
    // ✅ BARU: Toggle Active/Inactive Reagen
    // ============================================
    public function toggleReagenStatus($guid)
    {
        $user = auth()->user();
        $data = Reagen::where('guid', $guid)
            ->where('organization_guid', $user->organization_guid)
            ->firstOrFail();

        $data->is_active = !$data->is_active;
        $data->save();

        $status = $data->is_active ? 'activated' : 'deactivated';
        Alert::success('Success!', "Reagen has been {$status}");

        return redirect()->back();
    }
}