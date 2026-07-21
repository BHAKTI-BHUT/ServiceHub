<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;

define('LARAVEL_START', microtime(true));

// ── Absolute path to Laravel root (NOT relative ../) ──────────────
// Using absolute path avoids ANY symlink/relative-path ambiguity.
$laravelBase = '/home/u466475909/domains/bhandaripackersandmovers.in/ServiceHub';

// Maintenance mode
if (file_exists($m = $laravelBase . '/storage/framework/maintenance.php')) {
    require $m;
}

// Autoloader
require $laravelBase . '/vendor/autoload.php';

// ── Bootstrap app ─────────────────────────────────────────────────
/** @var Application $app */
$app = require $laravelBase . '/bootstrap/app.php';

// ── Pre-bind Request BEFORE any bootstrap/provider runs ───────────
// This prevents BindingResolutionException in ANY provider that
// touches URL facade or 'request' during boot() phase.
$request = Request::capture();
$app->instance('request', $request);
\Illuminate\Support\Facades\Facade::clearResolvedInstance('request');

// ── Handle request ────────────────────────────────────────────────
$kernel   = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
