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
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Validation\Rule;

class ManagementStockController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $groupGuid = $request->input('group_guid');
        $categoryGuid = $request->input('category_guid');
        $user = auth()->user();

        $query = Reagen::with(['stockReagen', 'group', 'category'])->where('organization_guid', $user->organization_guid);

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

        // ✅ Ambil Group & Category milik organisasi user
        $groups = ReagenGroup::where('organization_guid', $user->organization_guid)->latest()->get();
        $categories = ReagenCategory::where('organization_guid', $user->organization_guid)->latest()->get();

        return view('management-stock.management-stock', compact('reagens', 'groups', 'categories'));
    }

    /**
     * Simpan Group baru
     */
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

    /**
     * Simpan Category baru
     */
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

        // ✅ Ambil data berdasarkan organisasi user
        $groups = ReagenGroup::where('organization_guid', $user->organization_guid)->orderBy('name')->get();
        $categories = ReagenCategory::where('organization_guid', $user->organization_guid)->orderBy('name')->get();
        $storageLocations = StorageLocation::where('organization_guid', $user->organization_guid)->orderBy('code')->get();

        return view('management-stock.add-reagen', compact('groups', 'categories', 'storageLocations'));
    }

    public function addReagenStore(Request $request)
    {
        // ✅ Validasi termasuk field baru
        $validatedData = $request->validate([
            'noCatalog' => 'required',
            'nameReagen' => 'required',
            'merk' => 'required',
            'packSize' => 'required',
            'hazardOptions' => 'array',
            'msds' => 'required',
            'price' => 'required',
            'buffer_stock' => 'required|numeric|min:0',

            // ✅ Field Baru
            'group_guid' => 'nullable|uuid|exists:reagen_groups,guid',
            'category_guid' => 'nullable|uuid|exists:reagen_categories,guid',
            'storage_location_guid' => 'nullable|uuid|exists:storage_locations,guid',
            'reagent_form' => 'required|in:liquid,solid,crystal',
        ]);

        // Proses Hazard Options
        $hazardOptions = $request->has('hazardOptions') ? $request->input('hazardOptions') : [];
        $validatedData['hazardOptions'] = implode(',', $hazardOptions);

        // Set Organization GUID
        $validatedData['organization_guid'] = auth()->user()->organization_guid;

        // ✅ Generate Recommended Storage Otomatis
        $validatedData['recommended_storage_location'] = \App\Models\Reagen::getRecommendedStorage($hazardOptions);

        // Simpan ke Database
        \App\Models\Reagen::create($validatedData);

        Alert::success('Success!', 'Data has been added successfully');
        return redirect()->route('management-stock.index');
    }

    public function viewReagen($guid)
    {
        $user = auth()->user();
        $data = Reagen::with([
            'stockReagen',
            'stockHistories' => function ($q) {
                $q->orderBy('created_at', 'desc');
            },
            'reagenIn',
            'group',      // ✅ Tambahkan ini
            'category'    // ✅ Tambahkan ini
        ])
            ->where('guid', $guid)
            ->where('organization_guid', $user->organization_guid)
            ->firstOrFail();

        $hazardOptions = explode(',', $data->hazardOptions ?? '');
        return view('management-stock.view-reagen', compact('data', 'hazardOptions'));
    }

    // ✅ EDIT: Menggunakan $guid & filter organisasi langsung
    public function editReagen($guid)
    {
        $user = auth()->user();
        $data = Reagen::where('guid', $guid)
            ->where('organization_guid', $user->organization_guid)
            ->firstOrFail();

        $hazardOptions = explode(',', $data->hazardOptions ?? '');

        // ✅ Ambil data untuk dropdown edit
        $groups = ReagenGroup::where('organization_guid', $user->organization_guid)->orderBy('name')->get();
        $categories = ReagenCategory::where('organization_guid', $user->organization_guid)->orderBy('name')->get();
        $storageLocations = StorageLocation::where('organization_guid', $user->organization_guid)->orderBy('code')->get();

        return view('management-stock.edit-reagen', compact('data', 'hazardOptions', 'groups', 'categories', 'storageLocations'));
    }

    // ✅ DELETE: Menggunakan $guid
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

    // ✅ UPDATE: Menggunakan $guid & redirect ke route berbasis guid
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
            'msds' => 'required',
            'price' => 'required',
            'buffer_stock' => 'required|numeric|min:0',
            'reagent_form' => 'required|in:liquid,solid,crystal', // ✅ Validasi baru
        ]);

        $hazardOptions = $request->has('hazardOptions') ? $request->input('hazardOptions') : [];
        $validatedData['hazardOptions'] = implode(',', $hazardOptions);

        // Update recommended storage location jika hazard berubah
        $validatedData['recommended_storage_location'] = Reagen::getRecommendedStorage(
            $hazardOptions,
            $user->organization_guid
        );

        $data->update($validatedData);
        Alert::success('SUCCESS!', 'Reagen updated successfully');
        return redirect()->route('data.view', ['guid' => $data->guid]);
    }

    // ✅ AJAX/FETCH DATA: Menggunakan $guid
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

    public function addStock(Request $request)
    {
        $user = auth()->user();
        $validatedDataStock = $request->validate([
            'noCatalog' => 'required',
            'batch' => 'required',
            'quantity' => 'required|numeric',
            'expiredDate' => 'required|date',
            'note' => 'nullable'
        ]);

        // Ambil GUID master reagen berdasarkan noCatalog yang diinput user
        $masterReagen = Reagen::where('noCatalog', $validatedDataStock['noCatalog'])
            ->where('organization_guid', $user->organization_guid)
            ->firstOrFail();

        // ✅ Simpan relasi GUID ke tabel ReagenIn
        $validatedDataStock['reagen_guid'] = $masterReagen->guid;
        $validatedDataStock['user_id'] = $user->id;
        $validatedDataStock['organization_guid'] = $user->organization_guid;

        ReagenIn::create($validatedDataStock);

        // Update/Cbuat total stock di tabel StockReagen
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

        // ✅ TRIGGER UPDATE REKOMENDASI OTOMATIS
        \App\Http\Controllers\OrderController::refreshRecommendations($user->organization_guid);

        return redirect()->back();
    }

    // app/Http/Controllers/ManagementStockController.php

    public function addStockReagen($guid) // ✅ Ganti menjadi $guid
    {
        $user = auth()->user();

        // Query menggunakan guid, bukan noCatalog
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

        // ✅ WAJIB ADA ->with(['reagen', 'user'])
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
        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
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
}
