<?php

namespace App\Http\Controllers;

use App\Models\LogbookReagen;
use Illuminate\Http\Request;
use App\Models\Reagen;
use App\Models\StockHistory;
use App\Models\StockReagen;
use App\Models\Order;
use App\Models\ReagenIn;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan statistik dan data terkait organisasi user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // ✅ Ambil user yang sedang login
        $user = auth()->user();

        // Hitung total reagen berdasarkan organization_guid user
        $totalReagen = Reagen::whereHas('reagenIn.user', function ($q) use ($user) {
            $q->where('organization_guid', $user->organization_guid);
        })->count();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Hitung total quantity stock berdasarkan organization_guid user
        $totalQuantity = StockReagen::whereHas('reagen.reagenIn.user', function ($q) use ($user) {
            $q->where('organization_guid', $user->organization_guid);
        })->sum('quantity');

        // Hitung total Reagen In bulan ini berdasarkan organization_guid user
        $totalQuantityIn = ReagenIn::whereHas('user', function ($q) use ($user) {
            $q->where('organization_guid', $user->organization_guid);
        })->whereMonth('created_at', $currentMonth)
          ->whereYear('created_at', $currentYear)
          ->sum('quantity');

        // Hitung total Reagen Taken (Out) bulan ini berdasarkan organization_guid user
        $totalQuantityOut = LogbookReagen::whereHas('user', function ($q) use ($user) {
            $q->where('organization_guid', $user->organization_guid);
        })->whereMonth('created_at', $currentMonth)
          ->whereYear('created_at', $currentYear)
          ->sum('quantity_taken');

        // Ambil data logbook berdasarkan organization_guid user
        $logbook = LogbookReagen::whereHas('user', function ($q) use ($user) {
            $q->where('organization_guid', $user->organization_guid);
        })->get();

        // Ambil data stock history dengan quantity = 0
        $zeroStockReagen = StockHistory::where('month', $currentMonth)
                            ->where('year', $currentYear)
                            ->where('quantity', 0)
                            ->get();

        // Ambil data chart untuk reagen spesifik (noCatalog = '1000142500')
        $stocks = StockHistory::select('month', 'year', 'quantity')
                            ->where('noCatalog', '=', '1000142500')
                            ->orderBy('year')
                            ->orderBy('month')
                            ->get();

        // Proses data untuk chart
        $chartData = [];
        foreach ($stocks as $stock) {
            $monthYear = $stock->month . ' ' . $stock->year;
            if (!isset($chartData[$monthYear])) {
                $chartData[$monthYear] = [];
            }
            $chartData[$monthYear][] = $stock->quantity;
        }

        $labels = array_keys($chartData);
        $data = array_values($chartData);

        // Ambil data order berdasarkan organization_guid user
        $reagenOrder = Order::whereHas('user', function ($q) use ($user) {
            $q->where('organization_guid', $user->organization_guid);
        })->get();

        // Ambil 10 reagen dengan expired date terdekat berdasarkan organization_guid user
        $reagenED = ReagenIn::whereHas('user', function ($q) use ($user) {
            $q->where('organization_guid', $user->organization_guid);
        })->orderBy('expiredDate', 'asc')->take(10)->get();

        // ✅ KEMBALIKAN VIEW DENGAN MENYERTAKAN $user
        return view('dashboard.dashboard', compact(
            'totalReagen',
            'totalQuantity',
            'totalQuantityIn',
            'totalQuantityOut',
            'zeroStockReagen',
            'reagenOrder',
            'reagenED',
            'labels',
            'data',
            'user'  // ✅ PENTING: Tambahkan 'user' agar tersedia di view
        ));
    }

    /**
     * Mendapatkan data chart untuk reagen berdasarkan noCatalog.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChartData(Request $request)
    {
        $noCatalog = $request->query('noCatalog');
        $currentYear = date('Y');

        // Generate array semua bulan dalam tahun berjalan
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $months[] = "{$currentYear}-{$month}";
        }

        // Query total quantity per bulan dari tabel reagens_in
        $query = DB::table('reagens_in')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->where('noCatalog', $noCatalog)
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Siapkan data final dengan semua bulan (termasuk yang nilainya 0)
        $data = [];
        foreach ($months as $month) {
            $data[] = [
                'month' => $month,
                'total_quantity' => isset($query[$month]) ? $query[$month]->total_quantity : 0
            ];
        }

        return response()->json($data);
    }

    /**
     * Mendapatkan daftar reagen yang tersedia untuk user berdasarkan organization_guid.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReagentList()
    {
        $user = auth()->user();

        $reagents = DB::table('reagens')
            ->join('reagens_in', 'reagens.noCatalog', '=', 'reagens_in.noCatalog')
            ->join('users', 'reagens_in.user_id', '=', 'users.id')
            ->where('users.organization_guid', $user->organization_guid)
            ->select('reagens.noCatalog', 'reagens.nameReagen')
            ->distinct()
            ->orderBy('reagens.nameReagen', 'asc')
            ->get();

        if ($reagents->isEmpty()) {
            Log::info('No reagents found in database for user', [
                'user_id' => $user->id,
                'organization_guid' => $user->organization_guid
            ]);
        }

        return response()->json($reagents);
    }

    /**
     * Mendapatkan data chart untuk logbook berdasarkan noCatalog.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLogbookChartData(Request $request)
    {
        $noCatalog = $request->query('noCatalog');
        $currentYear = date('Y');

        // Generate array semua bulan dalam tahun berjalan
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $months[] = "{$currentYear}-{$month}";
        }

        // Query total quantity_taken per bulan dari tabel logbook_reagens
        $query = DB::table('logbook_reagens')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(quantity_taken) as total_quantity')
            )
            ->where('noCatalog', $noCatalog)
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Siapkan data final dengan semua bulan (termasuk yang nilainya 0)
        $data = [];
        foreach ($months as $month) {
            $data[] = [
                'month' => $month,
                'total_quantity' => isset($query[$month]) ? $query[$month]->total_quantity : 0
            ];
        }

        return response()->json($data);
    }

    /**
     * Mendapatkan daftar reagen untuk chart logbook berdasarkan organization_guid.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLogbookReagentList()
    {
        $user = auth()->user();

        $reagents = DB::table('reagens')
            ->join('reagens_in', 'reagens.noCatalog', '=', 'reagens_in.noCatalog')
            ->join('users', 'reagens_in.user_id', '=', 'users.id')
            ->where('users.organization_guid', $user->organization_guid)
            ->select('reagens.noCatalog', 'reagens.nameReagen')
            ->distinct()
            ->orderBy('reagens.nameReagen', 'asc')
            ->get();

        if ($reagents->isEmpty()) {
            Log::info('No reagents found in database for logbook', [
                'user_id' => $user->id,
                'organization_guid' => $user->organization_guid
            ]);
        }

        return response()->json($reagents);
    }
}