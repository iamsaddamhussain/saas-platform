<?php

use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\ModuleController;
use App\Http\Controllers\Tenant\UserController;
use App\Http\Controllers\Tenant\SettingsController;
use App\Http\Controllers\SuperAdmin\TenantController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Root route for tenant subdomains - redirect to dashboard
Route::middleware(['tenant'])->get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

Route::middleware(['tenant', 'auth', 'tenant.user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');
    
    // Module management
    Route::get('/modules', [ModuleController::class, 'index'])
        ->middleware('permission:modules.view')
        ->name('tenant.modules.index');
    Route::post('/modules/{module}/toggle', [ModuleController::class, 'toggle'])
        ->middleware('permission:modules.manage')
        ->name('tenant.modules.toggle');
    
    // User management
    Route::middleware('permission:users.view')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('tenant.users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('tenant.users.create');
        Route::post('/users', [UserController::class, 'store'])->name('tenant.users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('tenant.users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('tenant.users.edit');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('tenant.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('tenant.users.destroy');
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('tenant.users.toggle-status');
        Route::post('/users/{user}/resend-verification', [UserController::class, 'resendVerification'])
            ->name('tenant.users.resend-verification');
    });
    
    // Settings
    Route::middleware('permission:settings.view')->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('tenant.settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])
            ->middleware('permission:settings.edit')
            ->name('tenant.settings.update');
        Route::post('/settings/logo', [SettingsController::class, 'updateLogo'])
            ->middleware('permission:settings.edit')
            ->name('tenant.settings.logo');
        Route::delete('/settings/logo', [SettingsController::class, 'deleteLogo'])
            ->middleware('permission:settings.edit')
            ->name('tenant.settings.logo');
    });
    
    // Stop impersonation (available for impersonated sessions)
    Route::post('/stop-impersonation', [TenantController::class, 'stopImpersonation'])
        ->name('tenant.stop-impersonation');
    
    // Add more tenant routes here as modules are built
});