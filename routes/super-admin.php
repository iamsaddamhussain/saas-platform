<?php

use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\TenantController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'super.admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('tenants', TenantController::class);
    Route::post('tenants/{tenant}/impersonate', [TenantController::class, 'impersonate'])->name('tenants.impersonate');
    Route::post('stop-impersonation', [TenantController::class, 'stopImpersonation'])->name('stop-impersonation');
});