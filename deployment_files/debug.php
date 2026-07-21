<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo "<div style='background:#7f1d1d;color:#fca5a5;padding:20px;font-family:monospace;font-size:16px;border-radius:8px;margin-top:10px;'>";
        echo "<h2>💥 FATAL PHP ERROR DETECTED</h2>";
        echo "<strong>Message:</strong> " . htmlspecialchars($error['message']) . "<br><br>";
        echo "<strong>File:</strong> " . htmlspecialchars($error['file']) . "<br>";
        echo "<strong>Line:</strong> " . $error['line'] . "<br>";
        echo "</div>";
    }
});

echo "<div style='font-family:monospace;background:#0f172a;color:#e2e8f0;padding:25px;font-family:monospace;line-height:1.6;'>";
echo "<h1 style='color:#38bdf8;'>🔍 Complete Safe Debugger</h1>";

$laravelPath = '/home/u466475909/domains/bhandaripackersandmovers.in/ServiceHub';

try {
    echo "<h3>Step 1: Autoload</h3>";
    require $laravelPath . '/vendor/autoload.php';
    echo "✅ Autoload loaded.<br>";

    echo "<h3>Step 2: Bootstrap App</h3>";
    $app = require_once $laravelPath . '/bootstrap/app.php';
    echo "✅ App instance created.<br>";

    echo "<h3>Step 3: Bind Request & Kernel Handle</h3>";
    $request = \Illuminate\Http\Request::create('/admin/', 'GET');
    $app->instance('request', $request);

    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request);
    echo "✅ Kernel handle finished.<br>";

    echo "<h3>Step 4: Check URLs</h3>";
    echo "URL::to('/'): <code>" . \Illuminate\Support\Facades\URL::to('/') . "</code><br>";
    echo "config('app.url'): <code>" . config('app.url') . "</code><br>";
    echo "route('dashboard'): <code>" . route('dashboard') . "</code><br>";

    echo "<h3>Step 5: Response Details</h3>";
    echo "Status Code: <strong>" . $response->getStatusCode() . "</strong><br>";
    if (in_array($response->getStatusCode(), [301, 302])) {
        echo "➡️ Redirect Location: <strong style='color:#a3e635;font-size:16px;'>" . htmlspecialchars($response->headers->get('Location')) . "</strong><br>";
    } else if ($response->getStatusCode() >= 400) {
        echo "❌ Error Content Preview:<br><pre style='background:#1e1e1e;color:#fca5a5;padding:10px;'>" . htmlspecialchars(substr($response->getContent(), 0, 1000)) . "</pre>";
    } else {
        echo "✅ 200 OK Response Length: " . strlen($response->getContent()) . " bytes<br>";
    }
} catch (\Throwable $e) {
    echo "<div style='background:#450a0a;color:#fca5a5;padding:20px;border-radius:8px;margin-top:10px;'>";
    echo "<h2 style='color:#ef4444;'>❌ CAUGHT EXCEPTION / ERROR:</h2>";
    echo "<strong>Type:</strong> " . get_class($e) . "<br>";
    echo "<strong>Message:</strong> <span style='font-size:18px;color:#fff;'>" . htmlspecialchars($e->getMessage()) . "</span><br><br>";
    echo "<strong>File:</strong> " . $e->getFile() . " (Line " . $e->getLine() . ")<br><br>";
    echo "<strong>Stack Trace:</strong><pre style='background:#1e1e1e;color:#cbd5e1;padding:10px;overflow-x:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "</div>";
