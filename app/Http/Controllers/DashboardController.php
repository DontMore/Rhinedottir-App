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
}
