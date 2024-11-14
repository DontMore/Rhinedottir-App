<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\StockReagen;
use App\Models\Reagen;
use App\Models\ReagenIn;
use App\Models\LogbookReagen;
use App\Models\StockHistory;
use App\Models\StockOpname;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;
use PDF;
use Validator;


class StockOpnameController extends Controller
{
    //
    public function index()
    {
        $yearNow = Carbon::now()->year;

        // Mengecek apakah ada data dengan tahun saat ini
        $yearCheck = StockOpname::where('year', $yearNow)->exists();
        $yearData = StockOpname::where('year', $yearNow)->get();
        $month = $yearData->pluck('month')->toArray();

        $allMonth = range(1, 12);

        // Ada bulan yang tidak ada
        $missMonth = array_diff($allMonth, $month);

        // Menyimpan bulan yang kurang ke database
        foreach ($missMonth as $missMonth) {
            // Simpan data ke database
            StockOpname::create([
                'month' => $missMonth,
                'year' => $yearNow,
                'user_id' => auth()->user()->id,
                // Tambahkan atribut lain yang diperlukan sesuai struktur tabel `ReagenIn`
                // Misalnya: 'nameReagen' => 'Default Name', 'batch' => 'Default Batch'
            ]);
        }

        

        $data = StockOpname::all();

        return view('stock-opname.so-list', compact('data'));
    }

    public function soDetail(Request $request)
    {
        $timezone = config('app.timezone');
        // Mengambil parameter bulan dan tahun dari request
        $month = $request->input('month') ?? Carbon::now()->format('m');
        $year = $request->input('year') ?? Carbon::now()->format('Y');

        // dd($month);

        $noCatalog = StockHistory::where('year', $year)->where('month', $month)->pluck('noCatalog')->toArray();
        $noCatalogStd = Reagen::all()->pluck('noCatalog')->toArray();

        // mencari noCatalog yang tidak ada pada database History Stock
        $missNoCatalog = array_diff($noCatalogStd, $noCatalog);

        // Mengambil semua data reagen beserta relasinya dengan filter bulan dan tahun
        $reagens = StockHistory::where('year', $year)
        ->where('month', $month) 
        ->get();

        // Mengambil quantity_actual untuk bulan sebelumnya
        $previousMonthData = StockHistory::where('year', $year)
        ->where('month', $month - 1)
        ->get(['noCatalog', 'quantity_actual']); // Ambil noCatalog dan quantity_actual bulan sebelumnya

        // Mengaitkan quantity_before dengan reagen saat ini
        $reagens->each(function ($reagen) use ($previousMonthData) {
        // Mencari data bulan sebelumnya yang cocok dengan noCatalog saat ini
        $previousData = $previousMonthData->firstWhere('noCatalog', $reagen->noCatalog);

        // Jika data ditemukan, ambil quantity_actual
        $reagen->quantity_before = $previousData ? $previousData->quantity_actual : null; // Set null jika tidak ada data
        });

        // Menyimpan bulan yang kurang ke database
        foreach ($missNoCatalog as $missNoCatalog) {
            // Find quantity_actual from StockReagen for the previous data
            $previousData = StockReagen::where('noCatalog', $missNoCatalog)->first();
            
            // mentotalkan data reagen masuk
            $reagenInQuantitySum = ReagenIn::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('noCatalog', $missNoCatalog)
                    ->sum('quantity');

            // mentotalkan data reagen diambil
            $reagenOutQuantitySum = LogbookReagen::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('noCatalog', $missNoCatalog)
                    ->sum('quantity_taken');
            
            // Retrieve quantity from ReagenIn if it exists, otherwise set to null or default
            $reagenInData = $reagenInQuantitySum ? $reagenInQuantitySum : 0;
            $reagenOutData = $reagenOutQuantitySum ? $reagenOutQuantitySum : 0;
                            
            // Save the quantity_before in the new record
            StockHistory::create([
                'month' => $month,
                'year' => $year,
                'noCatalog' => $missNoCatalog,
                'quantity' => $previousData ? $previousData->quantity : 0,
                'quantity_in' => $reagenInData,
                'quantity_out' => $reagenOutData
                // Add other required attributes here, e.g., 'nameReagen' => 'Default Name', etc.
            ]);
        }

        // var_dump($reagens->quantity_in);die;

        return view('stock-opname.so-detail', compact('reagens', 'month', 'year'));
    }

    public function generatedSO(Request $request)
    {
        $timezone = config('app.timezone');
        // Mengambil parameter bulan dan tahun dari request
        $month = $request->input('month') ?? Carbon::now()->format('m');
        $year = $request->input('year') ?? Carbon::now()->format('Y');

        // hapus semua data pada tahun dan bulan yang di input
        StockHistory::where('year', $year)->where('month', $month)->delete();

        // mengambil semua nomor katalog pada database reagen
        $noCatalogStd = Reagen::all()->pluck('noCatalog')->toArray();

        // Menyimpan bulan yang kurang ke database
        foreach ($noCatalogStd as $noCatalog) {
            // Find quantity_actual from StockReagen for the previous data
            $previousData = StockHistory::where('year', $year)
                    ->where('month', $month - 1)
                    ->where('noCatalog', $noCatalog) // Filter berdasarkan noCatalog yang ditentukan
                    ->first('quantity_actual'); // Ambil noCatalog dan quantity_actual bulan sebelumnya
            
            // mentotalkan data reagen masuk
            $reagenInQuantitySum = ReagenIn::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('noCatalog', $noCatalog)
                    ->sum('quantity');

            // mentotalkan data reagen diambil
            $reagenOutQuantitySum = LogbookReagen::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('noCatalog', $noCatalog)
                    ->sum('quantity_taken');

            
            // Retrieve quantity from ReagenIn if it exists, otherwise set to null or default
            $reagenInData = $reagenInQuantitySum ? $reagenInQuantitySum : 0;
            $reagenOutData = $reagenOutQuantitySum ? $reagenOutQuantitySum : 0;
                            
            // Save the quantity_before in the new record
            StockHistory::create([
                'month' => $month,
                'year' => $year,
                'noCatalog' => $noCatalog,
                'quantity' => $previousData ? $previousData->quantity_actual : 0,
                'quantity_in' => $reagenInData,
                'quantity_out' => $reagenOutData
                // Add other required attributes here, e.g., 'nameReagen' => 'Default Name', etc.
            ]);
        }

        // Mengambil semua data reagen beserta relasinya dengan filter bulan dan tahun
        $reagens = StockHistory::where('year', $year)
            ->where('month', $month) 
            ->get();

        // Mengambil quantity_actual untuk bulan sebelumnya
        $previousMonthData = StockHistory::where('year', $year)
            ->where('month', $month - 1)
            ->get(['noCatalog', 'quantity_actual']); // Ambil noCatalog dan quantity_actual bulan sebelumnya

        // Mengaitkan quantity_before dengan reagen saat ini
        $reagens->each(function ($reagen) use ($previousMonthData) {
        // Mencari data bulan sebelumnya yang cocok dengan noCatalog saat ini
        $previousData = $previousMonthData->firstWhere('noCatalog', $reagen->noCatalog);

        // Jika data ditemukan, ambil quantity_actual
        $reagen->quantity_before = $previousData ? $previousData->quantity_actual : null; // Set null jika tidak ada data
        });

        return view('stock-opname.so-detail', compact('reagens', 'month', 'year'));
    }

    // public function generatedSO(Request $request)
    // {
    //    // Mengambil bulan dan tahun dari request
    //     $month = $request->input('month');
    //     $year = $request->input('year');

    //     // Mengambil semua data noCatalog dari model Reagen
    //     $noCatalogs = Reagen::select('noCatalog')->get();

        

    //     // Membuat array baru untuk menambahkan bulan dan tahun ke setiap noCatalog
    //     $dataWithMonthYear = $noCatalogs->map(function ($reagen) use ($month, $year) {
    //         return [
    //             'noCatalog' => $reagen->noCatalog,
    //             'month' => $month,
    //             'year' => $year,
    //         ];
    //     });

    //     // Menggunakan var_dump untuk menampilkan hasil
    //     var_dump($dataWithMonthYear);

    //     // Jika Anda ingin mengembalikan hasil ke view, bisa dilakukan seperti ini
    //     // return view('your_view_name', compact('dataWithMonthYear'));

    //     // Misalnya, mengambil data dari database berdasarkan bulan dan tahun
    //     $data = StockHistory::where('month', $month)->where('year', $year)->get();

    //     return view('stock-opname.generated', compact('data', 'month', 'year'));
    // }

    public function generateStock(Request $request)
    {
        $timezone = config('app.timezone');
        // Mengambil parameter bulan dan tahun dari request
        $month = $request->input('month') ?? Carbon::now()->format('m');
        $year = $request->input('year') ?? Carbon::now()->format('Y');

        // Mengambil semua data reagen beserta relasinya dengan filter bulan dan tahun
        $reagens = StockHistory::where('year', $year)
                            ->where('month', $month)
                            ->get();

        $reagens->each(function ($reagen) use ($year, $month) {
            $reagen->quantity_before = StockHistory::where('year', $year)
                ->where('month', $month - 1)
                ->sum('quantity');
        });

        $data = [
            'title' => 'Report Stock Reagen',
            'month' => $month,
            'year' => $year,
            'reagens' => $reagens,
        ];

        $pdf = PDF::loadView('stock-opname.stock-report', $data);

        return $pdf->stream('laporan.pdf');
    }

    public function updateQuantities(Request $request)
    {

    $timezone = config('app.timezone');
    // Mengambil parameter bulan dan tahun dari request
    $month = $request->input('month') ?? Carbon::now()->format('m');
    $year = $request->input('year') ?? Carbon::now()->format('Y');
    // Retrieve the validated data
    $quantityActualJson = $request->input('quantity_actual');

    // Decode the JSON data
    $updatedQuantities = json_decode($quantityActualJson, true);

    // Update the database records
    foreach ($updatedQuantities as $id => $quantity) {
        $updateResult = DB::table('stock_histories')
        ->where('id',  $id)
        ->update([
            'quantity' => $quantity,
        ]);
    
    }

    // Mengambil semua data reagen beserta relasinya dengan filter bulan dan tahun
    $reagens = StockHistory::where('year', $year)
        ->where('month', $month)
        ->get();

    $reagens->each(function ($reagen) use ($year, $month) {
    $reagen->quantity_before = StockHistory::where('year', $year)
        ->where('month', $month - 1)
        ->sum('quantity');
    });

    $dataSesuai = true;

    foreach ($reagens as $reagen) {
        $selisih = $reagen->quantity - (($reagen->quantity_before + $reagen->quantity_in) - $reagen->quantity_out);

        if ($selisih != 0) {
            $dataSesuai = false;
            break;  // Exit the loop early since we already found a non-zero $selisih
        }
    }

    if ($dataSesuai) {
        // Check if the record already exists for the given month and year
        $existingRecord = StockOpname::where('month', $month)
            ->where('year', $year)
            ->where('user_id', auth()->user()->id)
            ->first();

        if ($existingRecord) {
            // Update the existing record
            $existingRecord->update(['status' => 1]);
        } else {
            // Create a new record
            StockOpname::create([
                'month' => $month,
                'year' => $year,
                'user_id' => auth()->user()->id,
                'status' => 1,
            ]);
        }
    } else {
        // Check if the record already exists for the given month and year
        $existingRecord = StockOpname::where('month', $month)
            ->where('year', $year)
            ->where('user_id', auth()->user()->id)
            ->first();

        if ($existingRecord) {
            // Update the existing record
            $existingRecord->update(['status' => 1]);
        } else {
            // Create a new record
            StockOpname::create([
                'month' => $month,
                'year' => $year,
                'user_id' => auth()->user()->id,
                'status' => 0,
            ]);
        }
    }

    // Tambahkan pesan sukses ke dalam sesi
    Alert::success('Success!', 'Data Stock Opname berhasil disimpan');

    // Return a response indicating success
    return redirect()->back()->with('success', 'Quantities updated successfully!');
    }

    public function getReagen($id) {
        $stockHistory = StockHistory::with('reagen')->find($id);
        $reagen = $stockHistory->reagen;
        return response()->json([
            'stockHistory' => $stockHistory,
            'reagen' => $reagen
        ]);
    }

    public function update(Request $request)
    {
        // Validasi data yang diterima dari formulir jika diperlukan
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity_actual' => 'required|numeric',
            'catatan' => 'nullable|string',
            'status' => 'nullable|string',
            'user_id' => 'required',
        ]);

        // Periksa apakah validasi gagal
        if ($validator->fails()) {
            // Tampilkan semua data yang divalidasi dan pesan kesalahan validasi pada konsol
            dd($request->all(), $validator->errors()->all());
            // Mengembalikan respons dengan kesalahan validasi
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Jika validasi berhasil, lanjutkan dengan logika pembaruan
        // Update data
        $updateResult = DB::table('stock_histories')
        ->where('id',  $request->id)
        ->update([
            'quantity_actual' => $request->quantity_actual,
            'catatan' => $request->catatan,
            'status' => $request->status,
            'user_id' => $request->user_id,
            'stock_opname' => 1,
            'updated_at' => now(),
        ]);

        // Mengembalikan respons dengan data yang diperbarui
        return response()->json(['success' => 'Stock history updated successfully'], 200);
    }
      
}
