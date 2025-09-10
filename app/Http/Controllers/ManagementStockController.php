<?php

namespace App\Http\Controllers;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Reagen;
use App\Models\ReagenIn;
use App\Models\StockReagen;
use App\Models\StockHistory;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\LogbookReagen;

class ManagementStockController extends Controller
{
    public function index(Request $request){
        $keyword = $request->input('keyword');
        $user = auth()->user();

        // Query data Reagen dengan menggunakan Eloquent, filter by organization
        $query = Reagen::with(['stockReagen' => function ($query) {
            $query->select('noCatalog', 'quantity');
        }])->whereHas('reagenIn.user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        });

        // Jika ada kata kunci pencarian, tambahkan kondisi pencarian
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('noCatalog', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('nameReagen', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('merk', 'LIKE', '%' . $keyword . '%');
            });
        }

        // Menambahkan pagination dengan batasan jumlah item per halaman
        $reagens = $query->paginate(20); // 10 adalah jumlah item per halaman, sesuaikan sesuai kebutuhan

        // Mengirim data reagens paginasi ke view
        return view('management-stock.management-stock', compact('reagens'));
    }

    public function addReagen(){
        return view('management-stock.add-reagen');
    }

    public function addReagenStore(Request $request){
        // Validasi jika diperlukan
        $validatedData = $request->validate([
            'noCatalog' => 'required',
            'nameReagen' => 'required',
            'merk' => 'required',
            'packSize' => 'required',
            'hazardOptions' => 'array',
            'msds' => 'required',
            'price' => 'required'
            // tambahkan validasi lainnya jika diperlukan
        ]);

        // Check if 'hazardOptions' key exists in the request data
        $hazardOptions = $request->has('hazardOptions') ? $request->input('hazardOptions') : [];

        // konversi array menjadi string
        $validatedData['hazardOptions'] = implode(',', $hazardOptions);

        Reagen::create($validatedData);

        // Tambahkan pesan sukses ke dalam sesi
        Alert::success('Success!', 'Data has been added successfully');

        return redirect()->route('management-stock.index');
    }

    // view data reagen
    public function viewReagen($noCatalog)
    {
        $user = auth()->user();
        $data = Reagen::whereHas('reagenIn.user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->find($noCatalog);

        if (!$data) {
            abort(404, 'Data reagen tidak ditemukan atau tidak memiliki akses.');
        }

        $hazardOptions = explode(',', $data->hazardOptions);

        // Ambil data reagenIn, urutkan dari yang terbaru, dan paginasi 10 per halaman, filter by organization
        $reagenIn = $data->reagenIn()->whereHas('user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->orderBy('created_at', 'desc')->paginate(10);

        return view('management-stock.view-reagen', compact('data', 'hazardOptions', 'reagenIn'));
    }

    // edit data reagen
    public function editReagen($noCatalog)
    {
        $user = auth()->user();
        $data = Reagen::whereHas('reagenIn.user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->find($noCatalog);

        if (!$data) {
            abort(404, 'Data reagen tidak ditemukan atau tidak memiliki akses.');
        }

        $hazardOptions = explode(',', $data->hazardOptions);
        return view('management-stock.edit-reagen', compact('data', 'hazardOptions'));
    }
    
    // delete data reagen
    public function deleteReagen($noCatalog)
    {
        $user = auth()->user();
        $data = Reagen::whereHas('reagenIn.user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->find($noCatalog);

        if (!$data) {
            abort(404, 'Data reagen tidak ditemukan atau tidak memiliki akses.');
        }

        $data->delete();
        return redirect()->route('management-stock.index');
    }

    // update data reagen
    public function updateReagen(Request $request, $noCatalog)
    {
        $user = auth()->user();
        // Ambil data reagen berdasarkan nomor katalog, filter by organization
        $data = Reagen::whereHas('reagenIn.user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->find($noCatalog);

        if (!$data) {
            abort(404, 'Data reagen tidak ditemukan atau tidak memiliki akses.');
        }

        // Validasi input
        $validatedData = $request->validate([
            'noCatalog' => 'required',
            'nameReagen' => 'required',
            'merk' => 'required',
            'packSize' => 'required',
            'hazardOptions' => 'array',
            'msds' => 'required',
            'price' => 'required'
            // Tambahkan validasi lainnya jika diperlukan
        ]);

        // Check if 'hazardOptions' key exists in the request data
        $hazardOptions = $request->has('hazardOptions') ? $request->input('hazardOptions') : [];

        // konversi array menjadi string
        $validatedData['hazardOptions'] = implode(',', $hazardOptions);

        // Perbarui data reagen
        $data->update($validatedData);

        Alert::success('SUCCESS!', 'Reagen Save');

        return redirect()->route('data.view', ['noCatalog' => $data->noCatalog]);
    }

    // add stock reagen
    public function addStockReagen($noCatalog){
        $user = auth()->user();
        $reagen = Reagen::whereHas('reagenIn.user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->where('noCatalog', $noCatalog)->first();

        if (!$reagen) {
            abort(404, 'Data reagen tidak ditemukan atau tidak memiliki akses.');
        }

        return view('management-stock.add-stock-reagen', compact('reagen'));
    }

    public function getReagenData($noCatalog)
    {
        $user = auth()->user();
        $reagen = Reagen::whereHas('reagenIn.user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->where('noCatalog', $noCatalog)->first();

        if (!$reagen) {
            return response()->json(['error' => 'Data reagen tidak ditemukan atau tidak memiliki akses.'], 404);
        }

        return response()->json($reagen);
    }

    public function addStock(Request $request)
    {
        $user = auth()->user();

        // Validasi input data
        $validatedDataStock = $request->validate([
            'noCatalog' => 'required',
            'batch' => 'required',
            'quantity' => 'required|numeric',
            'expiredDate' => 'required|date',
            'note' => 'nullable'
        ]);

        // Add user_id
        $validatedDataStock['user_id'] = $user->id;

        // Simpan data ke tabel ReagenIn
        $reagenIn = ReagenIn::create($validatedDataStock);

    // Tambahan kode untuk menambahkan quantity pada stock_reagens
    $stockReagen = StockReagen::where('noCatalog', $validatedDataStock['noCatalog'])->first();

    if ($stockReagen) {
        // Jika data sudah ada, tambahkan quantity
        $stockReagen->quantity += $validatedDataStock['quantity'];
        $stockReagen->save();
    } else {
        // Jika data belum ada, buat data baru
        StockReagen::create([
            'noCatalog' => $validatedDataStock['noCatalog'],
            'quantity' => $validatedDataStock['quantity'],
            // Tambahkan kolom-kolom lain sesuai kebutuhan
        ]);
    }

    Alert::success('SUCCESS!', 'Reagen Saved');

    return redirect()->back();
    }

    public function generateLabel($id)
    {
        $user = auth()->user();
        // Logic to fetch data for label generation based on $id, filter by organization
        $data = ReagenIn::whereHas('user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->find($id);

        if (!$data) {
            abort(404, 'Data tidak ditemukan atau tidak memiliki akses.');
        }

        // Generate QR code
        $qrCode = QrCode::size(100)->generate('http://127.0.0.1:8000/qrcode/'. $id);


        // Generate PDF
        $pdf = PDF::loadView('management-stock.reagen-label', compact('data', 'qrCode'));

        // Download PDF
        return $pdf->stream('reagen-label.pdf');
    }

    public function generateQrCode($id)
    {
        $user = auth()->user();
        // Check if the ReagenIn belongs to the user's organization
        $data = ReagenIn::whereHas('user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->find($id);

        if (!$data) {
            abort(404, 'Data tidak ditemukan atau tidak memiliki akses.');
        }

        // Generate QR code
        $qrCode = QrCode::format('png')->generate('http://127.0.0.1:8000/qrcode/' . $id);

        // Return QR code as base64 data URL
        return 'data:image/png;base64,' . base64_encode($qrCode);
    }

    public function deleteStock($id)
    {
        $user = auth()->user();
        // Ambil data stok berdasarkan id, filter by organization
        $reagenIn = ReagenIn::whereHas('user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->find($id);

        if ($reagenIn) {
            // Kurangi quantity dari stock_reagens
            $stockReagen = StockReagen::where('noCatalog', $reagenIn->noCatalog)->first();
            if ($stockReagen) {
                $stockReagen->quantity -= $reagenIn->quantity;
                if ($stockReagen->quantity < 0) {
                    $stockReagen->quantity = 0; // Jangan biarkan quantity menjadi negatif
                }
                $stockReagen->save();
            }

            // Dapatkan bulan dan tahun saat ini
            $currentMonth = Carbon::now()->format('m');
            $currentYear = Carbon::now()->format('Y');

            // Cek apakah sudah ada entri pada stock_histories dengan bulan dan tahun saat ini
            $stockHistory = StockHistory::where('noCatalog', $reagenIn->noCatalog)
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->first();

            if ($stockHistory) {
                // Update data stock_histories
                $stockHistory->quantity -= $reagenIn->quantity;
                $stockHistory->quantity_in -= $reagenIn->quantity; // Sesuaikan dengan logika Anda
                if ($stockHistory->quantity < 0) {
                    $stockHistory->quantity = 0; // Jangan biarkan quantity menjadi negatif
                }
                $stockHistory->save();
            }

            // Hapus data reagenIn
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
        // Ambil data dan urutkan berdasarkan created_at secara desc, filter by organization
        $reagenIn = ReagenIn::whereHas('user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d'); // Grup berdasarkan tanggal (format YYYY-MM-DD)
            });

        // Convert hasil groupBy ke collection
        $groupedData = collect($reagenIn);

        // Pagination manual
        $currentPage = request()->get('page', 1); // Ambil halaman saat ini
        $perPage = 10; // Jumlah grup per halaman
        $paginatedData = new LengthAwarePaginator(
            $groupedData->forPage($currentPage, $perPage), // Data untuk halaman saat ini
            $groupedData->count(), // Total jumlah grup
            $perPage, // Jumlah grup per halaman
            $currentPage, // Halaman saat ini
            ['path' => request()->url()] // URL untuk pagination
        );

        return view('management-stock.reagen-in', compact('paginatedData'));
    }

    public function reagenOut()
    {
        $user = auth()->user();
        // Ambil data dan urutkan berdasarkan created_at secara desc, filter by organization
        $reagenOut = LogbookReagen::whereHas('user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d'); // Grup berdasarkan tanggal (format YYYY-MM-DD)
            });
    
        // Convert hasil groupBy ke collection
        $groupedData = collect($reagenOut);
    
        // Pagination manual
        $currentPage = request()->get('page', 1); // Ambil halaman saat ini
        $perPage = 10; // Jumlah grup per halaman
        $paginatedData = new LengthAwarePaginator(
            $groupedData->forPage($currentPage, $perPage), // Data untuk halaman saat ini
            $groupedData->count(), // Total jumlah grup
            $perPage, // Jumlah grup per halaman
            $currentPage, // Halaman saat ini
            ['path' => request()->url()] // URL untuk pagination
        );
    
        return view('management-stock.reagen-out', compact('paginatedData'));
    }
    
    public function reagenExpired(Request $request)
    {
        $user = auth()->user();
        $query = ReagenIn::with('reagen')
            ->where('quantity', '>', 0)
            ->whereHas('user', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            });

        // Add search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('reagen', function($q) use ($search) {
                $q->where('nameReagen', 'LIKE', "%{$search}%")
                  ->orWhere('noCatalog', 'LIKE', "%{$search}%");
            });
        }

        // Get results with pagination
        $reagenExpired = $query->orderBy('expiredDate', 'asc')
            ->paginate(15)
            ->through(function ($item) {
                $today = now();
                $expDate = Carbon::parse($item->expiredDate);
                $daysUntilExpired = $today->diffInDays($expDate, false);

                // Add status and color based on expiry timeframe
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
