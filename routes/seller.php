<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Seller\ProjectController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\ServiceController;
use App\Http\Controllers\Seller\WalletController;
use App\Http\Controllers\Seller\NotificationController;
use App\Http\Controllers\Seller\SettingController;

/*
|--------------------------------------------------------------------------
| Seller Web Routes (React SPA)
|--------------------------------------------------------------------------
| All GET page routes render the React SPA (seller.dashboard.react).
| Functional POST/download routes remain for backend actions.
*/

    // Dashboard
    Route::get('/dashboard', fn () => view('seller.dashboard.react'))->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Projects
    |--------------------------------------------------------------------------
    */
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', fn () => view('seller.dashboard.react'))->name('index');
        Route::get('/{id}', fn () => view('seller.dashboard.react'))->name('show');
        Route::get('/{id}/attachments', fn () => view('seller.dashboard.react'))->name('attachments');
        Route::post('/{id}/status', [ProjectController::class, 'changeStatus'])->name('status');
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
        Route::get('/', fn () => view('seller.dashboard.react'))->name('index');
        Route::get('/{id}', fn () => view('seller.dashboard.react'))->name('show');
        Route::post('/{id}/status', [OrderController::class, 'changeStatus'])->name('status');
        Route::post('/{id}/complete', [OrderController::class, 'complete'])->name('complete');
        Route::post('/{id}/revision', [OrderController::class, 'requestRevision'])->name('revision');
        Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Services
    |--------------------------------------------------------------------------
    */
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', fn () => view('seller.dashboard.react'))->name('index');
        Route::get('/create', fn () => view('seller.dashboard.react'))->name('create');
        Route::get('/{service}/show', fn () => view('seller.dashboard.react'))->name('show');
        Route::get('/{service}/edit', fn () => view('seller.dashboard.react'))->name('edit');
        Route::post('/store', [ServiceController::class, 'store'])->name('store');
        Route::post('/{service}/update', [ServiceController::class, 'update'])->name('update');
        Route::delete('/{service}/delete', [ServiceController::class, 'destroy'])->name('delete');
        Route::get('/{service}/gallery', fn () => view('seller.dashboard.react'))->name('gallery');
        Route::post('/{service}/gallery/store', [ServiceController::class, 'galleryStore'])->name('gallery.store');
        Route::delete('/gallery/{image}/delete', [ServiceController::class, 'galleryDelete'])->name('gallery.delete');
    });

    /*
    |--------------------------------------------------------------------------
    | Wallet
    |--------------------------------------------------------------------------
    */
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', fn () => view('seller.dashboard.react'))->name('index');
        Route::get('/transactions', fn () => view('seller.dashboard.react'))->name('transactions');
        Route::get('/transaction/{id}', fn () => view('seller.dashboard.react'))->name('transaction.show');
        Route::get('/withdraw', fn () => view('seller.dashboard.react'))->name('withdraw.form');
        Route::post('/withdraw', [WalletController::class, 'withdrawRequest'])->name('withdraw.request');
        Route::get('/withdraw-history', fn () => view('seller.dashboard.react'))->name('withdraw.history');
    });

    /*
    |--------------------------------------------------------------------------
    | Reviews
    |--------------------------------------------------------------------------
    */
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', fn () => view('seller.dashboard.react'))->name('index');
        Route::get('/{review}', fn () => view('seller.dashboard.react'))->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', fn () => view('seller.dashboard.react'))->name('index');
        Route::get('/latest', fn () => view('seller.dashboard.react'))->name('latest');
        Route::get('/unread-count', fn () => view('seller.dashboard.react'))->name('count');
        Route::post('/{id}/read', fn () => response()->json(['success' => true]))->name('read');
        Route::post('/read-all', fn () => response()->json(['success' => true]))->name('read.all');
        Route::delete('/{id}', fn () => response()->json(['success' => true]))->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', fn () => view('seller.dashboard.react'))->name('index');
    });

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', fn () => view('seller.dashboard.react'))->name('index');
        Route::post('/password', [SettingController::class, 'updatePassword'])->name('password.update');
        Route::post('/notifications', [SettingController::class, 'updateNotifications'])->name('notifications.update');
        Route::delete('/devices/{id}', [SettingController::class, 'removeDevice'])->name('devices.remove');
        Route::post('/privacy', [SettingController::class, 'updatePrivacy'])->name('privacy.update');
        Route::post('/delete-account', [SettingController::class, 'destroyAccount'])->name('destroy');
    });

    // support -> React
    Route::get('/support', fn () => view('seller.dashboard.react'))->name('seller.support.index');

// All other seller pages -> React SPA
Route::get('/{any}', fn () => view('seller.dashboard.react'))->where('any', '.*');
