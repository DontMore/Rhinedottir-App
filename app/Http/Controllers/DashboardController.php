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
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Hitung total reagen berdasarkan organization_guid user
        $totalReagen = Reagen::where('organization_guid', $user->organization_guid)->count();

        // Hitung total quantity stock berdasarkan organization_guid user
        $totalQuantity = StockReagen::where('organization_guid', $user->organization_guid)->sum('quantity');

        // Hitung total Reagen In bulan ini
        $totalQuantityIn = ReagenIn::where('organization_guid', $user->organization_guid)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('quantity');

        // Hitung total Reagen Taken (Out) bulan ini
        $totalQuantityOut = LogbookReagen::where('organization_guid', $user->organization_guid)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('quantity_taken');

        // 🔥 QUERY BARU: Hitung pemakaian per bulan (12 bulan terakhir) untuk Grafik Deviasi
        $months = [];
        $monthLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('Y-m');
            $monthLabels[] = $date->format('M Y');
        }

        $monthlyUsageRaw = LogbookReagen::where('organization_guid', $user->organization_guid)
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(quantity_taken) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyActualUsage = [];
        foreach ($months as $m) {
            $monthlyActualUsage[] = (float) ($monthlyUsageRaw[$m] ?? 0);
        }

        // Ambil data logbook, stock history, order, dll (tetap sama)
        $logbook = LogbookReagen::where('organization_guid', $user->organization_guid)->get();

        $zeroStockReagen = StockReagen::where('organization_guid', $user->organization_guid)
            ->where('quantity', 0)
            ->get();

        $reagenOrder = Order::where('organization_guid', $user->organization_guid)->get();

        $reagenED = ReagenIn::where('organization_guid', $user->organization_guid)
            ->orderBy('expiredDate', 'asc')
            ->take(10)
            ->get();

        // ✅ RETURN VIEW DENGAN DATA BARU $monthlyActualUsage
        return view('dashboard.dashboard', compact(
            'totalReagen',
            'totalQuantity',
            'totalQuantityIn',
            'totalQuantityOut',
            'zeroStockReagen',
            'reagenOrder',
            'reagenED',
            'monthlyActualUsage',
            'monthLabels',
            'user'
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
            ->where('organization_guid', $user->organization_guid)
            ->select('noCatalog', 'nameReagen')
            ->distinct()
            ->orderBy('nameReagen', 'asc')
            ->get();

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

    public function getDeviationData(Request $request)
    {
        $noCatalog = $request->query('noCatalog');
        $user = auth()->user();
        
        $months = [];
        $labels = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('Y-m');
            $labels[] = $date->format('M Y');
        }

        $query = LogbookReagen::where('organization_guid', $user->organization_guid)
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth());

        if ($noCatalog) {
            $query->where('noCatalog', $noCatalog);
        }

        $monthlyUsageRaw = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(quantity_taken) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $data = [];
        foreach ($months as $m) {
            $data[] = (float) ($monthlyUsageRaw[$m] ?? 0);
        }

        return response()->json([
            'labels' => $labels,
            'values' => $data
        ]);
    }

    public function getBatchExpiryData(Request $request)
    {
        $noCatalog = $request->query('noCatalog');
        $user = auth()->user();

        if (!$noCatalog) {
            return response()->json([]);
        }

        $batches = ReagenIn::where('organization_guid', $user->organization_guid)
            ->where('noCatalog', $noCatalog)
            ->select('batch', 'expiredDate', 'quantity')
            ->orderBy('expiredDate', 'asc')
            ->get()
            ->map(function ($item) {
                $now = Carbon::now();
                $ed = Carbon::parse($item->expiredDate);
                $monthsRemaining = $now->diffInMonths($ed, false);
                
                return [
                    'batch' => $item->batch,
                    'expiredDate' => $item->expiredDate,
                    'formattedED' => $ed->format('d M Y'),
                    'monthsRemaining' => $monthsRemaining,
                    'status' => $monthsRemaining <= 0 ? 'Expired' : ($monthsRemaining <= 3 ? 'Warning' : 'Safe'),
                    'quantity' => $item->quantity
                ];
            });

        return response()->json($batches);
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
            ->where('organization_guid', $user->organization_guid)
            ->select('noCatalog', 'nameReagen')
            ->distinct()
            ->orderBy('nameReagen', 'asc')
            ->get();

        return response()->json($reagents);
    }
}
