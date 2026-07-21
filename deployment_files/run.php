<?php
// ============================================
// ServiceHub - Artisan Command Runner
// URL: /admin/run.php?key=servicehub2024
// DELETE THIS FILE AFTER USE!
// ============================================

$secret = 'servicehub2024';
if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
    die('<h2 style="color:red;font-family:sans-serif;">❌ Access Denied. Add ?key=servicehub2024</h2>');
}

$laravelPath = '/home/u466475909/domains/bhandaripackersandmovers.in/ServiceHub';
$phpBin = PHP_BINARY ?: 'php';

function runArtisan($laravelPath, $phpBin, $command) {
    $cmd = "cd " . escapeshellarg($laravelPath) . " && $phpBin artisan $command 2>&1";
    $output = shell_exec($cmd);
    return $output ?: '(no output)';
}

$action = $_GET['action'] ?? '';
$result = '';
$resultTitle = '';

if ($action === 'migrate') {
    $resultTitle = '🗄️ php artisan migrate --force';
    $result = runArtisan($laravelPath, $phpBin, 'migrate --force');
}
if ($action === 'seed') {
    $resultTitle = '🌱 php artisan db:seed --force';
    $result = runArtisan($laravelPath, $phpBin, 'db:seed --force');
}
if ($action === 'migrate_seed') {
    $resultTitle = '🗄️🌱 migrate + seed';
    $result  = "▶ migrate --force\n";
    $result .= runArtisan($laravelPath, $phpBin, 'migrate --force');
    $result .= "\n\n▶ db:seed --force\n";
    $result .= runArtisan($laravelPath, $phpBin, 'db:seed --force');
}
if ($action === 'optimize_clear') {
    $resultTitle = '🧹 optimize:clear';
    $result  = "▶ config:clear\n"   . runArtisan($laravelPath, $phpBin, 'config:clear') . "\n\n";
    $result .= "▶ cache:clear\n"    . runArtisan($laravelPath, $phpBin, 'cache:clear') . "\n\n";
    $result .= "▶ view:clear\n"     . runArtisan($laravelPath, $phpBin, 'view:clear') . "\n\n";
    $result .= "▶ route:clear\n"    . runArtisan($laravelPath, $phpBin, 'route:clear') . "\n\n";
    $result .= "▶ optimize:clear\n" . runArtisan($laravelPath, $phpBin, 'optimize:clear');
}
if ($action === 'optimize') {
    $resultTitle = '⚡ optimize';
    $result  = "▶ config:cache\n"   . runArtisan($laravelPath, $phpBin, 'config:cache') . "\n\n";
    $result .= "▶ route:cache\n"    . runArtisan($laravelPath, $phpBin, 'route:cache') . "\n\n";
    $result .= "▶ view:cache\n"     . runArtisan($laravelPath, $phpBin, 'view:cache') . "\n\n";
    $result .= "▶ optimize\n"       . runArtisan($laravelPath, $phpBin, 'optimize');
}
if ($action === 'storage_link') {
    $resultTitle = '🔗 storage:link';
    $result = runArtisan($laravelPath, $phpBin, 'storage:link');
}
if ($action === 'check') {
    $resultTitle = '🔍 System Check';
    $result  = "PHP Version: " . phpversion() . "\n";
    $result .= "PHP Binary: $phpBin\n";
    $result .= "Laravel Path: $laravelPath\n";
    $result .= "ServiceHub exists: " . (is_dir($laravelPath) ? "✅ YES" : "❌ NO") . "\n";
    $result .= ".env exists: " . (file_exists($laravelPath . '/.env') ? "✅ YES" : "❌ NO") . "\n";
    $result .= "vendor exists: " . (is_dir($laravelPath . '/vendor') ? "✅ YES" : "❌ NO") . "\n";
    $result .= "storage writable: " . (is_writable($laravelPath . '/storage') ? "✅ YES" : "❌ NO") . "\n";
    $result .= "shell_exec enabled: " . (function_exists('shell_exec') ? "✅ YES" : "❌ NO") . "\n";
    $result .= "\n▶ php artisan --version\n" . runArtisan($laravelPath, $phpBin, '--version');
}

$key = '?key=servicehub2024';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>ServiceHub — Artisan Runner</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: #e2e8f0; min-height: 100vh; padding: 30px 20px; }
  h1 { color: #38bdf8; font-size: 1.6rem; margin-bottom: 4px; }
  .sub { color: #64748b; font-size: 0.85rem; margin-bottom: 24px; }
  .warn { background: #7f1d1d33; border: 1px solid #ef4444; border-radius: 8px; padding: 10px 16px; margin-bottom: 24px; color: #fca5a5; font-size: 0.85rem; }
  .section { color: #64748b; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; margin: 20px 0 8px; }
  .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px; margin-bottom: 8px; }
  .btn { display: block; padding: 13px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.88rem; text-align: center; transition: opacity 0.2s; }
  .blue   { background: #1d4ed8; color: #fff; }
  .green  { background: #15803d; color: #fff; }
  .purple { background: #6d28d9; color: #fff; }
  .yellow { background: #92400e; color: #fff; }
  .teal   { background: #0f766e; color: #fff; }
  .red    { background: #991b1b; color: #fff; }
  .big    { background: linear-gradient(135deg, #1d4ed8, #7c3aed); color: #fff; font-size: 0.95rem; padding: 16px; }
  .btn:hover { opacity: 0.85; }
  .result { background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 20px; margin-top: 24px; }
  .result h3 { color: #38bdf8; margin-bottom: 12px; font-size: 0.95rem; }
  pre { white-space: pre-wrap; color: #86efac; font-family: 'Courier New', monospace; font-size: 0.82rem; line-height: 1.7; }
  hr { border: none; border-top: 1px solid #1e293b; margin: 20px 0; }
</style>
</head>
<body>

<h1>⚙️ ServiceHub — Artisan Runner</h1>
<p class="sub">bhandaripackersandmovers.in — Live Server Commands</p>

<div class="warn">⚠️ <strong>Security:</strong> Is file ko use ke baad turant DELETE karo!</div>

<p class="section">🔍 Diagnosis</p>
<div class="grid">
  <a class="btn teal" href="<?= $key ?>&action=check">🔍 System Check</a>
</div>

<hr>

<p class="section">🗄️ Database</p>
<div class="grid">
  <a class="btn yellow" href="<?= $key ?>&action=migrate">🗄️ Migrate</a>
  <a class="btn yellow" href="<?= $key ?>&action=seed">🌱 Seed</a>
  <a class="btn big"    href="<?= $key ?>&action=migrate_seed">🗄️🌱 Migrate + Seed (Both)</a>
</div>

<hr>

<p class="section">🧹 Cache & Optimize</p>
<div class="grid">
  <a class="btn red"    href="<?= $key ?>&action=optimize_clear">🧹 Optimize:Clear (All Cache Clear)</a>
  <a class="btn green"  href="<?= $key ?>&action=optimize">⚡ Optimize (Cache Build)</a>
</div>

<hr>

<p class="section">🔗 Storage</p>
<div class="grid">
  <a class="btn teal" href="<?= $key ?>&action=storage_link">🔗 Storage Link</a>
</div>

<?php if ($result): ?>
<div class="result">
  <h3>📋 Output — <?= htmlspecialchars($resultTitle) ?></h3>
  <pre><?= htmlspecialchars($result) ?></pre>
</div>
<?php endif; ?>

</body>
</html>
