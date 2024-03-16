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

        $logbook = LogbookReagen::all();
        $stockKosong = StockReagen::where('quantity', 0)->get();

        $reagenOrder = Order::all();

        $reagenED = ReagenIn::all();

        return view('dashboard.dashboard', compact('totalReagen', 'stockReagen', 'stockKosong', 'reagenOrder', 'reagenED'));
    }
}
