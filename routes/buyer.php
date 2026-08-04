<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Buyer\OrderController;
use App\Http\Controllers\Buyer\ReviewController;
use App\Http\Controllers\Buyer\WalletController;
use App\Http\Controllers\Buyer\ProfileController;
use App\Http\Controllers\Buyer\SettingController;
use App\Http\Controllers\Buyer\DashboardController;
use App\Http\Controllers\Buyer\ProjectController;
use App\Http\Controllers\Buyer\NotificationController;
use App\Http\Controllers\ChatController as ControllersChatController;

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

        Route::get('projects/attachments/{attachment}', [ProjectController::class, 'Attachments'])->name('attachments');
        Route::get('projects/attachments/{attachment}/download', [ProjectController::class, 'downloadAttachment'])->name('attachments.download');
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
        Route::get('/create/{order}', [ReviewController::class, 'create'])->name('create');
        Route::post('/store', [ReviewController::class, 'store'])->name('store');
        Route::get('/{review}', [ReviewController::class, 'show'])->name('show');
    });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::post('/update', [ProfileController::class, 'updateProfile'])->name('update');
        Route::post('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::post('/photo', [ProfileController::class, 'updatePhoto'])->name('photo');
        Route::delete('/photo', [ProfileController::class, 'deletePhoto'])->name('photo.delete');
        Route::get('/states/{country}', [ProfileController::class, 'getStates'])->name('states');
        Route::get('/cities/{state}', [ProfileController::class, 'getCities'])->name('cities');
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
        Route::get('/messages/{conversation}', [ChatController::class, 'loadMessages'])->name('messages');
        Route::post('/send', [ChatController::class, 'sendMessage'])->name('send');
        Route::post('/seen/{conversation}', [ChatController::class, 'markAsSeen'])->name('seen');
        Route::delete('/message/{message}', [ChatController::class, 'deleteMessage'])->name('delete');
        Route::get('/{conversation}', [ChatController::class, 'show'])->name('show');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/latest', [NotificationController::class, 'latest'])->name('latest');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('count');
        Route::post('/{id}/read', [NotificationController::class, 'markRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('read.all');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // support index(chat with admin for any qury)
    Route::get('/support', function () {
        return view('seller.support.index');
    })->name('support.index');
});
