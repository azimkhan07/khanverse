<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__ . '/admin.php';
require __DIR__ . '/seller.php';
require __DIR__ . '/buyer.php';
require __DIR__ . '/auth.php';

Route::get('/', function () {
    return view('frontend.home');
});

Route::post('/logout', function () {

    Auth::logout();

    request()->session()->invalidate();

    request()->session()->regenerateToken();

    return redirect('/login');
})->name('logout');

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
