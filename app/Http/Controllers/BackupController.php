<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        return view('settings.backup');
    }

    public function downloadBackup(Request $request)
    {
        $user = auth()->user();
        $table = $request->input('table', 'all');
        $format = $request->input('format', 'csv');

        if ($table === 'all') {
            // For simplicity, we export main tables related to the organization
            return $this->exportAllTables($user->organization_guid);
        }

        return $this->exportSingleTable($table, $user->organization_guid);
    }

    private function exportSingleTable($tableName, $orgGuid)
    {
        if (!Schema::hasTable($tableName)) {
            return redirect()->back()->with('error', 'Table not found.');
        }

        // Only allow exporting organization-specific data if the column exists
        $query = DB::table($tableName);
        if (Schema::hasColumn($tableName, 'organization_guid')) {
            $query->where('organization_guid', $orgGuid);
        }

        $data = $query->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'No data found for this table.');
        }

        $filename = "backup_{$tableName}_" . Carbon::now()->format('Y-m-d_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = array_keys((array)$data[0]);

        $callback = function() use($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $row) {
                fputcsv($file, (array)$row);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportAllTables($orgGuid)
    {
        // Define key tables to export
        $tables = ['reagens', 'reagen_ins', 'logbook_reagens', 'stock_reagens', 'stock_histories'];
        $zip = new \ZipArchive();
        $zipFilename = "full_backup_rhinedottir_" . Carbon::now()->format('Y-m-d_His') . ".zip";
        $zipPath = storage_path($zipFilename);

        if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
            return redirect()->back()->with('error', 'Cannot create zip file.');
        }

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                $query = DB::table($tableName);
                if (Schema::hasColumn($tableName, 'organization_guid')) {
                    $query->where('organization_guid', $orgGuid);
                }
                $data = $query->get();
                
                if ($data->isNotEmpty()) {
                    $csvData = "";
                    $columns = array_keys((array)$data[0]);
                    
                    // Create CSV content in memory
                    $handle = fopen('php://memory', 'r+');
                    fputcsv($handle, $columns);
                    foreach ($data as $row) {
                        fputcsv($handle, (array)$row);
                    }
                    rewind($handle);
                    $csvData = stream_get_contents($handle);
                    fclose($handle);

                    $zip->addFromString("{$tableName}.csv", $csvData);
                }
            }
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
