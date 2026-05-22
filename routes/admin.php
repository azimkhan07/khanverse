<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {

        Route::get('/', function () {
            return "Orders List";
        })->name('index');

        Route::get('/create', function () {
            return "Create Order";
        })->name('create');
    });

    // PROJECTS
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', fn() => "Projects List")->name('index');
    });

    // SERVICES
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', fn() => "Services List")->name('index');
    });

    // CATEGORIES
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', fn() => "Categories List")->name('index');
    });

    // USERS (Buyers + Sellers)
    Route::prefix('users')->name('users.')->group(function () {

        Route::get('/buyers', fn() => "Buyers List")->name('buyers.index');

        Route::get('/sellers', fn() => "Sellers List")->name('sellers.index');
    });

    Route::prefix('reports')->name('reports.')->group(function () {

        Route::get('/invoices', fn() => "Invoices Report")->name('invoices');

        Route::get('/sales', fn() => "Sales Report")->name('sales');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {

        Route::get('/admin', [SettingController::class, 'admin'])->name('admin');
        Route::get('/seller', [SettingController::class, 'seller'])->name('seller');
        Route::get('/buyer', [SettingController::class, 'buyer'])->name('buyer');
        Route::get('/frontend', [SettingController::class, 'frontend'])->name('frontend');
        Route::get('/auth', [SettingController::class, 'auth'])->name('auth');

        //CRUD
        Route::get('/create/{group}', [SettingController::class, 'create'])->name('create');
        Route::get('/edit/{id}', [SettingController::class, 'edit'])->name('edit');

        Route::post('/store', [SettingController::class, 'store'])->name('store');
        Route::post('/update/{id}', [SettingController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [SettingController::class, 'destroy'])->name('delete');
    });

    Route::resource('menu', MenuController::class)->names('menu')->middleware(['auth', 'role:admin']);

    Route::prefix('menu')->name('menu.')->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::get('/create', [MenuController::class, 'create'])->name('create');
        Route::post('/store', [MenuController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [MenuController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [MenuController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [MenuController::class, 'destroy'])->name('destroy');
    });

    Route::resource('roles', RoleController::class);

    Route::resource('permissions', PermissionController::class);

    Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');

    Route::post('roles/{role}/permissions',[RoleController::class, 'assignPermissions'])->name('roles.permissions.store');

    Route::get('/test-permission', function () {

        return "Permission Working";
    })->middleware('permission:users.view');
});
