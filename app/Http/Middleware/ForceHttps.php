<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Force HTTPS scheme and root URL on production.
     *
     * NOTE: This MUST be a middleware (not AppServiceProvider::boot) because
     * URL::forceRootUrl() internally resolves the 'url' service from the
     * container via RoutingServiceProvider, which needs 'request' to be bound.
     * The 'request' object is only available AFTER the kernel initialises it —
     * i.e., at middleware execution time, not during the boot phase.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');

            if (config('app.url')) {
                URL::forceRootUrl(config('app.url'));
            }
        }

        return $next($request);
    }
}
