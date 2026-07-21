<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<div style='font-family:monospace;background:#0f172a;color:#e2e8f0;padding:20px;'>";
echo "<h2 style='color:#38bdf8;'>Diagnostic Check Step 3 - Testing Route Execution</h2>";

$laravelPath = '/home/u466475909/domains/bhandaripackersandmovers.in/ServiceHub';

require $laravelPath . '/vendor/autoload.php';
$app = require_once $laravelPath . '/bootstrap/app.php';

function testRoute($app, $uri) {
    echo "<h3>Testing URI: <code>$uri</code></h3>";
    try {
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['PATH_INFO'] = $uri;
        $_SERVER['HTTP_HOST'] = 'bhandaripackersandmovers.in';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $request = \Illuminate\Http\Request::create($uri, 'GET');
        $response = $app->handleRequest($request);

        echo "Status: <strong>" . $response->getStatusCode() . "</strong><br>";
        if ($response->getStatusCode() >= 400) {
            echo "<div style='color:#ef4444;background:#331010;padding:10px;margin-top:5px;'>";
            echo "Content Preview: " . htmlspecialchars(substr($response->getContent(), 0, 500));
            echo "</div>";
        } else {
            echo "✅ Success! Preview: " . htmlspecialchars(substr($response->getContent(), 0, 300)) . "<br>";
        }
    } catch (\Throwable $e) {
        echo "<div style='color:#ef4444;background:#450a0a;padding:10px;border-radius:6px;margin-top:5px;'>";
        echo "❌ <strong>CRASH EXCEPTION:</strong> " . get_class($e) . "<br>";
        echo "<strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "<strong>File:</strong> " . $e->getFile() . " (Line " . $e->getLine() . ")<br>";
        echo "<strong>Trace:</strong><pre style='background:#1e1e1e;padding:10px;font-size:11px;overflow-x:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</div>";
    }
}

testRoute($app, '/login');
testRoute($app, '/admin/login');
testRoute($app, '/admin');

echo "<h3>Database Test:</h3>";
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "✅ DB Connected!<br>";
} catch (\Throwable $e) {
    echo "❌ DB Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo "</div>";
