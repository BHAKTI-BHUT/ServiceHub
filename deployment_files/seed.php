<?php
// ============================================
// ServiceHub - Programmatic Seeder Runner
// URL: /admin/seed.php?key=servicehub2024
// ============================================

$secret = 'servicehub2024';
if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
    die('<h2 style="color:red;font-family:sans-serif;">❌ Access Denied. Add ?key=servicehub2024</h2>');
}

define('LARAVEL_START', microtime(true));

// Autoload & Bootstrap
require __DIR__.'/../../ServiceHub/vendor/autoload.php';
$app = require_once __DIR__.'/../../ServiceHub/bootstrap/app.php';

// Instantiate kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre style='background:#0f172a;color:#86efac;padding:20px;font-family:monospace;line-height:1.6;'>";
echo "Command: php artisan db:seed --force (Programmatic)\n";
echo "--------------------------------------------------\n";

try {
    $status = $kernel->call('db:seed', ['--force' => true]);
    echo "Exit Code: " . $status . "\n\n";
    echo "Output:\n" . $kernel->output();
} catch (\Exception $e) {
    echo "❌ Error occurred: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
?>
