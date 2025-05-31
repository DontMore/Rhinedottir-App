<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockReagen;
use App\Models\StockOpname;
use App\Models\Reagen;
use App\Models\StockHistory;
use App\Models\LogbookReagen;
use App\Models\User;
use App\Models\ReagenIn;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LogbookExport;
use App\Exports\HistoricalExport;
use App\Exports\StockOpnameExport;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(){
        $report = StockOpname::all();
        return view('report.report', compact('report'));
    }

    public function reportDetail(Request $request)
    {
        $query = LogbookReagen::with(['reagen', 'user']);

        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->has('reagen') && $request->reagen) {
            $query->where('noCatalog', $request->reagen);
        }

        $logbooks = $query->orderBy('created_at', 'desc')->get();
        $reagens = Reagen::all();

        return view('report.report-detail', compact('logbooks', 'reagens'));
    }

    public function generatePDF(Request $request)
    {
        $timezone = config('app.timezone');
        // Mengambil parameter bulan dan tahun dari request
        $month = $request->input('month') ?? Carbon::now()->format('m');
        // Mendapatkan nilai dari input 'month' atau menggunakan bulan saat ini jika tidak ada input
        $currentMonth = $request->input('month') ?? Carbon::now()->format('m');

        // Membuat objek Carbon dari string bulan
        $carbonMonth = Carbon::createFromFormat('m', $currentMonth);

        // Mengubah format menjadi nama bulan penuh
        $formattedMonth = $carbonMonth->format('F');

        $year = $request->input('year') ?? Carbon::now()->format('Y');

        // Mengambil semua data reagen beserta relasinya dengan filter bulan dan tahun
        $reagens = StockHistory::whereYear('created_at', $year)
                               ->whereMonth('created_at', $month)
                               ->get();

        $data = [
            'title' => 'Report Stock Reagen',
            'month' => $formattedMonth,
            'year' => $year,
            'reagens' => $reagens,
        ];

        $pdf = PDF::loadView('report.pdf', $data);

        return $pdf->stream('laporan.pdf');
    }

    public function generateLogbookPDF(Request $request)
    {
        $query = LogbookReagen::with(['reagen', 'user']);

        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->has('reagen') && $request->reagen) {
            $query->where('noCatalog', $request->reagen);
        }

        $logbooks = $query->orderBy('created_at', 'desc')->get();

        $data = [
            'title' => 'Logbook Report',
            'start_date' => $request->start_date ?? 'All time',
            'end_date' => $request->end_date ?? 'All time',
            'reagen_filter' => $request->reagen ? Reagen::where('noCatalog', $request->reagen)->first()->nameReagen : 'All reagens',
            'logbooks' => $logbooks,
            'generated_at' => Carbon::now()->format('d/m/Y H:i:s')
        ];

        $pdf = PDF::loadView('report.logbook-pdf', $data);
        return $pdf->stream('logbook_report.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filename = 'logbook_report_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(
            new LogbookExport(
                $request->start_date,
                $request->end_date,
                $request->reagen
            ), 
            $filename
        );
    }

    public function historicalReport(Request $request)
    {
        $query = ReagenIn::with(['reagen'])
            ->join('users', 'reagens_in.user_id', '=', 'users.id')
            ->select('reagens_in.*', 'users.name as user_name');

        if ($request->has('start_date')) {
            $query->whereDate('reagens_in.created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('reagens_in.created_at', '<=', $request->end_date);
        }
        if ($request->has('reagen') && $request->reagen) {
            $query->where('reagens_in.noCatalog', $request->reagen);
        }

        $histories = $query->orderBy('reagens_in.created_at', 'desc')->get();
        $reagens = Reagen::all();

        return view('report.historical-report', compact('histories', 'reagens'));
    }

    public function reagenList()
    {
        $reagens = Reagen::select('noCatalog', 'nameReagen', 'merk', 'packSize', 'orderLevel')
            ->withCount(['stocks as total_quantity' => function($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->get();

        return view('report.reagen-list', compact('reagens'));
    }

    public function generateHistoricalPDF(Request $request)
    {
        $query = ReagenIn::with(['reagen'])
            ->join('users', 'reagens_in.user_id', '=', 'users.id')
            ->select('reagens_in.*', 'users.name as user_name');

        if ($request->has('start_date')) {
            $query->whereDate('reagens_in.created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('reagens_in.created_at', '<=', $request->end_date);
        }
        if ($request->has('reagen') && $request->reagen) {
            $query->where('reagens_in.noCatalog', $request->reagen);
        }

        $histories = $query->orderBy('reagens_in.created_at', 'desc')->get();

        $data = [
            'histories' => $histories,
            'start_date' => $request->start_date ?? 'All time',
            'end_date' => $request->end_date ?? 'All time',
            'reagen_filter' => $request->reagen ? Reagen::where('noCatalog', $request->reagen)->first()->nameReagen : 'All reagens',
            'generated_at' => Carbon::now()->format('d/m/Y H:i:s')
        ];

        $pdf = PDF::loadView('report.historical-pdf', $data);
        return $pdf->stream('historical_report.pdf');
    }

    public function exportHistoricalExcel(Request $request)
    {
        $filename = 'historical_report_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(
            new HistoricalExport(
                $request->start_date,
                $request->end_date,
                $request->reagen
            ), 
            $filename
        );
    }

    public function stockOpnameReport(Request $request)
    {
        $month = $request->input('month') ?? Carbon::now()->format('m');
        $year = $request->input('year') ?? Carbon::now()->format('Y');

        $stocks = StockHistory::with(['reagen'])
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        return view('report.stock-opname-report', compact('stocks', 'month', 'year'));
    }

    public function generateStockOpnamePDF(Request $request)
    {
        $month = $request->input('month') ?? Carbon::now()->format('m');
        $year = $request->input('year') ?? Carbon::now()->format('Y');

        $stocks = StockHistory::with(['reagen'])
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $data = [
            'stocks' => $stocks,
            'month' => Carbon::createFromFormat('m', $month)->format('F'),
            'year' => $year,
            'generated_at' => Carbon::now()->format('d/m/Y H:i:s')
        ];

        $pdf = PDF::loadView('report.stock-opname-pdf', $data);
        return $pdf->stream('stock_opname_report.pdf');
    }

    public function exportStockOpnameExcel(Request $request)
    {
        $month = $request->input('month') ?? Carbon::now()->format('m');
        $year = $request->input('year') ?? Carbon::now()->format('Y');
        
        $filename = "stock_opname_report_{$year}_{$month}.xlsx";
        return Excel::download(new StockOpnameExport($month, $year), $filename);
    }

}
