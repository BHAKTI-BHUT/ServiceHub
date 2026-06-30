<?php
// Script to fix absolute font paths in CSS for subdirectory deployments

$css_file = __DIR__ . '/assets/css/icons.min.css';

if (!file_exists($css_file)) {
    die("Error: CSS file not found at " . $css_file);
}

$content = file_get_contents($css_file);

// Replace absolute '/fonts/vendor/' with relative '../../fonts/vendor/'
$new_content = str_replace('/fonts/vendor/', '../../fonts/vendor/', $content);

if ($new_content !== $content) {
    if (file_put_contents($css_file, $new_content)) {
        echo "<h1>Success!</h1> Font paths updated in icons.min.css successfully.<br>";
    } else {
        echo "<h1>Error!</h1> Failed to write to icons.min.css. Check file permissions.";
    }
} else {
    echo "<h1>No changes needed!</h1> Font paths are already correct or '/fonts/vendor/' was not found.";
}
