<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Reagen;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // ✅ Tambahkan ini jika belum ada

class OrderController extends Controller
{
    // fungsi index
    public function index()
    {
        $user = auth()->user();
        $orders = Order::whereHas('user', function ($q) use ($user) {
            $q->where('organization_guid', $user->organization_guid);
        })->get();

        // Rekomendasi Order: Stok <= Buffer Stock
        $recommendations = Reagen::with('stockReagen')
            ->where('organization_guid', $user->organization_guid)
            ->where('buffer_stock', '>', 0)
            ->get()
            ->filter(function ($reagen) {
                $currentStock = $reagen->stockReagen ? $reagen->stockReagen->quantity : 0;
                return $currentStock <= $reagen->buffer_stock;
            });

        return view('order.order', compact('orders', 'recommendations'));
    }

    public function newOrderForm()
    {
        return view('order.new-order-form');
    }

    public function eksistingOrderForm()
    {
        $user = auth()->user();

        // ✅ Gunakan Query Builder agar lebih stabil & menghindari masalah relasi Eloquent
        $reagens = DB::table('reagens')
            ->join('reagens_in', 'reagens.noCatalog', '=', 'reagens_in.noCatalog')
            ->join('users', 'reagens_in.user_id', '=', 'users.id')
            ->where('users.organization_guid', $user->organization_guid)
            ->select('reagens.noCatalog', 'reagens.nameReagen', 'reagens.merk', 'reagens.packSize')
            ->distinct()
            ->orderBy('reagens.nameReagen', 'asc')
            ->get();

        return view('order.eksisting-order-form', compact('reagens'));
    }

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
                'status' => 'required',
            ]);

            // ✅ Inject data tenant & user secara AMAN dari session
            $validatedData['userId'] = $user->id;               // Legacy/compatibility
            $validatedData['user_guid'] = $user->guid;          // ✅ Baru
            $validatedData['organization_guid'] = $user->organization_guid; // ✅ Baru

            Order::create($validatedData);

            Alert::success('Success', 'Order berhasil dibuat!');
            return redirect()->route('order.index');
        } catch (\Exception $e) {
            \Log::error('Order Creation Failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat order. Silakan coba lagi.');
        }
    }


    // ✅ Method viewOrder
    public function viewOrder($guid)
    {
        $user = auth()->user();

        $order = Order::where('guid', $guid)
            ->whereHas('user', function ($q) use ($user) {
                $q->where('organization_guid', $user->organization_guid);
            })
            ->firstOrFail(); // Throw 404 jika tidak ditemukan

        $created_at = $order->created_at ? Carbon::parse($order->created_at) : Carbon::now();
        $daysPassed = $created_at->diffInDays(Carbon::now());

        return view('order.view-order', compact('order', 'daysPassed'));
    }

    // ✅ Method update
    public function update(Request $request, $guid)
    {
        $user = auth()->user();

        $request->validate([
            'status' => 'required|in:0,1',
        ]);

        $order = Order::where('guid', $guid)
            ->whereHas('user', function ($q) use ($user) {
                $q->where('organization_guid', $user->organization_guid);
            })
            ->firstOrFail();

        $order->status = $request->input('status');
        $order->save();

        Alert::success('Updated', 'Order status has been updated!');
        return redirect()->route('order.view', $order->guid);
    }

    // ✅ Method destroy
    public function destroy($guid)
    {
        $user = auth()->user();

        $item = Order::where('guid', $guid)
            ->whereHas('user', function ($q) use ($user) {
                $q->where('organization_guid', $user->organization_guid);
            })
            ->firstOrFail();

        $item->delete();

        Alert::success('Deleted', 'Item has been deleted successfully!');
        return redirect()->route('order.index');
    }

    public function getReagenData($noCatalogUtama)
    {
        $user = auth()->user();
        $reagen = Reagen::whereHas('reagenIn.user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->where('noCatalog', $noCatalogUtama)->first();

        if ($reagen) {
            return response()->json($reagen);
        } else {
            return response()->json(['error' => 'Reagen not found'], 404);
        }
    }
}
