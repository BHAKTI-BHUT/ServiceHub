<?php
/**
 * Live Server Error Diagnostics & Validation Script
 * Upload this file to the same directory as 'readlog.php' on the live server.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0f172a; color: #cbd5e1; padding: 30px; }
    h2 { color: #f43f5e; border-bottom: 2px solid #334155; padding-bottom: 10px; }
    .status-box { background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .pass { color: #10b981; font-weight: bold; }
    .fail { color: #ef4444; font-weight: bold; }
    code { background: #020617; padding: 3px 6px; border-radius: 4px; color: #f43f5e; }
    pre { background: #020617; padding: 15px; border-radius: 8px; overflow-x: auto; color: #38bdf8; }
</style>";

echo "<h2>⚙ Bhandari Packers Live Server Diagnostics</h2>";

$basePath = '/home/u466475909/domains/bhandaripackersandmovers.in/ServiceHub';

// 1. Check Required Files
echo "<div class='status-box'>";
echo "<h3>1. Checking Uploaded Files</h3>";

$filesToCheck = [
    'Model' => $basePath . '/app/Models/RefundRequest.php',
    'Controller' => $basePath . '/app/Http/Controllers/Backend/Admin/RefundRequestController.php',
    'Blade View' => $basePath . '/resources/views/Backend/Admin/Refunds/Index.blade.php',
    'Routes Config' => $basePath . '/routes/Admin.php',
    'Sidebar Navigation' => $basePath . '/resources/views/partials/sidebar-menu-items.blade.php'
];

$allFilesExist = true;
foreach ($filesToCheck as $label => $path) {
    if (file_exists($path)) {
        echo "✅ {$label}: <span class='pass'>FOUND</span> (<code>" . basename($path) . "</code>)<br>";
    } else {
        echo "❌ {$label}: <span class='fail'>MISSING!</span> (Path expected: <code>{$path}</code>)<br>";
        $allFilesExist = false;
    }
}
echo "</div>";

// 2. Class Compile Test
if ($allFilesExist) {
    echo "<div class='status-box'>";
    echo "<h3>2. Class Compilation & Autoload Test</h3>";
    try {
        require_once $basePath . '/vendor/autoload.php';
        $app = require_once $basePath . '/bootstrap/app.php';
        
        // Boot kernel to load configs
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        $kernel->bootstrap();

        echo "✅ Laravel Application: <span class='pass'>BOOTED SUCCESSFULLY</span><br>";

        // Check if classes exist and load
        if (class_exists(\App\Models\RefundRequest::class)) {
            echo "✅ Model <code>App\\Models\\RefundRequest</code>: <span class='pass'>LOADED</span><br>";
        } else {
            echo "❌ Model <code>App\\Models\\RefundRequest</code>: <span class='fail'>COULD NOT BE RESOLVED</span><br>";
        }

        if (class_exists(\App\Http\Controllers\Backend\Admin\RefundRequestController::class)) {
            echo "✅ Controller <code>App\\Http\\Controllers\\Backend\\Admin\\RefundRequestController</code>: <span class='pass'>LOADED</span><br>";
        } else {
            echo "❌ Controller <code>App\\Http\\Controllers\\Backend\\Admin\\RefundRequestController</code>: <span class='fail'>COULD NOT BE RESOLVED</span><br>";
        }

        // Check Database table
        echo "<h3>3. Database Table Diagnostics</h3>";
        $requiredTables = ['users', 'sessions', 'cache', 'refund_requests'];
        foreach ($requiredTables as $table) {
            if (\Schema::hasTable($table)) {
                echo "✅ Database Table <code>{$table}</code>: <span class='pass'>EXISTS</span><br>";
            } else {er
                echo "❌ Database Table <code>{$table}</code>: <span class='fail'>MISSING! (Crucial for page loads)</span><br>";
            }
        }

        if (\Schema::hasTable('refund_requests')) {
            $count = \DB::table('refund_requests')->count();
            echo "ℹ Total request entries in DB: <strong>{$count}</strong><br>";
        }

        // Check Log writing
        echo "<h3>4. Writing to Log Test</h3>";
        try {
            \Illuminate\Support\Facades\Log::info("Bhandari Packers diagnostics run successfully.");
            echo "✅ Log writing: <span class='pass'>SUCCESSFUL</span> (Server can write to logs)<br>";
        } catch (\Throwable $logError) {
            echo "❌ Log writing: <span class='fail'>FAILED!</span> Reason: " . htmlspecialchars($logError->getMessage()) . "<br>";
            echo "👉 <em>Ensure that <code>ServiceHub/storage/</code> directory has recursive write permissions (chmod -R 775).</em><br>";
        }

    } catch (\Throwable $e) {
        echo "💥 <span class='fail'>FATAL ERROR DURING COMPILATION:</span><br>";
        echo "<pre>" . $e->getMessage() . "\n\nIn " . $e->getFile() . " on line " . $e->getLine() . "\n\nStack Trace:\n" . $e->getTraceAsString() . "</pre>";
    }
    echo "</div>";
} else {
    echo "<div class='status-box'>";
    echo "<h3 class='fail'>⚠️ Compilation aborted because some files are missing on the live server.</h3>";
    echo "Please upload all listed files to the server and refresh this page.";
    echo "</div>";
}

// 5. Check for stale cache files
echo "<div class='status-box'>";
echo "<h3>5. Cache File Check</h3>";
$cacheFiles = [
    'Config Cache' => $basePath . '/bootstrap/cache/config.php',
    'Route Cache'  => $basePath . '/bootstrap/cache/routes-v7.php',
    'Services Cache' => $basePath . '/bootstrap/cache/services.php',
    'Packages Cache' => $basePath . '/bootstrap/cache/packages.php',
];
foreach ($cacheFiles as $label => $path) {
    if (file_exists($path)) {
        $age = time() - filemtime($path);
        $ageStr = $age > 3600 ? round($age / 3600, 1) . ' hours ago' : round($age / 60) . ' mins ago';
        echo "⚠️ {$label}: <span style='color:#fbbf24;'>CACHED</span> (last modified: {$ageStr}) — <code>" . basename($path) . "</code><br>";
    } else {
        echo "✅ {$label}: <span class='pass'>NOT CACHED (OK)</span><br>";
    }
}
echo "</div>";

// 6. Simulated HTTP Request (catches the REAL 500 error)
echo "<div class='status-box'>";
echo "<h3>6. Simulated HTTP Request Test (/dashboard)</h3>";
try {
    $request = \Illuminate\Http\Request::create('/dashboard', 'GET');
    $app->instance('request', $request);
    
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request);
    $statusCode = $response->getStatusCode();
    
    if ($statusCode >= 500) {
        echo "❌ Response Status: <span class='fail'>HTTP {$statusCode}</span><br>";
        echo "<pre style='background:#020617;color:#f87171;padding:10px;font-size:12px;white-space:pre-wrap;max-height:400px;overflow:auto;'>" . htmlspecialchars(substr($response->getContent(), 0, 10000)) . "</pre>";
    } elseif ($statusCode >= 300 && $statusCode < 400) {
        $location = $response->headers->get('Location', 'unknown');
        echo "↗️ Response Status: <span style='color:#38bdf8;'>HTTP {$statusCode} REDIRECT</span> → <code>" . htmlspecialchars($location) . "</code><br>";
        echo "<span class='pass'>This is normal (redirects to login page).</span><br>";
    } else {
        echo "✅ Response Status: <span class='pass'>HTTP {$statusCode}</span><br>";
    }
    
    $kernel->terminate($request, $response);
} catch (\Throwable $reqError) {
    echo "💥 <span class='fail'>REQUEST CRASHED!</span><br>";
    echo "<pre style='background:#020617;color:#f87171;padding:10px;font-size:12px;white-space:pre-wrap;'>" . htmlspecialchars($reqError->getMessage()) . "\n\nIn " . $reqError->getFile() . " on line " . $reqError->getLine() . "\n\nStack Trace:\n" . htmlspecialchars($reqError->getTraceAsString()) . "</pre>";
}
echo "</div>";

// 7. Safe Laravel Log Viewer
echo "<div class='status-box'>";
echo "<h3>7. Laravel Log Viewer (Last 5 Errors)</h3>";
$logPath = $basePath . '/storage/logs/laravel.log';
if (file_exists($logPath)) {
    $fp = fopen($logPath, 'r');
    if ($fp) {
        $size = filesize($logPath);
        $readSize = min($size, 100000);
        if ($readSize > 0) {
            fseek($fp, -$readSize, SEEK_END);
            $content = fread($fp, $readSize);
            fclose($fp);
            $entries = preg_split('/\n(?=\[\d{4}-\d{2}-\d{2})/', trim($content));
            $lastEntries = array_slice($entries, -5);
            foreach (array_reverse($lastEntries) as $entry) {
                echo "<pre style='background:#020617;color:#f87171;border:1px solid #ef444433;padding:10px;margin-bottom:10px;font-size:12px;white-space:pre-wrap;'>" . htmlspecialchars(substr($entry, 0, 5000)) . "</pre>";
            }
        } else {
            echo "ℹ Log file is empty.";
        }
    } else {
        echo "❌ Unable to open log file.";
    }
} else {
    echo "❌ Log file not found at <code>$logPath</code>";
}
echo "</div>";
