<?php
$logFile = '/home/u466475909/domains/bhandaripackersandmovers.in/ServiceHub/storage/logs/laravel.log';

if (!file_exists($logFile)) {
    echo "❌ Log file not found!";
    exit;
}

$content = file_get_contents($logFile);

// Split by log entries (each starts with [YYYY-MM-DD])
$entries = preg_split('/\n(?=\[\d{4}-\d{2}-\d{2})/', trim($content));

// Get last 3 entries
$lastEntries = array_slice($entries, -3);

echo "<style>body{background:#0f172a;color:#e2e8f0;font-family:monospace;padding:20px;}
.entry{background:#1e293b;border:1px solid #334155;border-radius:8px;padding:15px;margin-bottom:15px;}
.error-msg{color:#f87171;font-size:14px;font-weight:bold;}
.stack{color:#94a3b8;font-size:11px;margin-top:10px;}
</style>";

echo "<h2 style='color:#38bdf8;'>Last " . count($lastEntries) . " Laravel Errors</h2>";

foreach (array_reverse($lastEntries) as $i => $entry) {
    echo "<div class='entry'>";
    echo "<div class='error-msg'>" . htmlspecialchars(substr($entry, 0, 500)) . "</div>";
    echo "</div>";
}
?>
