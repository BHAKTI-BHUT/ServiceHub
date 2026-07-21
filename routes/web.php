<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\BookingController;
use App\Http\Controllers\Backend\BookingRequestController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SystemSettingController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/admin', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    // ── Profile ────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Customer Management ────────────────────────────────
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
    });

    // ── Booking Request Management ─────────────────────────
    Route::prefix('booking-request')->name('booking-request.')->group(function () {
        Route::get('/', [BookingRequestController::class, 'index'])->name('index');
        Route::get('/{bookingRequest}', [BookingRequestController::class, 'show'])->name('show');
        Route::post('/{bookingRequest}/approve', [BookingRequestController::class, 'approve'])->name('approve');
        Route::post('/{bookingRequest}/reject', [BookingRequestController::class, 'reject'])->name('reject');
    });

    // ── Booking Management ─────────────────────────────────
    Route::get('booking/search-customers', [BookingController::class, 'searchCustomers'])->name('booking.search-customers');
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/create', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
        Route::get('/{booking}/edit', [BookingController::class, 'edit'])->name('edit');
        Route::put('/{booking}', [BookingController::class, 'update'])->name('update');
        Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
        Route::post('/{booking}/complete', [BookingController::class, 'complete'])->name('complete');
        Route::post('/{booking}/assign-vendor', [BookingController::class, 'assignVendor'])->name('assignVendor');
        Route::get('/{booking}/registration-invoice', [BookingController::class, 'registrationInvoice'])->name('registration-invoice');
        Route::get('/{booking}/location', [BookingController::class, 'getLiveLocation'])->name('location');
    });

    // ── Vendor Booking Portal ──────────────────────────────
    Route::middleware(['role:Vendor'])->prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Backend\DashboardController::class, 'vendorIndex'])->name('dashboard');
        
        Route::prefix('booking')->name('booking.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Backend\Vendor\VendorBookingController::class, 'index'])->name('index');
            Route::get('/{booking}', [\App\Http\Controllers\Backend\Vendor\VendorBookingController::class, 'show'])->name('show');
            Route::post('/{booking}/respond', [\App\Http\Controllers\Backend\Vendor\VendorBookingController::class, 'respond'])->name('respond');
            Route::post('/{booking}/assign-supervisor', [\App\Http\Controllers\Backend\Vendor\VendorBookingController::class, 'assignSupervisor'])->name('assignSupervisor');
        });

        Route::prefix('wallet')->name('wallet.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Backend\Vendor\VendorWalletController::class, 'index'])->name('index');
        });
    });

    // ── Supervisor Booking Portal ───────────────────────────
    Route::middleware(['role:Superviser'])->prefix('supervisor')->name('supervisor.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Backend\DashboardController::class, 'supervisorIndex'])->name('dashboard');
        
        Route::prefix('booking')->name('booking.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'index'])->name('index');
            Route::get('/{booking}', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'show'])->name('show');
            Route::post('/{booking}/respond', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'respond'])->name('respond');
            Route::post('/{booking}/start-trip', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'startTrip'])->name('startTrip');
            Route::post('/{booking}/verify-otp', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'verifyOtp'])->name('verifyOtp');
            Route::post('/{booking}/upload-proof', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'uploadProof'])->name('uploadProof');
            Route::post('/{booking}/collect-cash', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'collectCash'])->name('collectCash');
            Route::post('/{booking}/start-shifting', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'startShifting'])->name('startShifting');
            Route::post('/{booking}/update-items', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'updateItems'])->name('updateItems');
            Route::post('/{booking}/complete-shifting', [\App\Http\Controllers\Backend\Supervisor\SupervisorBookingController::class, 'completeShifting'])->name('completeShifting');
        });
    });

    // ── User Management ────────────────────────────────────
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::post('/quick-create-supervisor', [UserController::class, 'quickCreateSupervisor'])->name('quick-create-supervisor');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/{user}/permissions', [UserController::class, 'permissions'])->name('permissions');
        Route::put('/{user}/permissions', [UserController::class, 'updatePermissions'])->name('permissions.update');
    });

    // ── Role Management ────────────────────────────────────
    Route::prefix('role')->name('role.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        Route::get('/{role}/permissions', [RoleController::class, 'permissions'])->name('permissions');
        Route::put('/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('permissions.update');
    });

    // ── System Settings ────────────────────────────────────
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SystemSettingController::class, 'edit'])->name('edit');
        Route::post('/', [SystemSettingController::class, 'update'])->name('update');
    });
});


Route::get('/admin/run-migrate', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = \Illuminate\Support\Facades\Artisan::output();

        return "<h2>✅ Migrate Successful!</h2><pre>" . $output . "</pre>";
    } catch (\Exception $e) {
        return "<h2>❌ Migrate Error</h2><pre>" . $e->getMessage() . "</pre>";
    }
});

Route::get('/admin/run-optimize', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $output = \Illuminate\Support\Facades\Artisan::output();

        return "<h2>✅ Optimize Clear Successful!</h2><pre>" . $output . "</pre>";
    } catch (\Exception $e) {
        return "<h2>❌ Optimize Clear Error</h2><pre>" . $e->getMessage() . "</pre>";
    }
});

Route::get('/admin/run-optimize', function () {
    return redirect('/run-optimize');
});

Route::get('/admin', function () {
    return redirect()->route('dashboard');
});

Route::get('/admin/run-seed', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        $output = \Illuminate\Support\Facades\Artisan::output();

        return "<h2>✅ Seeding Successful!</h2><pre>" . $output . "</pre>";
    } catch (\Exception $e) {
        return "<h2>❌ Seeding Error</h2><pre>" . $e->getMessage() . "</pre>";
    }
});

Route::get('/admin/run-logs', function () {
    $logFile = storage_path('logs/laravel.log');

    if (!file_exists($logFile)) {
        return "<h2>❌ No log file found</h2>";
    }

    // Last 200 lines dikhao (puri file nahi)
    $lines = file($logFile);
    $lastLines = array_slice($lines, -200);
    $content = implode("", $lastLines);

    return "<h2>📋 Laravel Logs (Last 200 lines)</h2>"
         . "<pre style='background:#1e1e1e;color:#d4d4d4;padding:20px;overflow:auto;max-height:80vh;font-size:13px;'>"
         . htmlspecialchars($content)
         . "</pre>";
});

Route::get('/run-artisan-storage-link', function () {
    $target = storage_path('app/public');
    $shortcut = public_path('storage');

    if (file_exists($shortcut)) {
        return "<h2>✅ Storage link already exists!</h2><p>Shortcut Path: {$shortcut}</p>";
    }

    try {
        // Try direct PHP symlink first (avoids exec() calls)
        if (symlink($target, $shortcut)) {
            return "<h2>✅ Storage Link Created Successfully via PHP symlink()!</h2>";
        }
        throw new \Exception("PHP symlink() returned false.");
    } catch (\Throwable $e) {
        try {
            // Fallback to Artisan (might fail if exec is disabled, but worth a try)
            \Illuminate\Support\Facades\Artisan::call('storage:link');
            $output = \Illuminate\Support\Facades\Artisan::output();
            return "<h2>✅ Storage Link Created via Artisan!</h2><pre>{$output}</pre>";
        } catch (\Throwable $ex) {
            return "<h2>❌ Error Creating Storage Link:</h2>"
                 . "<p><strong>Direct PHP symlink() failed:</strong> " . $e->getMessage() . "</p>"
                 . "<p><strong>Artisan storage:link failed:</strong> " . $ex->getMessage() . "</p>";
        }
    }
});

Route::get('/run-artisan-clear-cache', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('config:cache');
        $config = \Illuminate\Support\Facades\Artisan::output();

        \Illuminate\Support\Facades\Artisan::call('route:cache');
        $route = \Illuminate\Support\Facades\Artisan::output();

        \Illuminate\Support\Facades\Artisan::call('view:cache');
        $view = \Illuminate\Support\Facades\Artisan::output();

        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        $cache = \Illuminate\Support\Facades\Artisan::output();

        return "<div style='font-family:sans-serif;padding:20px;'>"
             . "<h2 style='color:#4CAF50;'>🚀 Cache Optimised & Cached Successfully!</h2>"
             . "<p><strong>Config Cache:</strong> {$config}</p>"
             . "<p><strong>Route Cache:</strong> {$route}</p>"
             . "<p><strong>View Cache:</strong> {$view}</p>"
             . "<p><strong>App Cache:</strong> {$cache}</p>"
             . "</div>";
    } catch (\Exception $e) {
        return "<h2>❌ Cache Error:</h2><p>" . $e->getMessage() . "</p>";
    }
});

Route::get('/run-artisan-clear-config', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');

        return "<div style='font-family:sans-serif;padding:20px;'>"
             . "<h2 style='color:#2196F3;'>🧹 All Caches Cleared! (config, route, view, cache)</h2>"
             . "</div>";
    } catch (\Exception $e) {
        return "<h2>❌ Error:</h2><p>" . $e->getMessage() . "</p>";
    }
});

Route::get('/run-artisan-migrate', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = \Illuminate\Support\Facades\Artisan::output();

        return "<div style='font-family:sans-serif;padding:20px;'>"
             . "<h2 style='color:#4CAF50;'>✅ Database Migration Executed Successfully!</h2>"
             . "<pre style='background:#1e1e1e;color:#d4d4d4;padding:15px;border-radius:6px;overflow:auto;'>{$output}</pre>"
             . "</div>";
    } catch (\Exception $e) {
        return "<h2>❌ Migration Error:</h2><p>" . $e->getMessage() . "</p>";
    }
});

Route::get('/run-artisan-seed', function (\Illuminate\Http\Request $request) {
    try {
        $params = ['--force' => true];
        if ($request->has('class')) {
            $params['--class'] = $request->input('class');
        }

        \Illuminate\Support\Facades\Artisan::call('db:seed', $params);
        $output = \Illuminate\Support\Facades\Artisan::output();

        $seederName = $request->input('class', 'DatabaseSeeder');

        return "<div style='font-family:sans-serif;padding:20px;'>"
             . "<h2 style='color:#4CAF50;'>🌱 Database Seeder ({$seederName}) Executed Successfully!</h2>"
             . "<pre style='background:#1e1e1e;color:#d4d4d4;padding:15px;border-radius:6px;overflow:auto;'>{$output}</pre>"
             . "</div>";
    } catch (\Exception $e) {
        return "<h2>❌ Seeder Error:</h2><p>" . $e->getMessage() . "</p>";
    }
});

require __DIR__ . '/auth.php';
require __DIR__ . '/Admin.php';
