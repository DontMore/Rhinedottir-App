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
use App\Exports\ExpiredReagenExport;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(){
        $report = StockOpname::all();
        return view('report.report', compact('report'));
    }

    public function reportDetail(Request $request)
    {
        $user = auth()->user();

        $query = LogbookReagen::with(['reagen', 'user'])
            ->whereHas('user', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            });

        // Only apply date filters if dates are provided
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('reagen')) {
            $query->where('noCatalog', $request->reagen);
        }

        $logbooks = $query->orderBy('created_at', 'desc')->get();

        $reagens = Reagen::whereHas('reagenIn.user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->get();

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
        $user = auth()->user();

        $query = LogbookReagen::with(['reagen', 'user'])
            ->whereHas('user', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            });

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
        $user = auth()->user();

        // Get Reagen In data, filter by organization
        $reagenInQuery = ReagenIn::with(['reagen'])
            ->join('users', 'reagens_in.user_id', '=', 'users.id')
            ->where('users.organization_id', $user->organization_id)
            ->select(
                'reagens_in.created_at',
                'reagens_in.noCatalog',
                'reagens_in.Id',
                DB::raw('CAST(reagens_in.quantity AS SIGNED) as quantity'),
                DB::raw('NULL as description'),
                'users.name as user_name',
                DB::raw("'in' as transaction_type")
            );

        // Get Logbook (Reagen Out) data, filter by organization
        $reagenOutQuery = LogbookReagen::with(['reagen'])
            ->join('users', 'logbook_reagens.user_id', '=', 'users.id')
            ->where('users.organization_id', $user->organization_id)
            ->select(
                'logbook_reagens.created_at',
                'logbook_reagens.noCatalog',
                'logbook_reagens.id as Id',
                DB::raw('CAST(logbook_reagens.quantity_taken AS SIGNED) as quantity'),
                'logbook_reagens.note as description',
                'users.name as user_name',
                DB::raw("'out' as transaction_type")
            );

        // Apply filters to both queries
        if ($request->filled('start_date')) {
            $reagenInQuery->whereDate('reagens_in.created_at', '>=', $request->start_date);
            $reagenOutQuery->whereDate('logbook_reagens.created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $reagenInQuery->whereDate('reagens_in.created_at', '<=', $request->end_date);
            $reagenOutQuery->whereDate('logbook_reagens.created_at', '<=', $request->end_date);
        }
        if ($request->filled('reagen')) {
            $reagenInQuery->where('reagens_in.noCatalog', $request->reagen);
            $reagenOutQuery->where('logbook_reagens.noCatalog', $request->reagen);
        }

        // Combine and sort results
        $histories = $reagenInQuery->union($reagenOutQuery)
            ->orderBy('created_at', 'desc')
            ->get();

        // Recalculate summary with explicit casting
        $summary = [
            'total_in' => $histories->where('transaction_type', 'in')
                ->sum('quantity'),
            'total_out' => $histories->where('transaction_type', 'out')
                ->sum('quantity'),
            'per_reagen' => $histories->groupBy(function($item) {
                    return $item->reagen->noCatalog . '|' . $item->reagen->nameReagen;
                })
                ->map(function ($group) {
                    list($noCatalog, $nameReagen) = explode('|', $group->first()->reagen->noCatalog . '|' . $group->first()->reagen->nameReagen);
                    return [
                        'no_catalog' => $noCatalog,
                        'name' => $nameReagen,
                        'in' => $group->where('transaction_type', 'in')->sum('quantity'),
                        'out' => $group->where('transaction_type', 'out')->sum('quantity')
                    ];
                })->values()
        ];

        $reagens = Reagen::whereHas('reagenIn.user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->get();

        return view('report.historical-report', compact('histories', 'reagens', 'summary'));
    }

    public function reagenList()
    {
        $user = auth()->user();

        $reagens = Reagen::select(
            'noCatalog', 
            'nameReagen', 
            'merk', 
            'packSize'
        )
        ->whereHas('reagenIn.user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })
        ->withCount(['stocks as total_quantity' => function($query) {
            $query->select(DB::raw('SUM(quantity)'));
        }])
        ->orderBy('nameReagen')
        ->get();

        return view('report.reagen-list', compact('reagens'));
    }

    public function generateHistoricalPDF(Request $request)
    {
        $user = auth()->user();

        // Get Reagen In data, filter by organization
        $reagenInQuery = ReagenIn::with(['reagen'])
            ->join('users', 'reagens_in.user_id', '=', 'users.id')
            ->where('users.organization_id', $user->organization_id)
            ->select(
                'reagens_in.created_at',
                'reagens_in.noCatalog',
                'reagens_in.Id',
                DB::raw('CAST(reagens_in.quantity AS SIGNED) as quantity'),
                DB::raw('NULL as description'),
                'users.name as user_name',
                DB::raw("'in' as transaction_type")
            );

        // Get Logbook (Reagen Out) data, filter by organization
        $reagenOutQuery = LogbookReagen::with(['reagen'])
            ->join('users', 'logbook_reagens.user_id', '=', 'users.id')
            ->where('users.organization_id', $user->organization_id)
            ->select(
                'logbook_reagens.created_at',
                'logbook_reagens.noCatalog',
                'logbook_reagens.id as Id',
                DB::raw('CAST(logbook_reagens.quantity_taken AS SIGNED) as quantity'),
                'logbook_reagens.note as description',
                'users.name as user_name',
                DB::raw("'out' as transaction_type")
            );

        // Apply filters to both queries
        if ($request->has('start_date')) {
            $reagenInQuery->whereDate('reagens_in.created_at', '>=', $request->start_date);
            $reagenOutQuery->whereDate('logbook_reagens.created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $reagenInQuery->whereDate('reagens_in.created_at', '<=', $request->end_date);
            $reagenOutQuery->whereDate('logbook_reagens.created_at', '<=', $request->end_date);
        }
        if ($request->has('reagen') && $request->reagen) {
            $reagenInQuery->where('reagens_in.noCatalog', $request->reagen);
            $reagenOutQuery->where('logbook_reagens.noCatalog', $request->reagen);
        }

        // Combine both queries
        $histories = $reagenInQuery->union($reagenOutQuery)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate summary data
        $summary = [
            'total_in' => $histories->where('transaction_type', 'in')
                ->sum('quantity'),
            'total_out' => $histories->where('transaction_type', 'out')
                ->sum('quantity'),
            'per_reagen' => $histories->groupBy(function($item) {
                    return $item->reagen->noCatalog . '|' . $item->reagen->nameReagen;
                })
                ->map(function ($group) {
                    list($noCatalog, $nameReagen) = explode('|', $group->first()->reagen->noCatalog . '|' . $group->first()->reagen->nameReagen);
                    return [
                        'no_catalog' => $noCatalog,
                        'name' => $nameReagen,
                        'in' => $group->where('transaction_type', 'in')->sum('quantity'),
                        'out' => $group->where('transaction_type', 'out')->sum('quantity')
                    ];
                })->values()
        ];

        $data = [
            'histories' => $histories,
            'summary' => $summary,
            'start_date' => $request->start_date ?? 'All time',
            'end_date' => $request->end_date ?? 'All time',
            'generated_at' => now()->format('d/m/Y H:i:s')
        ];

        $pdf = PDF::loadView('report.historical-pdf', $data)->setPaper('a4', 'landscape');
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

    public function expiredReagen(Request $request)
    {
        $query = ReagenIn::with(['reagen'])
            ->whereNotNull('expiredDate')
            ->where(function($q) {
                $q->whereDate('expiredDate', '<=', now()->addMonths(3))
                  ->orWhereDate('expiredDate', '<', now());
            });

        if ($request->has('expired_status')) {
            if ($request->expired_status === 'expired') {
                $query->whereDate('expiredDate', '<', now());
            } elseif ($request->expired_status === 'near') {
                $query->whereDate('expiredDate', '>', now())
                      ->whereDate('expiredDate', '<=', now()->addMonths(3));
            }
        }

        $reagents = $query->orderBy('expiredDate')->get();
        
        return view('report.expired-reagen', compact('reagents'));
    }

    public function exportExpiredPDF(Request $request)
    {
        $query = ReagenIn::with(['reagen'])
            ->whereNotNull('expiredDate')
            ->where(function($q) {
                $q->whereDate('expiredDate', '<=', now()->addMonths(3))
                  ->orWhereDate('expiredDate', '<', now());
            });

        if ($request->has('expired_status')) {
            if ($request->expired_status === 'expired') {
                $query->whereDate('expiredDate', '<', now());
            } elseif ($request->expired_status === 'near') {
                $query->whereDate('expiredDate', '>', now())
                      ->whereDate('expiredDate', '<=', now()->addMonths(3));
            }
        }

        $reagents = $query->orderBy('expiredDate')->get();

        $data = [
            'reagents' => $reagents,
            'status_filter' => $request->expired_status ?? 'all',
            'generated_at' => now()->format('d/m/Y H:i:s')
        ];

        $pdf = PDF::loadView('report.expired-pdf', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('expired_reagen_report.pdf');
    }

    public function exportExpiredExcel(Request $request)
    {
        $filename = 'expired_reagen_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new ExpiredReagenExport($request->expired_status), $filename);
    }

    public function generateReagenListPDF()
    {
        $user = auth()->user();

        $reagens = Reagen::select('noCatalog', 'nameReagen', 'merk', 'packSize')
            ->whereHas('reagenIn.user', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            })
            ->withCount(['stocks as total_quantity' => function($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->get();

        $pdf = PDF::loadView('report.reagen-list-pdf', compact('reagens'))->setPaper('a4', 'landscape');
        return $pdf->stream('reagen_list.pdf');
    }

}
