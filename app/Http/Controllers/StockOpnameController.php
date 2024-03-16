<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\StockReagen;
use App\Models\Reagen;
use App\Models\StockHistory;
use App\Models\StockOpname;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;
use PDF;
use Validator;


class StockOpnameController extends Controller
{
    //
    public function index(Request $request)
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
                ->sum('quantity_actual');
        });

        return view('stock-opname.stock-opname', compact('reagens', 'month', 'year'));
    }

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
