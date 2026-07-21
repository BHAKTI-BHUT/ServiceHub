<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * - Admin role users bypass ALL permission checks (Gate::before).
     * - Other users are checked against their OWN direct permissions only.
     *   This ensures one user's permissions never affect another user.
     */
    public function boot(): void
    {
        // ── Force HTTPS & Root URL ───────────────────────────────────────────
        // NOTE: URL::forceRootUrl() internally resolves the 'url' service from
        // the container (via RoutingServiceProvider) which requires 'request' to
        // be bound — but 'request' is NOT available during BootProviders phase.
        // HTTPS forcing is now handled via middleware in bootstrap/app.php.
        // (trustProxies + ForceHttpsUrl middleware)

        // ── Admin bypass ────────────────────────────────────────────────────
        // If the logged-in user has the "Admin" role, allow everything.
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Admin')) {
                return true;  // Admin can do anything, skip all other checks
            }
        });

        // ── Per-user direct permission gates ────────────────────────────────
        // For non-admin users, check ONLY their own direct permissions
        // (not permissions inherited via role). This prevents one user's
        // role from leaking permissions to another user's session.
        $modules = config('PermissionModule.modules', []);
        foreach ($modules as $modulePermissions) {
            foreach ($modulePermissions as $permission) {
                Gate::define($permission, function ($user) use ($permission) {
                    // Use Spatie's direct permission check (no role inheritance)
                    return $user->hasDirectPermission($permission);
                });
            }
        }
    }
}
