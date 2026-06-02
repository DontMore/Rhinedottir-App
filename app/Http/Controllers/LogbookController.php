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
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $user = auth()->user();

        // ✅ Query menggunakan organization_guid
        $query = Reagen::with(['stockReagen' => function ($query) {
            $query->select('reagen_guid', 'quantity'); // ✅ foreign key wajib ada
        }])->where('organization_guid', $user->organization_guid);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('noCatalog', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('nameReagen', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('merk', 'LIKE', '%' . $keyword . '%');
            });
        }

        $reagens = $query->paginate(20);
        return view('logbook.logbook', compact('reagens'));
    }

    // ✅ TAKE REAGEN: Parameter diubah ke $guid
    public function takeReagen($guid)
    {
        $user = auth()->user();

        $reagen = Reagen::with(['stockReagen' => function ($query) {
            $query->select('reagen_guid', 'quantity');
        }])
            ->where('guid', $guid) // ✅ Filter by guid
            ->where('organization_guid', $user->organization_guid) // ✅ organization_guid
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

        // Set user_id & organization_guid
        $validatedData['user_id'] = $user->id;
        $validatedData['organization_guid'] = $user->organization_guid;

        // ✅ PERBAIKAN 1: Cek stok dengan query sederhana & langsung
        // Jangan gunakan whereHas yang berbelit. Filter langsung pakai noCatalog & org_guid
        $availableStock = StockReagen::where('noCatalog', $validatedData['noCatalog'])
            ->where('organization_guid', $user->organization_guid)
            ->sum('quantity');

        // Debug: Jika stok 0, berarti data di tabel stock_reagens kosong atau noCatalog tidak cocok
        if ($availableStock < $validatedData['quantity_taken']) {
            Alert::error('Failed', 'Stok tidak mencukupi. Tersedia: ' . $availableStock . ', Diminta: ' . $validatedData['quantity_taken'])->showCloseButton();
            return redirect()->back();
        }

        // Opsional: Ambil guid reagen untuk disimpan ke logbook
        $masterReagen = Reagen::where('noCatalog', $validatedData['noCatalog'])->first();
        if ($masterReagen) {
            $validatedData['reagen_guid'] = $masterReagen->guid;
        }

        // Create a new entry in the LogbookReagen table
        LogbookReagen::create($validatedData);

        // ✅ PERBAIKAN 2: Kurangi stok dengan logika FIFO (First In First Out)
        // Ambil semua stok untuk reagen ini
        $stockItems = StockReagen::where('noCatalog', $validatedData['noCatalog'])
            ->where('organization_guid', $user->organization_guid)
            ->orderBy('created_at', 'asc') // Ambil stok yang paling lama dulu
            ->get();

        $remainingQtyToTake = $validatedData['quantity_taken'];

        foreach ($stockItems as $stockItem) {
            if ($remainingQtyToTake <= 0) break;

            if ($stockItem->quantity >= $remainingQtyToTake) {
                // Stok di batch ini cukup
                $stockItem->quantity -= $remainingQtyToTake;
                $stockItem->save();
                $remainingQtyToTake = 0;
            } else {
                // Stok di batch ini kurang, ambil semua sisanya
                $remainingQtyToTake -= $stockItem->quantity;
                $stockItem->quantity = 0;
                $stockItem->save();
            }
        }

        Alert::success('Success', 'Information has been successfully saved.');
        return redirect()->route('logbook.index');
    }

    // ✅ LOGBOOK HISTORY: Parameter $guid, query pakai organization_guid
    public function logbookHistory($guid)
    {
        $user = auth()->user();

        // ✅ Ambil data logbook dengan filter organization_guid
        $logbookReagens = LogbookReagen::with(['reagen', 'user'])
            ->where('reagen_guid', $guid) // ✅ Filter by reagen_guid
            ->where('organization_guid', $user->organization_guid) // ✅ organization_guid
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->through(function ($logbook) {
                $logbook->formatted_created_at = $logbook->created_at->format('d-m-Y');
                return $logbook;
            });

        // ✅ Ambil data master reagen berdasarkan guid
        $reagen = Reagen::with(['stockReagen' => function ($query) {
            $query->select('reagen_guid', 'quantity'); // ✅ foreign key wajib ada
        }])
            ->where('guid', $guid)
            ->where('organization_guid', $user->organization_guid)
            ->firstOrFail();

        return view('logbook.logbook-history', compact('reagen', 'logbookReagens'));
    }

    // ✅ ADMIN DELETE LOGBOOK HISTORY + ROLLBACK STOCK_HISTORIES
    public function deleteLogbookHistory($guid)
    {
        $user = auth()->user();

        // Ambil logbook history berdasarkan guid + akses organisasi
        $logbook = LogbookReagen::where('guid', $guid)
            ->where('organization_guid', $user->organization_guid)
            ->firstOrFail();

        $qtyTaken = (float) ($logbook->quantity_taken ?? 0);
        if ($qtyTaken <= 0) {
            // Tetap hapus logbook, tapi tidak rollback bila qty tidak valid
            $logbook->delete();
            return redirect()->back()->with('success', 'Logbook history deleted.');
        }

        // Rollback berdasarkan bulan & tahun dari created_at logbook
        $date = $logbook->created_at ? Carbon::parse($logbook->created_at) : now();
        $month = $date->format('m');
        $year = $date->format('Y');

        $stockHistory = StockHistory::where('reagen_guid', $logbook->reagen_guid)
            ->where('organization_guid', $user->organization_guid)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($stockHistory) {
            $stockHistory->quantity = (float) $stockHistory->quantity + $qtyTaken;
            $stockHistory->quantity_out = max((float) $stockHistory->quantity_out - $qtyTaken, 0);
            $stockHistory->save();
        }

        // ✅ Update current stock (stock_reagens) so Reagent History "Current Stock" increases
        // Since original TAKE used FIFO by StockReagen.noCatalog, rollback will add back qty to earliest batch.
        $stockReagen = StockReagen::where('noCatalog', $logbook->noCatalog ?? null)
            ->where('organization_guid', $user->organization_guid)
            ->orderBy('created_at', 'asc')
            ->first();

        if ($stockReagen) {
            $stockReagen->quantity = (float) $stockReagen->quantity + $qtyTaken;
            $stockReagen->save();
        }

        $logbook->delete();

        return redirect()->back()->with('success', 'Logbook history deleted and stock returned.');
    }

    public function takeQRCode($id)
    {
        $user = auth()->user();

        $reagenIn = ReagenIn::where('organization_guid', $user->organization_guid)
            ->findOrFail($id);

        return view('logbook.logbook-qrcode', compact('reagenIn'));
    }

    // ✅ TAKE ADMIN: Parameter $guid
    public function takeAdmin($guid) // ✅ Ganti parameter menjadi $guid
    {
        $user = auth()->user();

        // ✅ Query menggunakan guid & organization_guid
        $reagen = Reagen::where('guid', $guid)
            ->where('organization_guid', $user->organization_guid)
            ->firstOrFail();

        // ✅ Filter analis berdasarkan organisasi user
        $analisList = DB::table('users')
            ->where('organization_guid', $user->organization_guid)
            ->pluck('name', 'id');

        return view('logbook.logbook-take-admin', compact('reagen', 'analisList'));
    }

    // ✅ STORE TAKE ADMIN: Gunakan organization_guid
    public function storeTakeAdmin(Request $request)
    {
        $user = auth()->user();

        $validatedData = $request->validate([
            'noCatalog' => 'required',
            'user_id' => 'required',
            'batch' => 'required',
            'quantity_taken' => 'required|numeric|min:1',
            'analis' => 'required|exists:users,id',
            'note' => 'nullable|string',
            'date' => 'required|date',
        ]);

        // ✅ Cek stok dengan filter organization_guid
        $availableStock = StockReagen::whereHas('reagen', function ($q) use ($user) {
            $q->where('organization_guid', $user->organization_guid);
        })->where('reagen_guid', $validatedData['reagen_guid'] ?? null)
            ->sum('quantity');

        if ($availableStock < $validatedData['quantity_taken']) {
            Alert::error('Failed', 'Please recheck the stock and the quantity being taken.')->showCloseButton();
            return redirect()->back();
        }

        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');

        // ✅ Update stock_histories dengan filter organization_guid
        $stockHistory = StockHistory::where('reagen_guid', $validatedData['reagen_guid'] ?? null)
            ->where('organization_guid', $user->organization_guid)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        if ($stockHistory) {
            DB::table('stock_histories')
                ->where('reagen_guid', $validatedData['reagen_guid'] ?? null)
                ->where('organization_guid', $user->organization_guid)
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->update([
                    'quantity' => $stockHistory->quantity - $validatedData['quantity_taken'],
                    'quantity_out' => $stockHistory->quantity_out + $validatedData['quantity_taken'],
                    'updated_at' => now(),
                ]);
        } else {
            StockHistory::create([
                'reagen_guid' => $validatedData['reagen_guid'] ?? null,
                'noCatalog' => $validatedData['noCatalog'],
                'quantity' => 0,
                'quantity_in' => 0,
                'quantity_out' => $validatedData['quantity_taken'],
                'month' => $currentMonth,
                'year' => $currentYear,
                'organization_guid' => $user->organization_guid, // ✅ Tambahkan
            ]);
        }

        // ✅ Simpan ke LogbookReagen
        LogbookReagen::create([
            'reagen_guid' => $validatedData['reagen_guid'] ?? null,
            'noCatalog' => $validatedData['noCatalog'],
            'user_id' => $validatedData['user_id'],
            'batch' => $validatedData['batch'],
            'quantity_taken' => $validatedData['quantity_taken'],
            'note' => $validatedData['note'],
            'organization_guid' => $user->organization_guid,
            'created_at' => $validatedData['date'],
        ]);

        // ✅ Update stock_reagens
        StockReagen::whereHas('reagen', function ($q) use ($user) {
            $q->where('organization_guid', $user->organization_guid);
        })->where('reagen_guid', $validatedData['reagen_guid'] ?? null)
            ->decrement('quantity', $validatedData['quantity_taken']);

        Alert::success('Success', 'Information has been successfully saved.');
        return redirect()->route('logbook.index');
    }
}
