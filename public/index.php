<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// ── Pre-bind Request ─────────────────────────────────────────────
// Bind the request to the container BEFORE handleRequest() boots
// service providers. This prevents BindingResolutionException when
// any provider's boot() method (or Spatie/third-party packages)
// attempts to resolve 'request' during the BootProviders phase.
// handleRequest() internally re-binds the same instance — safe & idempotent.
$request = Request::capture();
$app->instance('request', $request);

$app->handleRequest($request);

