<?php

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

use Illuminate\Support\Facades\Artisan;

try {
    echo "<h2>🧹 Clearing Laravel Caches Standalone Script</h2>";

    Artisan::call('route:clear');
    echo "<p>✅ Route Cache Cleared: " . Artisan::output() . "</p>";

    Artisan::call('config:clear');
    echo "<p>✅ Config Cache Cleared: " . Artisan::output() . "</p>";

    Artisan::call('view:clear');
    echo "<p>✅ View Cache Cleared: " . Artisan::output() . "</p>";

    Artisan::call('cache:clear');
    echo "<p>✅ Application Cache Cleared: " . Artisan::output() . "</p>";

    echo "<h3>🎉 All Laravel caches have been successfully cleared!</h3>";
} catch (\Exception $e) {
    echo "<h3 style='color:red;'>❌ Error: " . $e->getMessage() . "</h3>";
}
