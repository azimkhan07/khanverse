<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\RememberController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // The React SPA (resources/panels) owns these pages via react-router.
    Route::get('register', fn () => view('app'))->name('register');
    Route::get('login', fn () => view('app'))->name('login');
    Route::get('forgot-password', fn () => view('app'))->name('password.request');
    Route::get('reset-password/{token}', fn () => view('app'))->name('password.reset');

    // Fallback form posts (kept for backward compatibility with the blade auth UI).
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
                ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});

// Google OAuth
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])
            ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])
            ->name('google.callback');

// Remember me encrypt/decrypt
Route::post('/encrypt-value', [RememberController::class, 'encrypt'])
            ->middleware('throttle:30,1')
            ->name('encrypt.value');

Route::post('/decrypt-value', [RememberController::class, 'decrypt'])
            ->middleware('throttle:30,1')
            ->name('decrypt.value');
