<?php
// ============================================
// ServiceHub - Programmatic Cache Clear Runner
// URL: /admin/clear.php?key=servicehub2024
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

$commands = [
    'config:clear',
    'cache:clear',
    'view:clear',
    'route:clear',
    'optimize:clear'
];

echo "<pre style='background:#0f172a;color:#86efac;padding:20px;font-family:monospace;line-height:1.6;'>";
echo "Command: php artisan cache/config/view clear (Programmatic)\n";
echo "--------------------------------------------------\n";

foreach ($commands as $command) {
    try {
        $status = $kernel->call($command);
        echo "Command: php artisan $command\n";
        echo "Exit Code: " . $status . "\n";
        echo "Output:\n" . $kernel->output() . "\n";
        echo "--------------------------------------------------\n";
    } catch (\Exception $e) {
        echo "❌ Error on '$command': " . $e->getMessage() . "\n";
        echo "--------------------------------------------------\n";
    }
}

echo "</pre>";
?>
