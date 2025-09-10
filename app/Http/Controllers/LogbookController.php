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

        // Menambahkan pagination
        $reagens = $query->paginate(20); // Mengatur jumlah item per halaman, misalnya 10

        return view('logbook.logbook', compact('reagens'));
    }

    // Fungsi takeReagen
    public function takeReagen($noCatalog){
        $user = auth()->user();
        $reagen = Reagen::with(['stockReagen' => function ($query) {
                $query->select('noCatalog', 'quantity');
            }])
            ->whereHas('reagenIn.user', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            })
            ->where('noCatalog', $noCatalog)
            ->first();

        if (!$reagen) {
            abort(404, 'Data reagen tidak ditemukan atau tidak memiliki akses.');
        }

        return view('logbook.logbook-take', compact('reagen'));
    }

    public function store(Request $request)
    {
    $user = auth()->user();
    // Validasi form
    $validatedData = $request->validate([
        'noCatalog' => 'required',
        'batch' => 'required',
        'quantity_taken' => 'required|numeric|min:1',
        'note' => 'nullable|string',
    ]);

    // Set user_id to current user
    $validatedData['user_id'] = $user->id;

    // Check if there is sufficient stock in the stock_reagens table, filter by organization
    $availableStock = StockReagen::whereHas('reagen.reagenIn.user', function ($q) use ($user) {
        $q->where('organization_id', $user->organization_id);
    })->where('noCatalog', $validatedData['noCatalog'])->sum('quantity');

    if ($availableStock < $validatedData['quantity_taken']) {
        // Redirect back with an error message if the requested quantity exceeds the available stock
        Alert::error('Failed', 'Please recheck the stock and the quantity being taken.')->showCloseButton();
        return redirect()->back();
    }

    // Create a new entry in the LogbookReagen table
    LogbookReagen::create($validatedData);

    // Update the stock_reagens table by reducing the quantity, filter by organization
    StockReagen::whereHas('reagen.reagenIn.user', function ($q) use ($user) {
        $q->where('organization_id', $user->organization_id);
    })->where('noCatalog', $validatedData['noCatalog'])
        ->orderBy('created_at') // You may need to adjust the order based on your business logic
        ->decrement('quantity', $validatedData['quantity_taken']);

        Alert::success('Success', 'Informasi has been successfully saved.');

    // Redirect with a success message or handle it based on your requirements
    return redirect()->route('logbook.index');
    }

    public function logbookHistory($noCatalog)
    {
        $user = auth()->user();
        // Ambil data logbook dengan pagination, filter by organization
        $logbookReagens = LogbookReagen::whereHas('user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->where('noCatalog', $noCatalog)
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->through(function ($logbook) {
                $logbook->formatted_created_at = $logbook->created_at->format('d-m-Y');
                return $logbook;
            });

        $reagen = Reagen::with(['stockReagen' => function ($query) {
            $query->select('noCatalog', 'quantity');
        }])
        ->whereHas('reagenIn.user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })
        ->where('noCatalog', $noCatalog)
        ->first();

        if (!$reagen) {
            abort(404, 'Data reagen tidak ditemukan atau tidak memiliki akses.');
        }

        return view('logbook.logbook-history', compact('reagen', 'logbookReagens'));
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
        $user = auth()->user();

        // Lakukan logika sesuai kebutuhan dengan menggunakan $noCatalog

        // Contoh: Ambil data reagen berdasarkan nomor katalog, filter by organization
        $reagen = Reagen::whereHas('reagenIn.user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->where('noCatalog', $noCatalog)->first();

        // Ambil data analis dari database, filter by organization
        $analisList = DB::table('users')
            ->where('organization_id', $user->organization_id)
            ->pluck('name', 'id');

        // Jika reagen tidak ditemukan, redirect ke halaman lain atau berikan pesan error
        if (!$reagen) {
            return redirect()->route('order.index')->with('error', 'Reagen not found');
        }

        // Kirim data reagen ke view take-admin.blade.php
        return view('logbook.logbook-take-admin', compact('reagen', 'analisList'));
    }

    // menyimpan data take reagen admin
    public function storeTakeAdmin(Request $request)
    {
        $user = auth()->user();

        // Validasi form
    $validatedData = $request->validate([
        'noCatalog' => 'required',
        'user_id' => 'required',
        'batch' => 'required',
        'quantity_taken' => 'required|numeric|min:1',
        'analis' => 'required|exists:users,id',
        'note' => 'nullable|string',
        'date' => 'required|date', // pastikan ini valid
    ]);

    // Check if there is sufficient stock in the stock_reagens table, filter by organization
    $availableStock = StockReagen::whereHas('reagen.reagenIn.user', function ($q) use ($user) {
        $q->where('organization_id', $user->organization_id);
    })->where('noCatalog', $validatedData['noCatalog'])->sum('quantity');

    if ($availableStock < $validatedData['quantity_taken']) {
        // Redirect back with an error message if the requested quantity exceeds the available stock
        Alert::error('Failed', 'Please recheck the stock and the quantity being taken.')->showCloseButton();
        return redirect()->back();
    }

    // Dapatkan bulan dan tahun saat ini
    $currentMonth = Carbon::now()->format('m');
    $currentYear = Carbon::now()->format('Y');

    // Cek apakah sudah ada entri pada stock_histories dengan bulan dan tahun saat ini
    $stockHistory = DB::table('stock_histories')
        ->where('noCatalog', $validatedData['noCatalog'])
        ->where('month', $currentMonth)
        ->where('year', $currentYear)
        ->first();

    if ($stockHistory) {
        // Update data
        DB::table('stock_histories')
            ->where('noCatalog', $validatedData['noCatalog'])
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->update([
                'quantity' => $stockHistory->quantity - $validatedData['quantity_taken'],
                'quantity_out' => $stockHistory->quantity_out + $validatedData['quantity_taken'],
                'updated_at' => now(),
            ]);
    } else {
        // Jika belum ada, buat entri baru pada stock_histories
        StockHistory::create([
            'noCatalog' => $validatedData['noCatalog'],
            'quantity' => 0,
            'quantity_in' => 0,
            'quantity_out' => $validatedData['quantity_taken'],
            'month' => $currentMonth,
            'year' => $currentYear,
        ]);
    }

    // Create a new entry in the LogbookReagen table
    LogbookReagen::create([
        'noCatalog' => $validatedData['noCatalog'],
        'user_id' => $validatedData['user_id'],
        'batch' => $validatedData['batch'],
        'quantity_taken' => $validatedData['quantity_taken'],
        'user_id' => $validatedData['analis'], // Assuming `analis` is an ID
        'note' => $validatedData['note'],
        'created_at' => $validatedData['date'], // Set `created_at` manually
    ]);

    // Update the stock_reagens table by reducing the quantity, filter by organization
    StockReagen::whereHas('reagen.reagenIn.user', function ($q) use ($user) {
        $q->where('organization_id', $user->organization_id);
    })->where('noCatalog', $validatedData['noCatalog'])
        ->orderBy('created_at') // Adjust order based on your logic
        ->decrement('quantity', $validatedData['quantity_taken']);

    Alert::success('Success', 'Information has been successfully saved.');

    // Redirect with a success message or handle it based on your requirements
    return redirect()->route('logbook.index');
    }

}
