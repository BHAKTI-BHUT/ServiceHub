<?php
// Simple, standalone error viewer
error_reporting(E_ALL);
ini_set('display_errors', 1);

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo "<div style='background:#7f1d1d;color:#fca5a5;padding:20px;font-family:monospace;font-size:16px;border-radius:8px;'>";
        echo "<h2>💥 FATAL PHP ERROR DETECTED</h2>";
        echo "<strong>Message:</strong> " . htmlspecialchars($error['message']) . "<br><br>";
        echo "<strong>File:</strong> " . htmlspecialchars($error['file']) . "<br>";
        echo "<strong>Line:</strong> " . $error['line'] . "<br>";
        echo "</div>";
    }
});

echo "<div style='background:#0f172a;color:#e2e8f0;padding:25px;font-family:monospace;line-height:1.6;'>";
echo "<h1 style='color:#38bdf8;'>🔍 Direct Laravel Error Tester</h1>";

$laravelPath = '/home/u466475909/domains/bhandaripackersandmovers.in/ServiceHub';

echo "<h3>Step 1: Check .env & Config Cache</h3>";
$cacheFile = $laravelPath . '/bootstrap/cache/config.php';
if (file_exists($cacheFile)) {
    echo "⚠️ <strong style='color:#f87171;'>ALERT: bootstrap/cache/config.php exists! Deleting it now...</strong><br>";
    @unlink($cacheFile);
    echo file_exists($cacheFile) ? "❌ Failed to delete config.php. Please delete manually!" : "✅ Successfully deleted config.php!<br>";
} else {
    echo "✅ No bootstrap/cache/config.php found.<br>";
}

echo "<h3>Step 2: Autoload Composer</h3>";
if (file_exists($laravelPath . '/vendor/autoload.php')) {
    require $laravelPath . '/vendor/autoload.php';
    echo "✅ Autoload loaded.<br>";
} else {
    echo "❌ vendor/autoload.php missing!<br>";
    exit;
}

echo "<h3>Step 3: Bootstrap Application</h3>";
try {
    $app = require_once $laravelPath . '/bootstrap/app.php';
    echo "✅ App bootstrapped successfully.<br>";
} catch (\Throwable $e) {
    echo "<div style='background:#450a0a;color:#fca5a5;padding:15px;'>";
    echo "❌ Bootstrap Crash: " . htmlspecialchars($e->getMessage()) . "<br>File: " . $e->getFile() . ":" . $e->getLine();
    echo "</div>";
    exit;
}

echo "<h3>Step 4: Execute HTTP Kernel Request (The Main Page Render)</h3>";
try {
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $request = \Illuminate\Http\Request::capture();
    $response = $kernel->handle($request);
    
    echo "HTTP Status Code: <strong>" . $response->getStatusCode() . "</strong><br>";
    
    if ($response->getStatusCode() == 500) {
        echo "<div style='background:#450a0a;color:#fca5a5;padding:15px;margin-top:10px;'>";
        echo "❌ Response was 500 Internal Error!<br>";
        echo "Content preview:<br><pre>" . htmlspecialchars(substr($response->getContent(), 0, 1000)) . "</pre>";
        echo "</div>";
    } else {
        echo "✅ Response Status " . $response->getStatusCode() . "!<br>";
    }
} catch (\Throwable $e) {
    echo "<div style='background:#450a0a;color:#fca5a5;padding:20px;border-radius:8px;margin-top:10px;'>";
    echo "<h2 style='color:#ef4444;'>❌ KERNEL CRASH DETECTED!</h2>";
    echo "<strong>Exception Type:</strong> " . get_class($e) . "<br>";
    echo "<strong>Error Message:</strong> <span style='font-size:18px;color:#fff;'>" . htmlspecialchars($e->getMessage()) . "</span><br><br>";
    echo "<strong>File:</strong> " . $e->getFile() . " (Line " . $e->getLine() . ")<br><br>";
    echo "<strong>Stack Trace:</strong><pre style='background:#1e1e1e;color:#cbd5e1;padding:10px;overflow-x:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "</div>";
