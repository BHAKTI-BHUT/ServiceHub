<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Detailed Files Check</h2>";

$base_path = '/home/u466475909/domains/bhandaripackersandmovers.in/public_html/admin';

function list_files($dir) {
    if (!is_dir($dir)) {
        echo "❌ Directory not found: <code>$dir</code><br>";
        return;
    }
    echo "<h3>Files in " . basename($dir) . ":</h3>";
    $files = scandir($dir);
    $count = 0;
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $full_path = $dir . '/' . $file;
        echo "- " . $file . " (" . (is_dir($full_path) ? 'Directory' : 'File, ' . filesize($full_path) . ' bytes') . ")<br>";
        $count++;
        if ($count > 10) {
            echo "- ... and more files<br>";
            break;
        }
    }
}

list_files($base_path . '/fonts/vendor/bootstrap-icons');
list_files($base_path . '/fonts/vendor/remixicon');
