<?php

namespace App\Http\Controllers;
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

class ManagementStockController extends Controller
{
    public function index(Request $request){
        $keyword = $request->input('keyword');
    
        // Query data Reagen dengan menggunakan Eloquent
        $query = Reagen::with(['stockReagen' => function ($query) {
            $query->select('noCatalog', 'quantity');
        }]);
    
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

        return redirect()->route('management-stock');
    }

    // view data reagen
    public function viewReagen($noCatalog)
    {
        $data = Reagen::find($noCatalog);
        $hazardOptions = explode(',', $data->hazardOptions);
        $reagenIn = $data->reagenIn;
        return view('management-stock.view-reagen', compact('data', 'hazardOptions', 'reagenIn'));
    }

    // edit data reagen
    public function editReagen($noCatalog)
    {
        $data = Reagen::find($noCatalog);
        $hazardOptions = explode(',', $data->hazardOptions);
        return view('management-stock.edit-reagen', compact('data', 'hazardOptions'));
    }
    
    // delete data reagen
    public function deleteReagen($noCatalog)
    {
        $data = Reagen::find($noCatalog);
        $data->delete();
        return redirect()->route('management-stock');
    }

    // update data reagen
    public function updateReagen(Request $request, $noCatalog)
    {
        // Ambil data reagen berdasarkan nomor katalog
        $data = Reagen::find($noCatalog);

        // Validasi input
        $validatedData = $request->validate([
            'noCatalog' => 'required',
            'nameReagen' => 'required',
            'merk' => 'required',
            'packSize' => 'required',
            'hazardOptions' => 'required|array',
            'msds' => 'required',
            'price' => 'required'
            // Tambahkan validasi lainnya jika diperlukan
        ]);

        // Konversi array menjadi string
        $validatedData['hazardOptions'] = implode(',', $validatedData['hazardOptions']);

        // Perbarui data reagen
        $data->update($validatedData);

        Alert::success('SUCCESS!', 'Reagen Save');

        return redirect()->route('data.view', ['noCatalog' => $data->noCatalog]);
    }

    // add stock reagen
    public function addStockReagen($noCatalog){
        $reagen = Reagen::where('noCatalog', $noCatalog)->first();
        return view('management-stock.add-stock-reagen', compact('reagen'));
    }

    public function getReagenData($noCatalog)
    {
        $reagen = Reagen::where('noCatalog', $noCatalog)->first();
        return response()->json($reagen);
    }

    public function addStock(Request $request)
    {
        
    // Validasi input data
    $validatedDataStock = $request->validate([
        'noCatalog' => 'required',
        'batch' => 'required',
        'quantity' => 'required|numeric',
        'expiredDate' => 'required|date',
        'note' => 'nullable'
    ]);

    // Simpan data ke tabel ReagenIn
    $reagenIn = ReagenIn::create($validatedDataStock);

    // Dapatkan bulan dan tahun saat ini
    $currentMonth = Carbon::now()->format('m'); // 'F' akan mengembalikan nama bulan dalam bentuk lengkap
    $currentYear = Carbon::now()->format('Y');  // 'Y' akan mengembalikan tahun dalam format empat digit


    // Cek apakah sudah ada entri pada stock_histories dengan bulan dan tahun saat ini
    $stockHistory = DB::table('stock_histories')
        ->where('noCatalog',  $validatedDataStock['noCatalog'])
        ->where('month', $currentMonth)
        ->where('year', $currentYear)
        ->first();

    if ($stockHistory) {
            // Update data
            $updateResult = DB::table('stock_histories')
                ->where('noCatalog',  $validatedDataStock['noCatalog'])
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->update([
                    'quantity' => $stockHistory->quantity + $validatedDataStock['quantity'],
                    'quantity_in' => $stockHistory->quantity_in + $validatedDataStock['quantity'],  // Gantilah dengan nilai yang diinginkan
                    'updated_at' => now(),
                ]);
    } else {
        // Jika belum ada, buat entri baru pada stock_histories
        StockHistory::create([
            'noCatalog' => $validatedDataStock['noCatalog'],
            'quantity' => $validatedDataStock['quantity'], // Default quantity, dapat diubah sesuai kebutuhan
            'quantity_in' => $validatedDataStock['quantity'],
            'quantity_out' => 0, // Default quantity_out, dapat diubah sesuai kebutuhan
            'month' => Carbon::now()->format('m'),
            'year' => Carbon::now()->format('Y'),
        ]);
    }

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
        // Logic to fetch data for label generation based on $id
        // For example:
        $data = ReagenIn::find($id);

        // Generate QR code
        $qrCode = QrCode::size(100)->generate('http://127.0.0.1:8000/qrcode/'. $id);

        
        // Generate PDF
        $pdf = PDF::loadView('management-stock.reagen-label', compact('data', 'qrCode'));
        
        // Download PDF
        return $pdf->stream('reagen-label.pdf');
    }

    public function generateQrCode($id)
    {
        // Generate QR code
        $qrCode = QrCode::format('png')->generate('http://127.0.0.1:8000/qrcode/' . $id);
        
        // Return QR code as base64 data URL
        return 'data:image/png;base64,' . base64_encode($qrCode);
    }

}
