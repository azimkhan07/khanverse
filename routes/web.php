<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\InvoiceController;

/*
|--------------------------------------------------------------------------
| Web Routes (Public React SPA)
|--------------------------------------------------------------------------
| Routes below with /admin, /seller, /buyer prefixes are injected by the
| RouteServiceProvider from routes/admin.php, routes/seller.php, routes/buyer.php.
| All public GET routes fall through to the React SPA mount (app.blade.php).
*/

Route::get('/redirect-user', function () {
    $user = Auth::user();
    if ($user->role == 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if ($user->role == 'seller') {
        return redirect()->route('seller.dashboard');
    }
    if ($user->role == 'buyer') {
        return redirect()->route('buyer.dashboard');
    }
    abort(403);
})->middleware('auth');

// Panel index redirects - authenticated users go to their dashboard, guests to login
foreach (['admin', 'seller', 'buyer'] as $panel) {
    Route::get('/'.$panel, function () use ($panel) {
        if (Auth::check()) {
            $role = Auth::user()->role;
            if ($role == $panel) {
                return redirect()->route($panel . '.dashboard');
            }
            if (in_array($role, ['admin', 'seller', 'buyer'])) {
                return redirect()->route($role . '.dashboard');
            }
        }
        return redirect()->route('login');
    });
}

// Auth routes (login, register, forgot-password, etc.) — blade templates
require base_path('routes/auth.php');

// Public React SPA - serve the app for all non-panel public routes

// Public invoice access (scannable barcode opens / downloads the PDF without login)
Route::get('/invoice/{token}', [InvoiceController::class, 'view'])->name('invoice.view');
Route::get('/invoice/{token}/download', [InvoiceController::class, 'download'])->name('invoice.download');

Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!admin|seller|buyer).*');
