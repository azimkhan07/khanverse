<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\ProjectController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\ServiceController;
use App\Http\Controllers\Seller\WalletController;
use App\Http\Controllers\Seller\ReviewController;
use App\Http\Controllers\Seller\NotificationController;
use App\Http\Controllers\Seller\ChatController;
use App\Http\Controllers\Seller\ProfileController;
use App\Http\Controllers\Seller\SettingController;

Route::middleware(['auth', 'role:seller'])
    ->prefix('seller')
    ->name('seller.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Projects
        |--------------------------------------------------------------------------
        */

        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');

        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

        /*
        |--------------------------------------------------------------------------
        | Services
        |--------------------------------------------------------------------------
        */

        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

        /*
        |--------------------------------------------------------------------------
        | Wallet
        |--------------------------------------------------------------------------
        */

        Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');

        /*
        |--------------------------------------------------------------------------
        | Reviews
        |--------------------------------------------------------------------------
        */

        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');

        /*
        |--------------------------------------------------------------------------
        | Notifications
        |--------------------------------------------------------------------------
        */

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

        /*
        |--------------------------------------------------------------------------
        | Chat
        |--------------------------------------------------------------------------
        */

        Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');

        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::post('/update', [ProfileController::class, 'updateProfile'])->name('update');
            Route::post('/password', [ProfileController::class, 'updatePassword'])->name('password');
            Route::post('/photo', [ProfileController::class, 'updatePhoto'])->name('photo');
            Route::delete('/photo', [ProfileController::class, 'deletePhoto'])->name('photo.delete');
        });

        /*
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    });
