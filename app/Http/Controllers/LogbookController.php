<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Reagen;
use App\Models\StockReagen;
use App\Models\ReagenIn;
use App\Models\LogbookReagen;
use App\Models\StockHistory;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;

use Illuminate\Http\Request;

class LogbookController extends Controller
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
    
        // Menambahkan pagination
        $reagens = $query->paginate(20); // Mengatur jumlah item per halaman, misalnya 10
    
        return view('logbook.logbook', compact('reagens'));
    }

    // Fungsi takeReagen
    public function takeReagen($noCatalog){   
        $reagen = Reagen::with(['stockReagen' => function ($query) {
                $query->select('noCatalog', 'quantity');
            }])
            ->where('noCatalog', $noCatalog)
            ->first();
    
        return view('logbook.logbook-take', compact('reagen'));
    }         

    public function store(Request $request)
    {
    // Validasi form
    $validatedData = $request->validate([
        'noCatalog' => 'required',
        'user_id' => 'required',
        'batch' => 'required',
        'quantity_taken' => 'required|numeric|min:1',
        'note' => 'nullable|string',
    ]);

    // Check if there is sufficient stock in the stock_reagens table
    $availableStock = StockReagen::where('noCatalog', $validatedData['noCatalog'])->sum('quantity');

    if ($availableStock < $validatedData['quantity_taken']) {
        // Redirect back with an error message if the requested quantity exceeds the available stock
        Alert::error('Failed', 'Please recheck the stock and the quantity being taken.')->showCloseButton();
        return redirect()->back();
    }

    // Dapatkan bulan dan tahun saat ini
    $currentMonth = Carbon::now()->format('m'); // 'F' akan mengembalikan nama bulan dalam bentuk lengkap
    $currentYear = Carbon::now()->format('Y');  // 'Y' akan mengembalikan tahun dalam format empat digit

    // Cek apakah sudah ada entri pada stock_histories dengan bulan dan tahun saat ini
    $stockHistory = DB::table('stock_histories')
        ->where('noCatalog',  $validatedData['noCatalog'])
        ->where('month', $currentMonth)
        ->where('year', $currentYear)
        ->first();

    if ($stockHistory) {
            // Update data
            $updateResult = DB::table('stock_histories')
                ->where('noCatalog',  $validatedData['noCatalog'])
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->update([
                    'quantity' => $stockHistory->quantity - $validatedData['quantity_taken'],
                    'quantity_out' => $stockHistory->quantity_out + $validatedData['quantity_taken'],  // Gantilah dengan nilai yang diinginkan
                    'updated_at' => now(),
                ]);
    } else {
        // Jika belum ada, buat entri baru pada stock_histories
        StockHistory::create([
            'noCatalog' => $validatedData['noCatalog'],
            'quantity' => 0, // Default quantity, dapat diubah sesuai kebutuhan
            'quantity_in' => 0,
            'quantity_out' => $validatedData['quantity_taken'], // Default quantity_out, dapat diubah sesuai kebutuhan
            'month' => Carbon::now()->format('m'),
            'year' => Carbon::now()->format('Y'),
        ]);
    }

    // Create a new entry in the LogbookReagen table
    LogbookReagen::create($validatedData);

    // Update the stock_reagens table by reducing the quantity
    StockReagen::where('noCatalog', $validatedData['noCatalog'])
        ->orderBy('created_at') // You may need to adjust the order based on your business logic
        ->decrement('quantity', $validatedData['quantity_taken']);

        Alert::success('Success', 'Informasi has been successfully saved.');

    // Redirect with a success message or handle it based on your requirements
    return redirect()->route('logbook.index');
    }

    public function logbookHistory($noCatalog)
    {   
        $reagen = Reagen::with(['stockReagen' => function ($query) {
            $query->select('noCatalog', 'quantity');
        }])
        ->where('noCatalog', $noCatalog)
        ->first();
        
        return view('logbook.logbook-history', compact('reagen'));
    }

    // Fungsi takeReagen
    public function takeQRCode($id){
        // Mengambil data reagen berdasarkan ID
        $reagenIn = ReagenIn::findOrFail($id);

        // Mengirimkan data reagen ke view
        return view('logbook.logbook-qrcode', compact('reagenIn'));
    }

    public function takeAdmin($noCatalog)
    {
        // Lakukan logika sesuai kebutuhan dengan menggunakan $noCatalog
        
        // Contoh: Ambil data reagen berdasarkan nomor katalog
        $reagen = Reagen::where('noCatalog', $noCatalog)->first();

        // Jika reagen tidak ditemukan, redirect ke halaman lain atau berikan pesan error
        if (!$reagen) {
            return redirect()->route('order.index')->with('error', 'Reagen not found');
        }

        // Kirim data reagen ke view take-admin.blade.php
        return view('logbook.logbook-take-admin', compact('reagen'));
    }

}
