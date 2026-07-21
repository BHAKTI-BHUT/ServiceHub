<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$laravelPath = '/home/u466475909/domains/bhandaripackersandmovers.in/ServiceHub';

echo "<div style='font-family:monospace;background:#0f172a;color:#e2e8f0;padding:20px;'>";
echo "<h2 style='color:#38bdf8;'>🛣️ Route List Diagnostic</h2>";

try {
    require $laravelPath . '/vendor/autoload.php';
    $app = require $laravelPath . '/bootstrap/app.php';

    $request = \Illuminate\Http\Request::capture();
    $app->instance('request', $request);

    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    $routes = \Illuminate\Support\Facades\Route::getRoutes();

    echo "Total routes registered: <b>" . count($routes) . "</b><br><br>";

    echo "<b>Checking 'admin.' named routes:</b><br><ul>";
    $foundRefunds = false;
    foreach ($routes as $route) {
        $name = $route->getName();
        if ($name && str_starts_with($name, 'admin.')) {
            echo "<li>" . htmlspecialchars($name) . " => " . htmlspecialchars($route->uri()) . "</li>";
            if ($name === 'admin.refunds') {
                $foundRefunds = true;
            }
        }
    }
    echo "</ul>";

    if ($foundRefunds) {
        echo "<h3 style='color:#a3e635;'>✅ 'admin.refunds' ROUTE IS REGISTERED!</h3>";
    } else {
        echo "<h3 style='color:#f87171;'>❌ 'admin.refunds' ROUTE IS MISSING IN ROUTE COLLECTION!</h3>";
    }

} catch (\Throwable $e) {
    echo "❌ Error: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</div>";
