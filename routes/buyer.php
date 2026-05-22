<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:buyer'])->prefix('buyer')->name('buyer.')->group(function () {

    Route::get('/dashboard', function () {
        return view('buyer.dashboard');
    })->name('dashboard');


});
