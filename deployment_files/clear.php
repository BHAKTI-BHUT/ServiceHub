<?php
// ============================================
// ServiceHub - Clear Cache
// URL: /admin/clear.php?key=servicehub2024
// DELETE THIS FILE AFTER USE!
// ============================================

$secret = 'servicehub2024';
if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
    die('<h2 style="color:red;font-family:sans-serif;">❌ Access Denied. Add ?key=servicehub2024</h2>');
}

$laravelPath = '/home/u466475909/domains/bhandaripackersandmovers.in/ServiceHub';
$phpBin = PHP_BINARY ?: 'php';

$commands = [
    'config:clear',
    'cache:clear',
    'view:clear',
    'route:clear',
    'optimize:clear'
];

echo "<pre style='background:#0f172a;color:#86efac;padding:20px;font-family:monospace;line-height:1.6;'>";
foreach ($commands as $command) {
    $cmd = "cd " . escapeshellarg($laravelPath) . " && $phpBin artisan $command 2>&1";
    $output = shell_exec($cmd);
    echo "Command: php artisan $command\n";
    echo htmlspecialchars($output ?: '(no output)') . "\n\n";
}
echo "</pre>";
?>
