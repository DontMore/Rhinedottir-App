<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\StockReagen;
use App\Models\Reagen;
use App\Models\ReagenIn;
use App\Models\LogbookReagen;
use App\Models\StockHistory;
use App\Models\StockOpname;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;
use PDF;
use Validator;


class StockOpnameController extends Controller
{
    private function getMonthName($monthNumber)
    {
        return Carbon::create()->month($monthNumber)->format('F');
    }

    public function index(Request $request)
    {
        $yearNow = Carbon::now()->year;

        // Mengecek apakah ada data dengan tahun saat ini
        $yearCheck = StockOpname::where('year', $yearNow)->exists();
        $yearData = StockOpname::where('year', $yearNow)->get();
        $month = $yearData->pluck('month')->toArray();

        $allMonth = range(1, 12);

        // Ada bulan yang tidak ada
        $missMonth = array_diff($allMonth, $month);

        // Menyimpan bulan yang kurang ke database
        foreach ($missMonth as $missMonth) {
            // Simpan data ke database
            StockOpname::create([
                'month' => $missMonth,
                'year' => $yearNow,
                'user_id' => auth()->user()->id,
                // Tambahkan atribut lain yang diperlukan sesuai struktur tabel `ReagenIn`
                // Misalnya: 'nameReagen' => 'Default Name', 'batch' => 'Default Batch'
            ]);
        }

        // Build query with filters
        $query = StockOpname::query();

        // Apply month filter if selected
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        // Apply year filter if selected
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Get filtered data with ordering and pagination
        $data = $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Add month names to the collection
        $data->getCollection()->transform(function ($item) {
            $item->month_name = $this->getMonthName($item->month);
            return $item;
        });

        return view('stock-opname.so-list', compact('data'));
    }

    public function soDetail(Request $request)
    {
        $user = auth()->user();
        $month = $request->input('month') ?? Carbon::now()->format('m');
        $year = $request->input('year') ?? Carbon::now()->format('Y');

        $noCatalog = StockHistory::where('year', $year)->where('month', $month)->pluck('noCatalog')->toArray();

        // ✅ PERBAIKAN: Ambil noCatalog DAN guid dari tabel reagens
        $reagents = DB::table('reagens')
            ->join('reagens_in', 'reagens.noCatalog', '=', 'reagens_in.noCatalog')
            ->join('users', 'reagens_in.user_id', '=', 'users.id')
            ->where('users.organization_guid', $user->organization_guid)
            ->select('reagens.noCatalog', 'reagens.guid as reagen_guid') // ✅ Ambil guid juga
            ->distinct()
            ->get();

        $missNoCatalog = $reagents->filter(function ($item) use ($noCatalog) {
            return !in_array($item->noCatalog, $noCatalog);
        });

        $previousMonth = $month == 1 ? 12 : $month - 1;
        $previousYear = $month == 1 ? $year - 1 : $year;

        foreach ($missNoCatalog as $item) {
            $noCatalog = $item->noCatalog;
            $reagenGuid = $item->reagen_guid; // ✅ Simpan guid dari query

            if (empty($noCatalog)) continue;

            $previousData = StockHistory::where('year', $previousYear)
                ->where('month', $previousMonth)
                ->where('noCatalog', $noCatalog)
                ->first();

            $reagenInQuantitySum = DB::table('reagens_in')
                ->join('users', 'reagens_in.user_id', '=', 'users.id')
                ->where('users.organization_guid', $user->organization_guid)
                ->where('reagens_in.noCatalog', $noCatalog)
                ->whereYear('reagens_in.created_at', $year)
                ->whereMonth('reagens_in.created_at', $month)
                ->sum('reagens_in.quantity');

            $reagenOutQuantitySum = DB::table('logbook_reagens')
                ->join('users', 'logbook_reagens.user_id', '=', 'users.id')
                ->where('users.organization_guid', $user->organization_guid)
                ->where('logbook_reagens.noCatalog', $noCatalog)
                ->whereYear('logbook_reagens.created_at', $year)
                ->whereMonth('logbook_reagens.created_at', $month)
                ->sum('logbook_reagens.quantity_taken');

            // ✅ updateOrCreate dengan menyertakan reagen_guid di array update/insert
            StockHistory::updateOrCreate(
                [
                    'month' => $month,
                    'year' => $year,
                    'noCatalog' => $noCatalog,
                ],
                [
                    'reagen_guid' => $reagenGuid, // ✅ Wajib disimpan agar relasi tidak null
                    'quantity' => $previousData ? $previousData->quantity_actual : 0,
                    'quantity_in' => $reagenInQuantitySum ?? 0,
                    'quantity_out' => $reagenOutQuantitySum ?? 0,
                    'quantity_actual' => 0,
                    'stock_opname' => 0,
                    'updated_at' => now(),
                ]
            );
        }

        $reagens = StockHistory::with('reagen')
            ->where('year', $year)
            ->where('month', $month)
            ->get()
            ->filter(fn($item) => $item->reagen !== null);

        return view('stock-opname.so-detail', compact('reagens', 'month', 'year'));
    }

    public function generatedSO(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            $month = $request->input('month') ?? Carbon::now()->format('m');
            $year = $request->input('year') ?? Carbon::now()->format('Y');

            $previousMonth = $month == 1 ? 12 : $month - 1;
            $previousYear = $month == 1 ? $year - 1 : $year;

            $existingStockHistories = StockHistory::where('year', $year)
                ->where('month', $month)
                ->get()
                ->keyBy('noCatalog');

            // ✅ PERBAIKAN: Ambil noCatalog DAN guid
            $reagents = DB::table('reagens')
                ->join('reagens_in', 'reagens.noCatalog', '=', 'reagens_in.noCatalog')
                ->join('users', 'reagens_in.user_id', '=', 'users.id')
                ->where('users.organization_guid', $user->organization_guid)
                ->select('reagens.noCatalog', 'reagens.guid as reagen_guid')
                ->distinct()
                ->get();

            foreach ($reagents as $item) {
                $noCatalog = $item->noCatalog;
                $reagenGuid = $item->reagen_guid;

                if (isset($existingStockHistories[$noCatalog]) && $existingStockHistories[$noCatalog]->stock_opname == 1) {
                    continue;
                }

                $previousData = StockHistory::where('year', $previousYear)
                    ->where('month', $previousMonth)
                    ->where('noCatalog', $noCatalog)
                    ->first();

                $quantityBefore = $previousData ? $previousData->quantity_actual : 0;

                $reagenInQuantitySum = DB::table('reagens_in')
                    ->join('users', 'reagens_in.user_id', '=', 'users.id')
                    ->where('users.organization_guid', $user->organization_guid)
                    ->where('reagens_in.noCatalog', $noCatalog)
                    ->whereYear('reagens_in.created_at', $year)
                    ->whereMonth('reagens_in.created_at', $month)
                    ->sum('reagens_in.quantity');

                $reagenOutQuantitySum = DB::table('logbook_reagens')
                    ->join('users', 'logbook_reagens.user_id', '=', 'users.id')
                    ->where('users.organization_guid', $user->organization_guid)
                    ->where('logbook_reagens.noCatalog', $noCatalog)
                    ->whereYear('logbook_reagens.created_at', $year)
                    ->whereMonth('logbook_reagens.created_at', $month)
                    ->sum('logbook_reagens.quantity_taken');

                // ✅ Simpan reagen_guid secara eksplisit
                StockHistory::updateOrCreate(
                    [
                        'month' => $month,
                        'year' => $year,
                        'noCatalog' => $noCatalog,
                    ],
                    [
                        'reagen_guid' => $reagenGuid, // ✅ Kunci relasi
                        'quantity' => $quantityBefore,
                        'quantity_in' => $reagenInQuantitySum ?? 0,
                        'quantity_out' => $reagenOutQuantitySum ?? 0,
                        'quantity_actual' => isset($existingStockHistories[$noCatalog]) ? $existingStockHistories[$noCatalog]->quantity_actual : 0,
                        'stock_opname' => isset($existingStockHistories[$noCatalog]) ? $existingStockHistories[$noCatalog]->stock_opname : 0,
                        'user_id' => $user->guid,
                        'updated_at' => now()
                    ]
                );
            }

            DB::commit();
            $reagens = StockHistory::with('reagen')->where('year', $year)->where('month', $month)->get();
            Alert::success('Success!', 'Stock data has been generated successfully');
            return view('stock-opname.so-detail', compact('reagens', 'month', 'year'));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Generate Stock Error: ' . $e->getMessage());
            Alert::error('Error!', 'Failed to generate stock data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating stock data');
        }
    }

    public function generateStock(Request $request)
    {
        try {
            $timezone = config('app.timezone');
            $month = $request->input('month') ?? Carbon::now()->format('m');
            $year = $request->input('year') ?? Carbon::now()->format('Y');

            // Get previous month/year
            $previousMonth = $month == 1 ? 12 : $month - 1;
            $previousYear = $month == 1 ? $year - 1 : $year;

            // Get all reagents with their relationships
            $reagens = StockHistory::with('reagen')
                ->where('year', $year)
                ->where('month', $month)
                ->get();

            // Get previous month's data for each reagent
            $reagens->each(function ($reagen) use ($previousYear, $previousMonth) {
                $previousData = StockHistory::where('year', $previousYear)
                    ->where('month', $previousMonth)
                    ->where('noCatalog', $reagen->noCatalog)
                    ->first();

                $reagen->quantity_before = $previousData ? $previousData->quantity_actual : 0;
                $reagen->expected_stock = ($reagen->quantity_before + $reagen->quantity_in) - $reagen->quantity_out;
                $reagen->difference = $reagen->quantity_actual - $reagen->expected_stock;
            });

            $data = [
                'title' => 'Report Stock Reagen',
                'month' => $month,
                'year' => $year,
                'reagens' => $reagens,
                'generated_at' => Carbon::now()->setTimezone($timezone)->format('d/m/Y H:i:s T')
            ];

            $pdf = PDF::loadView('stock-opname.stock-report', $data);
            return $pdf->stream('stock_opname_report_' . $year . '_' . $month . '.pdf');
        } catch (\Exception $e) {
            Log::error('Generate Stock Report Error: ' . $e->getMessage());
            Alert::error('Error!', 'Failed to generate stock report');
            return redirect()->back()->with('error', 'Error generating stock report');
        }
    }

    public function updateQuantities(Request $request)
    {
        try {
            DB::beginTransaction();

            $month = $request->input('month') ?? Carbon::now()->format('m');
            $year = $request->input('year') ?? Carbon::now()->format('Y');
            $quantityActualJson = $request->input('quantity_actual');

            if (!$quantityActualJson) {
                throw new \Exception('No quantity data provided');
            }

            $updatedQuantities = json_decode($quantityActualJson, true);

            foreach ($updatedQuantities as $id => $quantity) {
                $stockHistory = StockHistory::findOrFail($id);
                $stockHistory->quantity_actual = $quantity;
                $stockHistory->stock_opname = 1;
                $stockHistory->save();
            }

            $reagens = StockHistory::where('year', $year)
                ->where('month', $month)
                ->get();

            $dataSesuai = true;
            $totalSelisih = 0;

            foreach ($reagens as $reagen) {
                $expectedStock = ($reagen->quantity + $reagen->quantity_in) - $reagen->quantity_out;
                $actualStock = $reagen->quantity_actual;
                $selisih = $actualStock - $expectedStock;
                $totalSelisih += abs($selisih);
            }

            // Update or create stock opname record
            StockOpname::updateOrCreate(
                [
                    'month' => $month,
                    'year' => $year,
                    'user_id' => auth()->user()->id
                ],
                [
                    'status' => ($totalSelisih == 0) ? 1 : 0,
                    'updated_at' => now()
                ]
            );

            DB::commit();
            Alert::success('Success!', 'Data Stock Opname berhasil disimpan');
            return redirect()->back()->with('success', 'Stock opname updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Stock Opname Update Error: ' . $e->getMessage());
            Alert::error('Error!', 'Terjadi kesalahan saat menyimpan data');
            return redirect()->back()->with('error', 'Error updating stock opname');
        }
    }

    // untuk mengambil data reagen untuk modal
    public function getReagen($id, Request $request)
    {
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun');

        Log::channel('debug')->info("Parameter bulan: $bulan, tahun: $tahun, id: $id");

        if (!$bulan || !$tahun) {
            Log::channel('debug')->error("Parameter bulan atau tahun tidak ditemukan");
            return response()->json([
                'error' => 'Parameter bulan dan tahun wajib diisi'
            ], 400);
        }

        $stockHistory = StockHistory::with('reagen')
            ->where('id', $id)
            ->where('month', $bulan)
            ->where('year', $tahun)
            ->first();

        Log::channel('debug')->info("Query result: " . ($stockHistory ? $stockHistory->toJson() : 'No Data Found'));

        if (!$stockHistory) {
            Log::channel('debug')->warning("Data tidak ditemukan untuk ID: $id, Bulan: $bulan, Tahun: $tahun");
            return response()->json([
                'error' => 'Data tidak ditemukan untuk ID, bulan, dan tahun yang diberikan'
            ], 404);
        }

        $reagen = $stockHistory->reagen;
        Log::channel('debug')->info("Reagen data: " . $reagen->toJson());

        return response()->json([
            'stockHistory' => $stockHistory,
            'reagen' => $reagen
        ]);
    }

    public function update(Request $request)
    {
        // Validasi data yang diterima dari formulir jika diperlukan
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity_actual' => 'required|numeric',
            'catatan' => 'nullable|string',
            'status' => 'nullable|string',
            'user_id' => 'required',
        ]);

        // Periksa apakah validasi gagal
        if ($validator->fails()) {
            // Tampilkan semua data yang divalidasi dan pesan kesalahan validasi pada konsol
            dd($request->all(), $validator->errors()->all());
            // Mengembalikan respons dengan kesalahan validasi
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Jika validasi berhasil, lanjutkan dengan logika pembaruan
        // Update data
        $updateResult = DB::table('stock_histories')
            ->where('id',  $request->id)
            ->update([
                'quantity_actual' => $request->quantity_actual,
                'catatan' => $request->catatan,
                'status' => $request->status,
                'user_id' => $request->user_id,
                'stock_opname' => 1,
                'updated_at' => now(),
            ]);

        // Mengembalikan respons dengan data yang diperbarui
        return response()->json(['success' => 'Stock history updated successfully'], 200);
    }
}
