<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST ApiSetting Update ===\n\n";

try {
    $settings = App\Models\ApiSetting::first();
    
    if (!$settings) {
        echo "ERROR: No ApiSetting record found!\n";
        exit(1);
    }
    
    echo "Current record:\n";
    echo "  guid: " . $settings->guid . "\n";
    echo "  is_active: " . ($settings->is_active ? 'true' : 'false') . "\n";
    echo "  push_is_active: " . ($settings->push_is_active ? 'true' : 'false') . "\n";
    echo "  push_interval: " . $settings->push_interval . "\n";
    echo "  selected_tables: " . json_encode($settings->selected_tables) . "\n";
    echo "\n";

    // Test update
    $result = $settings->update([
        'gas_web_app_url' => $settings->gas_web_app_url,
        'is_active'       => true,
        'push_is_active'  => true,
        'push_interval'   => 'everyFiveMinutes',
        'selected_tables' => ['reagens', 'logbook_reagens'],
    ]);

    echo "Update result: " . ($result ? "SUCCESS ✅" : "FAILED ❌") . "\n";

    // Verify
    $fresh = App\Models\ApiSetting::first();
    echo "\nAfter update:\n";
    echo "  is_active: " . ($fresh->is_active ? 'true' : 'false') . "\n";
    echo "  push_is_active: " . ($fresh->push_is_active ? 'true' : 'false') . "\n";
    echo "  selected_tables: " . json_encode($fresh->selected_tables) . "\n";

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
}
