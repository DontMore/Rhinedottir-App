<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockReagen;
use App\Models\StockOpname;
use App\Models\Reagen;
use App\Models\StockHistory;
use Carbon\Carbon;
use PDF;

class ReportController extends Controller
{
    public function index(){
        $report = StockOpname::all();
        return view('report.report-list', compact('report'));
    }

    public function reportDetail(Request $request)
    {
        $timezone = config('app.timezone');
        // Mengambil parameter bulan dan tahun dari request
        $month = $request->input('month') ?? Carbon::now()->format('m');
        $year = $request->input('year') ?? Carbon::now()->format('Y');

        // Mengambil semua data reagen beserta relasinya dengan filter bulan dan tahun
        $reagens = StockHistory::whereYear('created_at', $year)
                            ->whereMonth('created_at', $month)
                            ->get();

        return view('report.report', compact('reagens', 'month', 'year'));
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

}
