<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

// Get domain from config
$appDomain = config('app.domain');

// Main application routes (for main domain)
Route::domain($appDomain)->group(function () {
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    });

    Route::get('/tenant-not-found', function () {
        return Inertia::render('Errors/TenantNotFound');
    });

    Route::get('/tenant-inactive', function () {
        return Inertia::render('Errors/TenantInactive');
    });

    // Include super admin routes
    require __DIR__.'/super-admin.php';

    // Main domain auth routes
    require __DIR__.'/main-auth.php';

    // Default dashboard redirect for super admin
    Route::get('/dashboard', function () {
        if (Auth::check() && Auth::user()->is_super_admin) {
            return redirect()->route('super-admin.dashboard');
        }
        return redirect('/');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

// Tenant routes (for subdomains)
// Handle both with and without ports for local development
$appDomain = config('app.domain');
Route::domain('{subdomain}.' . $appDomain)->group(function () {
    // Bind the subdomain parameter for route generation
    Route::bind('subdomain', function ($value) {
        return $value;
    });
    
    // Make subdomain available for all routes in this group
    Route::middleware(['web'])->group(function () {
        require __DIR__.'/tenant.php';
        require __DIR__.'/tenant-auth.php';
    });
});

// Fallback for any other routes
Route::fallback(function () {
    return Inertia::render('Errors/404');
});
