<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Cache-Control: no-store, no-cache');

$laravelPath = '/home/u466475909/domains/bhandaripackersandmovers.in/ServiceHub';

echo "<div style='font-family:monospace;background:#0f172a;color:#e2e8f0;padding:20px;'>";
echo "<h2 style='color:#38bdf8;'>🗑️ Bootstrap Cache Cleaner</h2>";

$cacheFiles = [
    'config.php',
    'services.php',
    'packages.php',
    'routes-v7.php',
    'events.php',
];

$cacheDir = $laravelPath . '/bootstrap/cache/';

foreach ($cacheFiles as $cf) {
    $path = $cacheDir . $cf;
    if (file_exists($path)) {
        if (@unlink($path)) {
            echo "✅ Deleted: <strong>$cf</strong><br>";
        } else {
            echo "❌ Could NOT delete: <strong>$cf</strong> — delete manually via cPanel<br>";
        }
    } else {
        echo "⬜ Not found (OK): $cf<br>";
    }
}

echo "<br><h3 style='color:#a3e635;'>Done! Now visit <a style='color:#38bdf8;' href='https://bhandaripackersandmovers.in/admin/'>the site</a></h3>";
echo "</div>";
