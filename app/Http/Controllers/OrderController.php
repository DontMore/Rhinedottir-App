<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Reagen;
use App\Models\ReagenIn;
use App\Models\OrderRecommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;

class OrderController extends Controller
{
    /**
     * Menampilkan halaman index Order dengan rekomendasi
     */
    public function index()
    {
        $user = auth()->user();
        
        // Ambil Order History berdasarkan organization_guid
        $orders = Order::where('organization_guid', $user->organization_guid)
                       ->orderBy('created_at', 'desc')
                       ->get();

        $recommendations = collect();
        $processedGuids = [];
        $threeMonthsFromNow = now()->addMonths(3);

        // 🔍 LOGIC A: Stok <= Buffer
        $reagens = Reagen::with('stockReagen')
            ->where('organization_guid', $user->organization_guid)
            ->get();

        foreach ($reagens as $reagen) {
            $qty = $reagen->stockReagen ? $reagen->stockReagen->quantity : 0;
            if ($qty <= $reagen->buffer_stock) {
                $reason = $qty == 0 ? 'Stok Habis (0)' : "Stok Rendah (<= {$reagen->buffer_stock})";
                $reagen->setAttribute('recommendation_reason', $reason);
                $recommendations->push($reagen);
                $processedGuids[] = $reagen->guid;
            }
        }

        // 🔍 LOGIC B: Expired Batch (<= 3 Bulan) & Tidak Ada Batch Lebih Baru
        $expiringBatches = ReagenIn::whereHas('reagen', fn($q) => $q->where('organization_guid', $user->organization_guid))
                                   ->where('expiredDate', '<=', $threeMonthsFromNow)
                                   ->get();

        foreach ($expiringBatches as $batch) {
            if (in_array($batch->reagen_guid, $processedGuids)) continue;
            
            $hasNewerBatch = ReagenIn::where('reagen_guid', $batch->reagen_guid)
                                     ->where('expiredDate', '>', $batch->expiredDate)
                                     ->exists();
            
            if (!$hasNewerBatch) {
                $reagen = Reagen::find($batch->reagen_guid);
                if ($reagen) {
                    $diff = now()->diffInMonths(Carbon::parse($batch->expiredDate));
                    $reagen->setAttribute('recommendation_reason', "Expired ~{$diff} bln (Batch Tertua)");
                    $recommendations->push($reagen);
                    $processedGuids[] = $reagen->guid;
                }
            }
        }

        // 💾 Simpan ke Database sebagai JSON
        $dataToSave = $recommendations->map(fn($r) => [
            'noCatalog'    => $r->noCatalog,
            'nameReagen'   => $r->nameReagen,
            'merk'         => $r->merk,
            'packSize'     => $r->packSize,
            'stock'        => $r->stockReagen ? $r->stockReagen->quantity : 0,
            'buffer_stock' => $r->buffer_stock,
            'reason'       => $r->recommendation_reason
        ])->values();

        OrderRecommendation::updateOrCreate(
            ['organization_guid' => $user->organization_guid],
            [
                'recommendations' => $dataToSave,
                'generated_at'    => now()
            ]
        );

        return view('order.order', compact('orders', 'recommendations'));
    }

    /**
     * Form Order Baru
     */
    public function newOrderForm()
    {
        return view('order.new-order-form');
    }

    /**
     * Form Order Eksisting dengan pre-select via GUID
     */
    public function eksistingOrderForm(Request $request)
    {
        $user = auth()->user();
        
        $reagens = DB::table('reagens')
            ->join('reagens_in', 'reagens.noCatalog', '=', 'reagens_in.noCatalog')
            ->join('users', 'reagens_in.user_id', '=', 'users.id')
            ->where('users.organization_guid', $user->organization_guid)
            ->select('reagens.noCatalog', 'reagens.nameReagen', 'reagens.merk', 'reagens.packSize', 'reagens.guid as reagen_guid')
            ->distinct()
            ->orderBy('reagens.nameReagen', 'asc')
            ->get();

        $selectedCatalog = $request->input('catalog');
        $selectedGuid = $request->input('reagen_guid'); // ✅ Tambahan untuk GUID

        return view('order.eksisting-order-form', compact('reagens', 'selectedCatalog', 'selectedGuid'));
    }

    /**
     * Simpan Order Baru dengan GUID
     */
    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            
            $validatedData = $request->validate([
                'noCatalog' => 'required|string',
                'nameReagen' => 'required|string',
                'merk' => 'required|string',
                'packSize' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'status' => 'required|in:0,1',
            ]);

            // ✅ Inject data tenant & user
            $validatedData['user_guid'] = $user->guid;
            $validatedData['organization_guid'] = $user->organization_guid;
            $validatedData['userId'] = $user->id; // Legacy compatibility

            // ✅ GUID akan auto-generated oleh Model::boot() creating event
            Order::create($validatedData);
            
            // ✅ Refresh rekomendasi setelah order dibuat
            $this->refreshRecommendations($user->organization_guid);
            
            Alert::success('Success', 'Order berhasil dibuat!');
            return redirect()->route('order.index');
            
        } catch (\Exception $e) {
            \Log::error('Order Creation Failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat order. Silakan coba lagi.');
        }
    }

    /**
     * View Detail Order berdasarkan GUID
     */
    public function viewOrder($guid)
    {
        $user = auth()->user();
        
        $order = Order::where('guid', $guid)
                      ->where('organization_guid', $user->organization_guid)
                      ->firstOrFail();
        
        $created_at = $order->created_at ? Carbon::parse($order->created_at) : Carbon::now();
        $daysPassed = $created_at->diffInDays(Carbon::now());
        
        return view('order.view-order', compact('order', 'daysPassed'));
    }

    /**
     * Update Status Order berdasarkan GUID
     */
    public function update(Request $request, $guid)
    {
        $user = auth()->user();
        
        $request->validate([
            'status' => 'required|in:0,1',
        ]);

        $order = Order::where('guid', $guid)
                      ->where('organization_guid', $user->organization_guid)
                      ->firstOrFail();
        
        $order->status = $request->input('status');
        $order->save();
        
        // ✅ Refresh rekomendasi jika status berubah
        $this->refreshRecommendations($user->organization_guid);
        
        Alert::success('Updated', 'Order status has been updated!');
        return redirect()->route('order.view', $order->guid);
    }

    /**
     * Hapus Order berdasarkan GUID
     */
    public function destroy($guid)
    {
        $user = auth()->user();
        
        $item = Order::where('guid', $guid)
                     ->where('organization_guid', $user->organization_guid)
                     ->firstOrFail();
        
        $item->delete();
        
        Alert::success('Deleted', 'Item has been deleted successfully!');
        return redirect()->route('order.index');
    }

    /**
     * AJAX: Get Reagen Data by GUID (bukan noCatalog)
     */
    public function getReagenData($guid)
    {
        $user = auth()->user();
        
        $reagen = Reagen::where('guid', $guid)
                        ->where('organization_guid', $user->organization_guid)
                        ->first();
        
        if ($reagen) {
            return response()->json($reagen);
        }
        return response()->json(['error' => 'Reagen not found'], 404);
    }

    /**
     * Helper: Refresh rekomendasi & simpan ke JSON DB
     */
    public static function refreshRecommendations($organization_guid)
    {
        $recommendations = collect();
        $processedGuids = [];
        $threeMonthsFromNow = now()->addMonths(3);

        // Logic Stok
        $reagens = Reagen::with('stockReagen')->where('organization_guid', $organization_guid)->get();
        foreach ($reagens as $reagen) {
            $qty = $reagen->stockReagen ? $reagen->stockReagen->quantity : 0;
            if ($qty <= $reagen->buffer_stock) {
                $reagen->setAttribute('recommendation_reason', $qty == 0 ? 'Stok Habis (0)' : "Stok Rendah (<= {$reagen->buffer_stock})");
                $recommendations->push($reagen);
                $processedGuids[] = $reagen->guid;
            }
        }

        // Logic Expired
        $expiringBatches = ReagenIn::whereHas('reagen', fn($q) => $q->where('organization_guid', $organization_guid))
                                   ->where('expiredDate', '<=', $threeMonthsFromNow)
                                   ->get();
        foreach ($expiringBatches as $batch) {
            if (in_array($batch->reagen_guid, $processedGuids)) continue;
            $hasNewerBatch = ReagenIn::where('reagen_guid', $batch->reagen_guid)
                                     ->where('expiredDate', '>', $batch->expiredDate)
                                     ->exists();
            if (!$hasNewerBatch) {
                $reagen = Reagen::find($batch->reagen_guid);
                if ($reagen) {
                    $diff = now()->diffInMonths(Carbon::parse($batch->expiredDate));
                    $reagen->setAttribute('recommendation_reason', "Expired ~{$diff} bln (Batch Tertua)");
                    $recommendations->push($reagen);
                    $processedGuids[] = $reagen->guid;
                }
            }
        }

        // Simpan JSON
        OrderRecommendation::updateOrCreate(
            ['organization_guid' => $organization_guid],
            [
                'recommendations' => $recommendations->map(fn($r) => [
                    'noCatalog' => $r->noCatalog, 'nameReagen' => $r->nameReagen,
                    'merk' => $r->merk, 'packSize' => $r->packSize,
                    'stock' => $r->stockReagen ? $r->stockReagen->quantity : 0,
                    'buffer_stock' => $r->buffer_stock, 'reason' => $r->recommendation_reason
                ])->values(),
                'generated_at' => now()
            ]
        );
    }
}