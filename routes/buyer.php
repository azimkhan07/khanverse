<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Buyer\ChatController;
use App\Http\Controllers\Buyer\OrderController;
use App\Http\Controllers\Buyer\ReviewController;
use App\Http\Controllers\Buyer\WalletController;
use App\Http\Controllers\Buyer\ProfileController;
use App\Http\Controllers\Buyer\SettingController;
use App\Http\Controllers\Buyer\DashboardController;
use App\Http\Controllers\Buyer\ProjectController;
use App\Http\Controllers\Buyer\NotificationController;

Route::middleware(['auth', 'role:buyer'])->prefix('buyer')->name('buyer.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    });

    // Projects
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
    });

    // Wallet
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        Route::get('/transactions', [WalletController::class, 'transactions'])->name('transactions');
        Route::get('/deposit', [WalletController::class, 'deposit'])->name('deposit');
        Route::post('/deposit', [WalletController::class, 'depositStore'])->name('deposit.store');
    });

    // Reviews
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('index');
        Route::get('/create', [ReviewController::class, 'create'])->name('create');
        Route::post('/store', [ReviewController::class, 'store'])->name('store');
        Route::get('/{review}', [ReviewController::class, 'show'])->name('show');
    });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::post('/update', [ProfileController::class, 'update'])->name('update');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/password', [SettingController::class, 'updatePassword'])->name('password.update');
        Route::post('/notifications', [SettingController::class, 'updateNotifications'])->name('notifications.update');
        Route::post('/privacy', [SettingController::class, 'updatePrivacy'])->name('privacy.update');
        Route::delete('/device/{id}', [SettingController::class, 'removeDevice'])->name('devices.remove');
    });

    // Chat
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::post('/open', [ChatController::class, 'openConversation'])->name('open');
        Route::get('/messages/{conversation}', [ChatController::class, 'messages'])->name('messages');
        Route::post('/send', [ChatController::class, 'send'])->name('send');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/read/{id}', [NotificationController::class, 'markAsRead'])->name('read');
    });

});
