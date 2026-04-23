<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Reagen;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // fungsi index
    public function index()
    {
        $user = auth()->user();
        $orders = Order::whereHas('user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->get();
        return view('order.order', compact('orders'));
    }

    public function newOrderForm()
    {
        return view('order.new-order-form');
    }

    public function eksistingOrderForm()
    {
        $user = auth()->user();
        $reagens = Reagen::whereHas('reagenIn.user', function ($q) use ($user) {
            $q->where('organization_id', $user->organization_id);
        })->get();
        return view('order.eksisting-order-form', compact('reagens'));
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            // Validasi form
            $validatedData = $request->validate([
                'noCatalog' => 'required|string',
                'nameReagen' => 'required|string',
                'merk' => 'required|string',
                'packSize' => 'required|string',
                'quantity' => 'required|integer',
                'status' => 'required',
            ]);

            // Set userId to current user
            $validatedData['userId'] = $user->id;

            // Simpan data ke dalam database menggunakan model Order
            Order::create($validatedData);

            Alert::success('Success', 'Order successfully!');

            return redirect()->route('order.index');
        } catch (\Exception $e) {
            // Log error
            \Log::error($e->getMessage());

            // Redirect back with an error message
            return redirect()->back()->with('error', 'Failed to create order.');
        }
    }


    public function viewOrder($id)
    {
        $order = Order::find($id);

        // Lakukan manipulasi waktu menggunakan Carbon
        $created_at = Carbon::parse($order->created_at);
        $daysPassed = $created_at->diffInDays(Carbon::now());

        return view('order.view-order', compact('order', 'daysPassed'));
    }

    public function update(Request $request, $id)
    {
        // Validasi formulir jika diperlukan
        $request->validate([
            'status' => 'required|in:0,1',
        ]);

        // Temukan order berdasarkan ID
        $order = Order::find($id);

        // Perbarui status berdasarkan data formulir
        $order->status = $request->input('status');
        $order->save();

        // Redirect kembali ke tampilan order atau tempat yang sesuai
        return redirect()->route('order.view', $id)->with('success', 'Order updated successfully');
    }

    public function destroy($id)
    {
        $item = Order::findOrFail($id);
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
