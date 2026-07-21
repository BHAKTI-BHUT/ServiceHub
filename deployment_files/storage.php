<?php
// ============================================
// ServiceHub - Programmatic Storage Link
// URL: /admin/storage.php?key=servicehub2024
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
echo "Command: php artisan storage:link (Programmatic)\n";
echo "--------------------------------------------------\n";

try {
    // If standard symlink fails on shared hosting, we can try to do symlink manually
    $status = $kernel->call('storage:link');
    echo "Exit Code: " . $status . "\n\n";
    echo "Output:\n" . $kernel->output();
} catch (\Exception $e) {
    echo "❌ Error occurred: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
?>
