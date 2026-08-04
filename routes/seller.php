<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\ProjectController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\ServiceController;
use App\Http\Controllers\Seller\WalletController;
use App\Http\Controllers\Seller\ReviewController;
use App\Http\Controllers\Seller\NotificationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Seller\ProfileController;
use App\Http\Controllers\Seller\SettingController;

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

Route::prefix('projects')->name('projects.')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('index');
    Route::get('/{id}', [ProjectController::class, 'show'])->name('show');
    Route::post('/{id}/status', [ProjectController::class, 'changeStatus'])->name('status');
    Route::get('/{id}/attachments', [ProjectController::class, 'attachments'])->name('attachments');
    Route::post('/{id}/attachments/upload', [ProjectController::class, 'uploadAttachment'])->name('attachments.upload');
    Route::get('/attachment/{id}/download', [ProjectController::class, 'downloadAttachment'])->name('attachments.download');
    Route::delete('/attachment/{id}', [ProjectController::class, 'deleteAttachment'])->name('attachments.delete');
});

/*
|--------------------------------------------------------------------------
| Orders
|--------------------------------------------------------------------------
*/

Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/{id}', [OrderController::class, 'show'])->name('show');
    Route::post('/{id}/status', [OrderController::class, 'changeStatus'])->name('status');
    Route::post('/{id}/complete', [OrderController::class, 'complete'])->name('complete');
    Route::post('/{id}/revision', [OrderController::class, 'requestRevision'])->name('revision');
    Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');

    // for future
    Route::post('/{id}/deliver', [OrderController::class, 'deliver'])->name('deliver');
    Route::get('/{id}/download', [OrderController::class, 'downloadDelivery'])->name('download');
});

/*
|--------------------------------------------------------------------------
| Services
|--------------------------------------------------------------------------
*/

Route::prefix('services')->name('services.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('/create', [ServiceController::class, 'create'])->name('create');
    Route::post('/store', [ServiceController::class, 'store'])->name('store');
    Route::get('/{service}/show', [ServiceController::class, 'show'])->name('show');
    Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('edit');
    Route::post('/{service}/update', [ServiceController::class, 'update'])->name('update');
    Route::delete('/{service}/delete', [ServiceController::class, 'destroy'])->name('delete');

    // Gallery

    Route::get('/{service}/gallery', [ServiceController::class, 'gallery'])->name('gallery');
    Route::post('/{service}/gallery/store', [ServiceController::class, 'galleryStore'])->name('gallery.store');
    Route::delete('/gallery/{image}/delete', [ServiceController::class, 'galleryDelete'])->name('gallery.delete');
});


// Seller Walet
Route::prefix('wallet')->name('wallet.')->group(function () {
    Route::get('/', [WalletController::class, 'index'])->name('index');

    Route::get('/transactions', [WalletController::class, 'transactions'])->name('transactions');
    Route::get('/transaction/{id}', [WalletController::class, 'showTransaction'])->name('transaction.show');

    Route::get('/withdraw', [WalletController::class, 'withdrawForm'])->name('withdraw.form');
    Route::post('/withdraw', [WalletController::class, 'withdrawRequest'])->name('withdraw.request');
    Route::get('/withdraw-history', [WalletController::class, 'withdrawHistory'])->name('withdraw.history');
});

// Review
Route::prefix('reviews')->name('reviews.')->group(function () {
    Route::get('/', [ReviewController::class, 'index'])->name('index');
    Route::get('/{review}', [ReviewController::class, 'show'])->name('show');
});

/*
        |--------------------------------------------------------------------------
        | Notifications
        |--------------------------------------------------------------------------
        */

Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/latest', [NotificationController::class, 'latest'])->name('latest');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('count');
    Route::post('/{id}/read', [NotificationController::class, 'markRead'])->name('read');
    Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('read.all');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
});

/*
|--------------------------------------------------------------------------
| Seller Chat
|--------------------------------------------------------------------------
*/

Route::prefix('chat')->name('chat.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Conversation
    |--------------------------------------------------------------------------
    */

    Route::get('/{conversation}', [ChatController::class, 'show'])->name('show');
    Route::get('/{conversation}/messages', [ChatController::class, 'loadMessages'])->name('messages');
    Route::post('/seen/{conversation}', [ChatController::class, 'markAsSeen'])->name('seen');

    /*
    |--------------------------------------------------------------------------
    | Message
    |--------------------------------------------------------------------------
    */

    Route::post('/send', [ChatController::class, 'sendMessage'])->name('send');
    Route::post('/{conversation}/seen', [ChatController::class, 'markAsSeen'])->name('seen');
    Route::delete('/message/{message}', [ChatController::class, 'deleteMessage'])->name('delete');

    Route::post('/open', [ChatController::class, 'openConversation'])->name('open');
});

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

Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingController::class, 'index'])->name('index');
    Route::post('/password', [SettingController::class, 'updatePassword'])->name('password.update');
    Route::post('/notifications', [SettingController::class, 'updateNotifications'])->name('notifications.update');
    Route::delete('/devices/{id}', [SettingController::class, 'removeDevice'])->name('devices.remove');
    Route::post('/privacy', [SettingController::class, 'updatePrivacy'])->name('privacy.update');
    Route::post('/delete-account', [SettingController::class, 'destroyAccount'])->name('destroy');
});

// support index(chat with admin for any qury)
Route::get('/support', function () {
    return view('seller.support.index');
})->name('seller.support.index');
//  test
Route::get('/test', function () {
    return "SELLER ROUTE WORKING";
});
