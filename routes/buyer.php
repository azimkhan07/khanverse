<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Buyer\OrderController;
use App\Http\Controllers\Buyer\ReviewController;
use App\Http\Controllers\Buyer\WalletController;
use App\Http\Controllers\Buyer\ProfileController;
use App\Http\Controllers\Buyer\SettingController;
use App\Http\Controllers\Buyer\ProjectController;

/*
|--------------------------------------------------------------------------
| Buyer Web Routes (React SPA)
|--------------------------------------------------------------------------
| All GET page routes render the React SPA (buyer.dashboard.react).
| Named routes are preserved so backend redirects keep working.
| Functional POST/download routes remain for backend actions.
*/

    // Dashboard
    Route::get('/dashboard', fn () => view('buyer.dashboard.react'))->name('dashboard');

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', fn () => view('buyer.dashboard.react'))->name('index');
        Route::get('/{order}', fn () => view('buyer.dashboard.react'))->name('show');
    });

    // Projects (page routes + functional file downloads)
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', fn () => view('buyer.dashboard.react'))->name('index');
        Route::get('/{project}', fn () => view('buyer.dashboard.react'))->name('show');
        Route::get('attachments/{attachment}/download', [ProjectController::class, 'downloadAttachment'])->name('attachments.download');
    });

    // Wallet
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', fn () => view('buyer.dashboard.react'))->name('index');
        Route::get('/transactions', fn () => view('buyer.dashboard.react'))->name('transactions');
        Route::get('/deposit', fn () => view('buyer.dashboard.react'))->name('deposit');
        Route::post('/deposit', [WalletController::class, 'depositStore'])->name('deposit.store');
    });

    // Reviews
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', fn () => view('buyer.dashboard.react'))->name('index');
        Route::get('/create/{order}', fn () => view('buyer.dashboard.react'))->name('create');
        Route::post('/store', [ReviewController::class, 'store'])->name('store');
        Route::get('/{review}', fn () => view('buyer.dashboard.react'))->name('show');
    });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', fn () => view('buyer.dashboard.react'))->name('index');
        Route::post('/update', [ProfileController::class, 'updateProfile'])->name('update');
        Route::post('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::post('/photo', [ProfileController::class, 'updatePhoto'])->name('photo');
        Route::delete('/photo', [ProfileController::class, 'deletePhoto'])->name('photo.delete');
        Route::get('/states/{country}', [ProfileController::class, 'getStates'])->name('states');
        Route::get('/cities/{state}', [ProfileController::class, 'getCities'])->name('cities');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', fn () => view('buyer.dashboard.react'))->name('index');
        Route::post('/password', [SettingController::class, 'updatePassword'])->name('password.update');
        Route::post('/notifications', [SettingController::class, 'updateNotifications'])->name('notifications.update');
        Route::post('/privacy', [SettingController::class, 'updatePrivacy'])->name('privacy.update');
        Route::delete('/device/{id}', [SettingController::class, 'removeDevice'])->name('devices.remove');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', fn () => view('buyer.dashboard.react'))->name('index');
        Route::get('/latest', fn () => view('buyer.dashboard.react'))->name('latest');
        Route::get('/unread-count', fn () => view('buyer.dashboard.react'))->name('count');
        Route::post('/{id}/read', fn () => response()->json(['success' => true]))->name('read');
        Route::post('/read-all', fn () => response()->json(['success' => true]))->name('read.all');
        Route::delete('/{id}', fn () => response()->json(['success' => true]))->name('destroy');
    });

    // Support (chat with admin) -> React
    Route::get('/support', fn () => view('buyer.dashboard.react'))->name('support.index');

Route::get('/{any}', fn () => view('buyer.dashboard.react'))->where('any', '.*');
