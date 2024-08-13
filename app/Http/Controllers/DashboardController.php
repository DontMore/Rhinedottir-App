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
    public function index(){
        // total reagen
        $totalReagen = Reagen::count();

        $currentMonth = Carbon::now()->month; // Get current month (1-12)
        $currentYear = Carbon::now()->year;

        $stockReagen = StockHistory::where('month', $currentMonth)
                                    ->where('year', $currentYear)
                                    ->first();

        $totalQuantity = StockHistory::where('month', $currentMonth)
                            ->where('year', $currentYear)
                            ->sum('quantity');
                                    
        $totalQuantityIn = StockHistory::where('month', $currentMonth)
                            ->where('year', $currentYear)
                            ->sum('quantity_in');

        $totalQuantityOut = StockHistory::where('month', $currentMonth)
                            ->where('year', $currentYear)
                            ->sum('quantity_out');

        $logbook = LogbookReagen::all();

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

        $reagenOrder = Order::all();

        $reagenED = ReagenIn::orderBy('expiredDate', 'asc')->take(10)->get();

        return view('dashboard.dashboard', compact('totalReagen', 'totalQuantity', 'totalQuantityIn', 'totalQuantityOut', 'zeroStockReagen', 'reagenOrder', 'reagenED', 'labels', 'data'));
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
        $reagents = DB::table('reagens')->select('noCatalog', 'nameReagen')->get();
        return response()->json($reagents);
    }
}