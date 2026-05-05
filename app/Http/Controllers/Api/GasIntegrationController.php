<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GasIntegrationController extends Controller
{
    protected $allowedTables = [
        'reagens',
        'logbook_reagens',
        'stock_reagens',
        'reagens_in',
        'orders',
        'stock_opnames'
    ];

    public function exportData(Request $request, $table)
    {
        if (!in_array($table, $this->allowedTables)) {
            return response()->json([
                'success' => false,
                'message' => 'Table not allowed or does not exist.'
            ], 403);
        }

        try {
            // Retrieve data, ideally you'd paginate if data is very large,
            // but for simple GAS sync, a full dump or time-filtered dump is often expected.
            // You can add logic here to filter by ?updated_since=YYYY-MM-DD
            $query = DB::table($table);
            
            if ($request->has('updated_since')) {
                $query->where('updated_at', '>=', $request->updated_since);
            }

            $data = $query->get();

            return response()->json([
                'success' => true,
                'data' => $data,
                'table' => $table,
                'count' => $data->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
