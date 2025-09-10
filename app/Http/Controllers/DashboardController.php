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

class DashboardController extends Controller
{
    // index
    public function index()
    {
        $user = auth()->user();

        $totalReagen = Reagen::whereHas('reagenIn.user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->count();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Get total stock quantity, filter by organization
        $totalQuantity = StockReagen::whereHas('reagen.reagenIn.user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->sum('quantity');

        // Calculate total Reagen In for current month, filter by organization
        $totalQuantityIn = ReagenIn::whereHas('user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->whereMonth('created_at', $currentMonth)
          ->whereYear('created_at', $currentYear)
          ->sum('quantity');

        // Calculate total Reagen Taken for current month, filter by organization
        $totalQuantityOut = LogbookReagen::whereHas('user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->whereMonth('created_at', $currentMonth)
          ->whereYear('created_at', $currentYear)
          ->sum('quantity_taken');

        $logbook = LogbookReagen::whereHas('user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->get();

        $zeroStockReagen = StockHistory::where('month', $currentMonth)
                            ->where('year', $currentYear)
                            ->where('quantity', 0)
                            ->get();

        $stocks = StockHistory::select('month', 'year', 'quantity')
                            ->where('noCatalog', '=', '1000142500') // Ubah sesuai nama kolom yang sesuai
                            ->orderBy('year')
                            ->orderBy('month')
                            ->get();

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

        $reagenOrder = Order::whereHas('user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->get();

        $reagenED = ReagenIn::whereHas('user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->orderBy('expiredDate', 'asc')->take(10)->get();

        return view('dashboard.dashboard', compact(
            'totalReagen',
            'totalQuantity',
            'totalQuantityIn',
            'totalQuantityOut',
            'zeroStockReagen',
            'reagenOrder',
            'reagenED',
            'labels',
            'data'
        ));
    }

    public function getChartData(Request $request)
    {
        $noCatalog = $request->query('noCatalog');
        $currentYear = date('Y');

        // Generate an array of all months in the current year
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $months[] = "{$currentYear}-{$month}";
        }

        // Query to get the total quantities per month
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

        // Initialize an array to hold the final data with all months
        $data = [];
        foreach ($months as $month) {
            $data[] = [
                'month' => $month,
                'total_quantity' => isset($query[$month]) ? $query[$month]->total_quantity : 0
            ];
        }

        return response()->json($data);
    }

    public function getReagentList()
    {
        $user = auth()->user();

        $reagents = DB::table('reagens')
            ->join('reagens_in', 'reagens.noCatalog', '=', 'reagens_in.noCatalog')
            ->join('users', 'reagens_in.user_id', '=', 'users.id')
            ->where('users.organization_id', $user->organization_id)
            ->select('reagens.noCatalog', 'reagens.nameReagen')
            ->distinct()
            ->orderBy('reagens.nameReagen', 'asc')  // Sort alphabetically by name
            ->get();

        if ($reagents->isEmpty()) {
            \Log::info('No reagents found in database');
        }

        return response()->json($reagents);
    }

    // chart logbook
    public function getLogbookChartData(Request $request)
    {
        $noCatalog = $request->query('noCatalog');
        $currentYear = date('Y');

        // Generate an array of all months in the current year
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $months[] = "{$currentYear}-{$month}";
        }

        // Query to get the total quantity_taken per month
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

        // Initialize an array to hold the final data with all months
        $data = [];
        foreach ($months as $month) {
            $data[] = [
                'month' => $month,
                'total_quantity' => isset($query[$month]) ? $query[$month]->total_quantity : 0
            ];
        }

        return response()->json($data);
    }

    public function getLogbookReagentList()
    {
        $user = auth()->user();

        $reagents = DB::table('reagens')
            ->join('reagens_in', 'reagens.noCatalog', '=', 'reagens_in.noCatalog')
            ->join('users', 'reagens_in.user_id', '=', 'users.id')
            ->where('users.organization_id', $user->organization_id)
            ->select('reagens.noCatalog', 'reagens.nameReagen')
            ->distinct()
            ->orderBy('reagens.nameReagen', 'asc')  // Sort alphabetically by name
            ->get();

        if ($reagents->isEmpty()) {
            \Log::info('No reagents found in database');
        }

        return response()->json($reagents);
    }
}