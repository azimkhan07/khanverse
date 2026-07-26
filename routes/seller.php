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

//  test
Route::get('/test', function () {
    return "SELLER ROUTE WORKING";
});
