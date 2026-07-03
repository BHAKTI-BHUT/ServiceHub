<?php
/**
 * =====================================================
 * ServiceHub - Deployment Helper Script
 * =====================================================
 * INSTRUCTIONS:
 *   1. Upload this file to: public_html/setup.php
 *   2. Open in browser: https://bhandaripackersandmovers.in/setup.php
 *   3. Click buttons to run commands
 *   4. DELETE this file immediately after use!
 * =====================================================
 */

// ── Security: Secret key required ────────────────────
$secret = 'servicehub2024';
if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
    die('<h2 style="color:red;font-family:sans-serif;">❌ Access Denied. Add ?key=servicehub2024 to URL</h2>');
}

// ── Laravel root path ─────────────────────────────────
$laravelPath = dirname(__DIR__) . '/ServiceHub';

function runArtisan($laravelPath, $command) {
    $artisan = $laravelPath . '/artisan';
    $phpBin  = PHP_BINARY ?: 'php';
    $output  = shell_exec("cd " . escapeshellarg($laravelPath) . " && $phpBin $artisan $command 2>&1");
    return $output ?: '(no output)';
}

function runShell($cmd) {
    $output = shell_exec($cmd . ' 2>&1');
    return $output ?: '(no output)';
}

$action = $_GET['action'] ?? '';
$result = '';

// ── Run requested action ──────────────────────────────
if ($action === 'config_clear')    $result = runArtisan($laravelPath, 'config:clear');
if ($action === 'cache_clear')     $result = runArtisan($laravelPath, 'cache:clear');
if ($action === 'view_clear')      $result = runArtisan($laravelPath, 'view:clear');
if ($action === 'route_clear')     $result = runArtisan($laravelPath, 'route:clear');
if ($action === 'config_cache')    $result = runArtisan($laravelPath, 'config:cache');
if ($action === 'migrate')         $result = runArtisan($laravelPath, 'migrate --force');
if ($action === 'db_seed')         $result = runArtisan($laravelPath, 'db:seed --force');
if ($action === 'optimize')        $result = runArtisan($laravelPath, 'optimize');
if ($action === 'storage_link') {
    $target = $laravelPath . '/storage/app/public';
    $link   = dirname(__DIR__) . '/public_html/storage';
    if (is_link($link)) unlink($link);
    $result = symlink($target, $link) ? "✅ Storage symlink created!\nTarget: $target\nLink: $link" : "❌ Symlink failed. Try manually.";
}
if ($action === 'run_all') {
    $steps = ['config:clear','cache:clear','view:clear','route:clear','config:cache','optimize'];
    $result = '';
    foreach ($steps as $cmd) {
        $result .= "▶ php artisan $cmd\n";
        $result .= runArtisan($laravelPath, $cmd) . "\n\n";
    }
}
if ($action === 'check') {
    $result  = "PHP Version: " . phpversion() . "\n";
    $result .= "Laravel Path: $laravelPath\n";
    $result .= "Laravel exists: " . (is_dir($laravelPath) ? "✅ YES" : "❌ NO") . "\n";
    $result .= ".env exists: " . (file_exists($laravelPath . '/.env') ? "✅ YES" : "❌ NO") . "\n";
    $result .= "vendor exists: " . (is_dir($laravelPath . '/vendor') ? "✅ YES" : "❌ NO") . "\n";
    $result .= "storage writable: " . (is_writable($laravelPath . '/storage') ? "✅ YES" : "❌ NO") . "\n";
    $result .= "bootstrap/cache writable: " . (is_writable($laravelPath . '/bootstrap/cache') ? "✅ YES" : "❌ NO") . "\n";
    $result .= "shell_exec enabled: " . (function_exists('shell_exec') ? "✅ YES" : "❌ NO") . "\n";
}

$key = '?key=servicehub2024';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ServiceHub Deploy Helper</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: #e2e8f0; min-height: 100vh; padding: 30px 20px; }
  h1 { color: #38bdf8; font-size: 1.8rem; margin-bottom: 5px; }
  .subtitle { color: #94a3b8; margin-bottom: 30px; font-size: 0.9rem; }
  .warning { background: #7f1d1d; border: 1px solid #ef4444; border-radius: 8px; padding: 12px 16px; margin-bottom: 25px; color: #fca5a5; font-size: 0.9rem; }
  .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 30px; }
  .btn { display: block; padding: 14px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem; text-align: center; transition: all 0.2s; border: 2px solid transparent; }
  .btn-blue  { background: #1e40af; color: #fff; border-color: #3b82f6; }
  .btn-blue:hover  { background: #2563eb; }
  .btn-green { background: #14532d; color: #fff; border-color: #22c55e; }
  .btn-green:hover { background: #16a34a; }
  .btn-red   { background: #7f1d1d; color: #fff; border-color: #ef4444; }
  .btn-red:hover   { background: #dc2626; }
  .btn-purple{ background: #4c1d95; color: #fff; border-color: #8b5cf6; }
  .btn-purple:hover{ background: #7c3aed; }
  .btn-yellow{ background: #78350f; color: #fff; border-color: #f59e0b; }
  .btn-yellow:hover{ background: #d97706; }
  .btn-teal  { background: #134e4a; color: #fff; border-color: #14b8a6; }
  .btn-teal:hover  { background: #0f766e; }
  .btn-big   { background: linear-gradient(135deg, #1d4ed8, #7c3aed); color: #fff; border: none; font-size: 1rem; padding: 18px; }
  .btn-big:hover { opacity: 0.9; transform: scale(1.02); }
  .result-box { background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 20px; }
  .result-box h3 { color: #38bdf8; margin-bottom: 12px; }
  pre { white-space: pre-wrap; color: #a3e635; font-family: 'Courier New', monospace; font-size: 0.85rem; line-height: 1.6; }
  .section-title { color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; margin-top: 20px; }
  hr { border: none; border-top: 1px solid #334155; margin: 20px 0; }
</style>
</head>
<body>

<h1>⚙️ ServiceHub Deploy Helper</h1>
<p class="subtitle">bhandaripackersandmovers.in — Deployment Control Panel</p>

<div class="warning">
  ⚠️ <strong>Security Warning:</strong> Is file ko use karne ke baad turant DELETE karo <code>public_html/setup.php</code>
</div>

<p class="section-title">🔍 Step 1 — Pehle Check Karo</p>
<div class="grid">
  <a class="btn btn-teal" href="<?= $key ?>&action=check">🔍 System Check</a>
</div>

<p class="section-title">🚀 Step 2 — Ek Click mein Sab Clear (Recommended)</p>
<div class="grid">
  <a class="btn btn-big" href="<?= $key ?>&action=run_all">⚡ Run All Clears + Optimize</a>
</div>

<p class="section-title">🔧 Step 3 — Individual Commands</p>
<div class="grid">
  <a class="btn btn-blue"   href="<?= $key ?>&action=config_clear">🗑️ Config Clear</a>
  <a class="btn btn-blue"   href="<?= $key ?>&action=cache_clear">🗑️ Cache Clear</a>
  <a class="btn btn-blue"   href="<?= $key ?>&action=view_clear">🗑️ View Clear</a>
  <a class="btn btn-blue"   href="<?= $key ?>&action=route_clear">🗑️ Route Clear</a>
  <a class="btn btn-green"  href="<?= $key ?>&action=config_cache">✅ Config Cache</a>
  <a class="btn btn-purple" href="<?= $key ?>&action=optimize">⚡ Optimize</a>
</div>

<p class="section-title">🗄️ Database</p>
<div class="grid">
  <a class="btn btn-yellow" href="<?= $key ?>&action=migrate">🗄️ Run Migrations</a>
  <a class="btn btn-yellow" href="<?= $key ?>&action=db_seed">🌱 Run Seeders</a>
</div>

<p class="section-title">🔗 Storage</p>
<div class="grid">
  <a class="btn btn-teal"   href="<?= $key ?>&action=storage_link">🔗 Create Storage Symlink</a>
</div>

<?php if ($result): ?>
<hr>
<div class="result-box">
  <h3>📋 Output:</h3>
  <pre><?= htmlspecialchars($result) ?></pre>
</div>
<?php endif; ?>

</body>
</html>
