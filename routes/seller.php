<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:seller'])->prefix('seller')->name('seller.')->group(function () {

    Route::get('/dashboard', function () {
        return view('seller.dashboard');
    })->name('dashboard');


    
});