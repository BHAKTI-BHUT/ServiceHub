<?php
/**
 * Standalone Server Diagnostics — NO Laravel Bootstrap
 * This script will NEVER crash because it does NOT load Laravel.
 * Upload to: public_html/admin/diag.php
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$base = '/home/u466475909/domains/bhandaripackersandmovers.in';
$serviceHub = $base . '/ServiceHub';
$adminDir   = $base . '/public_html/admin';

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Diag</title>
<style>
body{font-family:monospace;background:#0f172a;color:#e2e8f0;padding:20px;font-size:13px;}
h2{color:#38bdf8;border-bottom:1px solid #334155;padding-bottom:8px;margin-top:25px;}
.box{background:#1e293b;border:1px solid #334155;border-radius:8px;padding:15px;margin-bottom:15px;}
.ok{color:#10b981;}.fail{color:#ef4444;}.warn{color:#fbbf24;}
pre{background:#020617;padding:12px;border-radius:6px;color:#f87171;white-space:pre-wrap;font-size:11px;max-height:500px;overflow:auto;}
</style></head><body>";

echo "<h1 style='color:#f43f5e;'>🔍 Standalone Server Diagnostics</h1>";

// ─── 1. PHP Info ────────────────────────────────────────
echo "<h2>1. PHP Environment</h2><div class='box'>";
echo "PHP Version: <b>" . phpversion() . "</b><br>";
echo "Memory Limit: <b>" . ini_get('memory_limit') . "</b><br>";
echo "Max Execution Time: <b>" . ini_get('max_execution_time') . "s</b><br>";
echo "Display Errors: <b>" . ini_get('display_errors') . "</b><br>";
echo "Open Basedir: <b>" . (ini_get('open_basedir') ?: 'NONE (OK)') . "</b><br>";
echo "Error Log: <b>" . ini_get('error_log') . "</b><br>";
echo "</div>";

// ─── 2. Directory & Permission Check ─────────────────────
echo "<h2>2. Directory & Permissions</h2><div class='box'>";
$dirs = [
    'ServiceHub'          => $serviceHub,
    'vendor'              => $serviceHub . '/vendor',
    'storage'             => $serviceHub . '/storage',
    'storage/logs'        => $serviceHub . '/storage/logs',
    'storage/framework'   => $serviceHub . '/storage/framework',
    'bootstrap/cache'     => $serviceHub . '/bootstrap/cache',
    'public_html/admin'   => $adminDir,
];
foreach ($dirs as $label => $path) {
    if (is_dir($path)) {
        $writable = is_writable($path) ? "<span class='ok'>WRITABLE</span>" : "<span class='fail'>NOT WRITABLE!</span>";
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "✅ {$label}: EXISTS | {$writable} | perms: {$perms}<br>";
    } else {
        echo "❌ <span class='fail'>{$label}: MISSING!</span> ({$path})<br>";
    }
}
echo "</div>";

// ─── 3. Key Files Check ──────────────────────────────────
echo "<h2>3. Key Files</h2><div class='box'>";
$files = [
    '.env'           => $serviceHub . '/.env',
    'index.php'      => $adminDir . '/index.php',
    '.htaccess'      => $adminDir . '/.htaccess',
    'artisan'        => $serviceHub . '/artisan',
    'AppServiceProvider' => $serviceHub . '/app/Providers/AppServiceProvider.php',
];
foreach ($files as $label => $path) {
    if (file_exists($path)) {
        $size = filesize($path);
        echo "✅ {$label}: EXISTS ({$size} bytes)<br>";
    } else {
        echo "❌ <span class='fail'>{$label}: MISSING!</span><br>";
    }
}
echo "</div>";

// ─── 4. Cache Files (stale?) ─────────────────────────────
echo "<h2>4. Cached Bootstrap Files</h2><div class='box'>";
$cacheDir = $serviceHub . '/bootstrap/cache';
if (is_dir($cacheDir)) {
    $cacheFiles = glob($cacheDir . '/*.php');
    if (empty($cacheFiles)) {
        echo "✅ <span class='ok'>No cached files (clean state)</span><br>";
    } else {
        foreach ($cacheFiles as $cf) {
            $age = time() - filemtime($cf);
            $ageStr = $age > 3600 ? round($age/3600,1) . ' hrs ago' : round($age/60) . ' mins ago';
            echo "⚠️ <span class='warn'>" . basename($cf) . "</span> — modified {$ageStr} (" . filesize($cf) . " bytes)<br>";
        }
        echo "<br><b class='warn'>⚠️ If site gives 500 error, delete these cached files from File Manager!</b><br>";
        echo "Path: <code>{$cacheDir}/</code><br>";
    }
}
echo "</div>";

// ─── 5. Read .htaccess ───────────────────────────────────
echo "<h2>5. Live .htaccess Content</h2><div class='box'>";
$htaccess = $adminDir . '/.htaccess';
if (file_exists($htaccess)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($htaccess)) . "</pre>";
} else {
    echo "<span class='fail'>❌ .htaccess NOT FOUND in admin directory!</span>";
}
echo "</div>";

// ─── 6. Read index.php ───────────────────────────────────
echo "<h2>6. Live index.php Content</h2><div class='box'>";
$indexFile = $adminDir . '/index.php';
if (file_exists($indexFile)) {
    echo "<pre>" . htmlspecialchars(file_get_contents($indexFile)) . "</pre>";
} else {
    echo "<span class='fail'>❌ index.php NOT FOUND!</span>";
}
echo "</div>";

// ─── 7. Read AppServiceProvider.php (line 26-40) ─────────
echo "<h2>7. AppServiceProvider boot() method</h2><div class='box'>";
$aspFile = $serviceHub . '/app/Providers/AppServiceProvider.php';
if (file_exists($aspFile)) {
    $lines = file($aspFile);
    $show = array_slice($lines, 25, 20); // lines 26-45
    echo "<pre>";
    foreach ($show as $i => $line) {
        echo sprintf("%3d: %s", $i + 26, htmlspecialchars($line));
    }
    echo "</pre>";
} else {
    echo "<span class='fail'>❌ AppServiceProvider.php NOT FOUND!</span>";
}
echo "</div>";

// ─── 8. PHP Error Log (server-level) ─────────────────────
echo "<h2>8. PHP Error Log (Server Level)</h2><div class='box'>";
$errorLogs = [
    $adminDir . '/error_log',
    $base . '/public_html/error_log',
    $base . '/error_log',
    '/tmp/php-errors.log',
];
$found = false;
foreach ($errorLogs as $logPath) {
    if (file_exists($logPath)) {
        $found = true;
        echo "<b>Found: {$logPath}</b> (" . filesize($logPath) . " bytes)<br>";
        $fp = fopen($logPath, 'r');
        if ($fp) {
            $size = filesize($logPath);
            $readSize = min($size, 50000);
            if ($readSize > 0) {
                fseek($fp, -$readSize, SEEK_END);
                $content = fread($fp, $readSize);
                // Get last 30 lines
                $lines = explode("\n", trim($content));
                $lastLines = array_slice($lines, -30);
                echo "<pre>" . htmlspecialchars(implode("\n", $lastLines)) . "</pre>";
            }
            fclose($fp);
        }
        break; // Show first found log only
    }
}
if (!$found) {
    echo "ℹ No PHP error_log file found in standard locations.<br>";
    echo "Checked: " . implode(', ', $errorLogs);
}
echo "</div>";

// ─── 9. Laravel Log ──────────────────────────────────────
echo "<h2>9. Laravel Log (laravel.log)</h2><div class='box'>";
$laravelLog = $serviceHub . '/storage/logs/laravel.log';
if (file_exists($laravelLog)) {
    $size = filesize($laravelLog);
    echo "File size: {$size} bytes<br>";
    if ($size > 0) {
        $fp = fopen($laravelLog, 'r');
        $readSize = min($size, 80000);
        fseek($fp, -$readSize, SEEK_END);
        $content = fread($fp, $readSize);
        fclose($fp);
        $entries = preg_split('/\n(?=\[\d{4}-\d{2}-\d{2})/', trim($content));
        $last = array_slice($entries, -3);
        foreach (array_reverse($last) as $entry) {
            echo "<pre>" . htmlspecialchars(substr($entry, 0, 5000)) . "</pre>";
        }
    } else {
        echo "ℹ Log file is empty (0 bytes).";
    }
} else {
    echo "<span class='fail'>❌ laravel.log NOT FOUND!</span>";
}
echo "</div>";

echo "</body></html>";
