<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GasPushCommand extends Command
{
    protected $signature = 'gas:push';
    protected $description = 'Push data to Google Sheets via GAS Web App';

    protected $allowedTables = [
        'reagens',
        'logbook_reagens',
        'stock_reagens',
        'reagens_in',
        'orders',
        'stock_opnames',
        'order_recommendations'
    ];

    public function handle()
    {
        $settings = ApiSetting::first();
        if (!$settings || !$settings->push_is_active || !$settings->gas_web_app_url) {
            $this->info('GAS Push is disabled or not configured.');
            return;
        }

        // ✅ PERBAIKAN: Baca tabel yang dipilih dari database
        $selectedTables = $settings->selected_tables ?? [];
        
        // Jika selected_tables kosong, kirim semua tabel (backward compatibility)
        $tablesToPush = empty($selectedTables) 
            ? $this->allowedTables 
            : array_intersect($selectedTables, $this->allowedTables);

        if (empty($tablesToPush)) {
            $this->warn('No valid tables selected for push.');
            return;
        }

        $this->info('Starting GAS Push for tables: ' . implode(', ', $tablesToPush));
        
        try {
            foreach ($tablesToPush as $table) {
                $this->info("Pushing table: {$table}");
                
                // Khusus untuk order_recommendations, decode JSON dulu
                if ($table === 'order_recommendations') {
                    $rows = DB::table($table)->get();
                    $flatData = [];
                    foreach ($rows as $row) {
                        if (!empty($row->recommendations)) {
                            $decoded = json_decode($row->recommendations, true);
                            if (is_array($decoded)) {
                                $flatData = array_merge($flatData, $decoded);
                            }
                        }
                    }
                    $data = $flatData;
                } else {
                    $data = DB::table($table)->get();
                }

                $response = Http::post($settings->gas_web_app_url, [
                    'token'     => $settings->api_token,
                    'table'     => $table,
                    'payload'   => $data,
                    'pushed_at' => now()->toDateTimeString(),
                ]);

                if ($response->successful()) {
                    $this->info("✅ Successfully pushed {$table}.");
                } else {
                    $this->error("❌ Failed to push {$table}: " . $response->body());
                    Log::error("GAS Push Failed for {$table}: " . $response->body());
                }
            }

            $settings->update(['last_push_at' => now()]);
            $this->info('🎉 GAS Push completed successfully.');
        } catch (\Exception $e) {
            $this->error('💥 GAS Push failed: ' . $e->getMessage());
            Log::error('GAS Push Exception: ' . $e->getMessage());
        }
    }
}