<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// Tenant domain authentication routes (login only, no registration)
Route::middleware(['tenant', 'guest'])->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('tenant.login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->name('tenant.login.store');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('tenant.password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('tenant.password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('tenant.password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('tenant.password.store');
});

Route::middleware(['tenant', 'auth'])->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('tenant.verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('tenant.verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('tenant.verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('tenant.password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->name('tenant.password.confirm.store');

    Route::put('password', [PasswordController::class, 'update'])
        ->name('tenant.password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('tenant.logout');
});